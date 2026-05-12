<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Policy;

class PolicyObserver
{
    public function created(Policy $policy): void
    {
        $type = ucfirst(str_replace('_', ' ', $policy->plan_type));
        ActivityLog::record('policy.created', "Added {$type} policy for client #{$policy->client_id}", 'policy', $policy->id);
    }

    public function updated(Policy $policy): void
    {
        $type = ucfirst(str_replace('_', ' ', $policy->plan_type));
        ActivityLog::record('policy.updated', "Updated {$type} policy", 'policy', $policy->id);
    }

    public function deleted(Policy $policy): void
    {
        $type = ucfirst(str_replace('_', ' ', $policy->plan_type));
        ActivityLog::record('policy.deleted', "Removed {$type} policy");
    }
}
