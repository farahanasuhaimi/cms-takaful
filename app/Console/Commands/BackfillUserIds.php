<?php

namespace App\Console\Commands;

use App\Models\AngleContent;
use App\Models\Client;
use App\Models\Lead;
use App\Models\PlanProduct;
use App\Models\Policy;
use App\Models\ReachAngle;
use App\Models\Setting;
use App\Models\Touchpoint;
use App\Models\User;
use Illuminate\Console\Command;

class BackfillUserIds extends Command
{
    protected $signature   = 'tenancy:backfill {--email= : Email of the user to assign existing records to}';
    protected $description = 'Assign all null user_id records to the specified user (one-time migration)';

    public function handle(): int
    {
        $email = $this->option('email');
        $user  = $email
            ? User::where('email', $email)->firstOrFail()
            : User::orderBy('id')->firstOrFail();

        $this->info("Assigning all records to: {$user->name} ({$user->email}) [id={$user->id}]");

        $tables = [
            'clients'        => Client::class,
            'leads'          => Lead::class,
            'policies'       => Policy::class,
            'touchpoints'    => Touchpoint::class,
            'reach_angles'   => ReachAngle::class,
            'angle_contents' => AngleContent::class,
            'plan_products'  => PlanProduct::class,
            'settings'       => Setting::class,
        ];

        foreach ($tables as $label => $model) {
            $count = $model::withoutGlobalScopes()->whereNull('user_id')->update(['user_id' => $user->id]);
            $this->line("  {$label}: {$count} rows updated");
        }

        $user->update(['is_admin' => true]);
        $this->info("Admin flag set on {$user->email}");
        $this->info('Backfill complete.');

        return self::SUCCESS;
    }
}
