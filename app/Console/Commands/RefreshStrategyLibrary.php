<?php

namespace App\Console\Commands;

use App\Models\Strategy;
use App\Models\StrategyStep;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RefreshStrategyLibrary extends Command
{
    protected $signature = 'strategies:refresh-library
        {--email= : Email of the user who owns the strategies}
        {--dry-run : Show what would change without writing}';

    protected $description = 'Apply the 2026-10 Strategy Library rewrite (prospect angles, facts, cleaned scripts) to a user\'s strategies';

    private const FIELDS = [
        'title', 'description', 'category', 'channel', 'audience', 'content',
        'product_line', 'angle_cold', 'angle_warm', 'angle_hot', 'key_facts',
    ];

    public function handle(): int
    {
        $email = $this->option('email');
        $user  = $email
            ? User::where('email', $email)->firstOrFail()
            : User::orderBy('id')->firstOrFail();

        $library = require database_path('data/strategy-library-2026-10.php');
        $dryRun  = (bool) $this->option('dry-run');

        $plan = [];
        foreach ($library as $id => $entry) {
            $strategy = Strategy::with('steps')->find($id);

            $skip = match (true) {
                ! $strategy                                => 'not found',
                $strategy->user_id !== $user->id           => 'belongs to another user',
                $strategy->status === 'removed'            => 'removed',
                $strategy->title !== $entry['match_title']
                    && $strategy->title !== $entry['title'] => "title changed since the snapshot (\"{$strategy->title}\")",
                default                                    => null,
            };

            if ($skip) {
                $this->warn("#{$id} skipped: {$skip}");
                continue;
            }

            $plan[] = [$strategy, $entry];
            $steps = isset($entry['steps']) ? count($entry['steps']) . ' steps' : 'steps kept';
            $this->line("#{$id} {$strategy->title}  →  {$entry['title']}  ({$steps})");
        }

        if ($dryRun || ! $plan) {
            $this->info($dryRun ? 'Dry run: nothing written.' : 'Nothing to update.');
            return self::SUCCESS;
        }

        // Keep the old wording, so any rewrite can be undone by hand.
        $backup = 'strategy-backups/' . now()->format('Y-m-d_His') . "_user{$user->id}.json";
        Storage::put($backup, json_encode(
            collect($plan)->map(fn ($p) => $p[0]->toArray())->values(),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE,
        ));

        DB::transaction(function () use ($plan) {
            foreach ($plan as [$strategy, $entry]) {
                $strategy->update(collect($entry)->only(self::FIELDS)->all());

                if (isset($entry['steps'])) {
                    $strategy->steps()->delete();
                    foreach (array_values($entry['steps']) as $i => $step) {
                        StrategyStep::create([
                            'strategy_id' => $strategy->id,
                            'step_order'  => $i + 1,
                            'title'       => $step['title'],
                            'script'      => $step['script'],
                            'timing_note' => $step['timing_note'] ?? null,
                            'branch_yes'  => $step['branch_yes'] ?? null,
                            'branch_no'   => $step['branch_no'] ?? null,
                        ]);
                    }
                }
            }
        });

        $this->info('Updated ' . count($plan) . " strateg(ies) for {$user->email}. Old versions saved to " . Storage::path($backup));

        return self::SUCCESS;
    }
}
