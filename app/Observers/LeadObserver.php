<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Lead;

class LeadObserver
{
    public function created(Lead $lead): void
    {
        ActivityLog::record('lead.created', "Added lead {$lead->name}", 'lead', $lead->id);
    }

    public function updated(Lead $lead): void
    {
        if ($lead->wasChanged('converted_at') && $lead->converted_at) {
            ActivityLog::record('lead.converted', "Converted {$lead->name} to policyholder", 'lead', $lead->id);
        } else {
            ActivityLog::record('lead.updated', "Updated lead {$lead->name}", 'lead', $lead->id);
        }
    }

    public function deleted(Lead $lead): void
    {
        ActivityLog::record('lead.deleted', "Removed lead {$lead->name}");
    }
}
