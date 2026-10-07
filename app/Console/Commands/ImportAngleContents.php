<?php

namespace App\Console\Commands;

use App\Models\AngleContent;
use App\Models\ReachAngle;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportAngleContents extends Command
{
    protected $signature = 'angles:import-content
        {--email= : Email of the user who owns the angles}
        {--dry-run : Show what would be added without writing}';

    protected $description = 'Add the hand-written 2026-10 casual/story/factual content to each Reach Angle as a new batch';

    private const MODEL_LABEL = 'Timothy (hand-written, no API)';

    public function handle(): int
    {
        $email = $this->option('email');
        $user  = $email
            ? User::where('email', $email)->firstOrFail()
            : User::orderBy('id')->firstOrFail();

        $library = require database_path('data/angle-contents-2026-10.php');

        $plan = [];
        foreach ($library as $id => $entry) {
            $angle = ReachAngle::withoutGlobalScopes()->find($id);

            $skip = match (true) {
                ! $angle                                  => 'not found',
                $angle->user_id !== $user->id             => 'belongs to another user',
                $angle->title !== $entry['match_title']   => "title changed since the snapshot (\"{$angle->title}\")",
                AngleContent::withoutGlobalScopes()->where('angle_id', $id)
                    ->where('model', self::MODEL_LABEL)->exists() => 'already imported',
                default                                   => null,
            };

            if ($skip) {
                $this->warn("#{$id} skipped: {$skip}");
                continue;
            }

            $plan[] = [$angle, $entry];
            $this->line("#{$id} {$angle->title}: casual, story, factual");
        }

        if ($this->option('dry-run') || ! $plan) {
            $this->info($this->option('dry-run') ? 'Dry run: nothing written.' : 'Nothing to add.');
            return self::SUCCESS;
        }

        DB::transaction(function () use ($plan, $user) {
            foreach ($plan as [$angle, $entry]) {
                $batch = (AngleContent::withoutGlobalScopes()->where('angle_id', $angle->id)->max('batch') ?? 0) + 1;

                foreach (['casual', 'story', 'factual'] as $style) {
                    AngleContent::create([
                        'user_id'   => $user->id,
                        'angle_id'  => $angle->id,
                        'batch'     => $batch,
                        'style'     => $style,
                        'content'   => $entry[$style],
                        'is_pinned' => false,
                        'model'     => self::MODEL_LABEL,
                    ]);
                }
            }
        });

        $this->info('Added a new batch to ' . count($plan) . " angle(s) for {$user->email}. Earlier batches are untouched.");

        return self::SUCCESS;
    }
}
