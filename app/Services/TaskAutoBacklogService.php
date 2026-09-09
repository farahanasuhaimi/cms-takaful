<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\Policy;
use App\Models\Task;
use App\Models\Touchpoint;

class TaskAutoBacklogService
{
    /**
     * Reconcile the user's Backlog against live CRM signals: create a task
     * for anything newly due, and remove tasks whose signal has resolved
     * (renewed, contacted, follow-up cleared) — but only if the user hasn't
     * already dismissed that task (soft-deleted) or moved it off Backlog,
     * since that's read as "already seen, leave it alone".
     */
    public static function sync(int $userId): void
    {
        foreach (self::collectSignals($userId) as $sourceType => $signals) {
            self::reconcile($userId, $sourceType, $signals);
        }
    }

    private static function collectSignals(int $userId): array
    {
        $overdueFollowUps = Touchpoint::with('touchable')
            ->whereNotNull('next_action_date')
            ->whereNotNull('next_action')
            ->where('next_action_date', '<', now()->startOfDay())
            ->get()
            ->mapWithKeys(fn ($tp) => [
                $tp->id => 'Follow up: ' . ($tp->touchable?->name ?? '—') . ' — ' . $tp->next_action,
            ]);

        $cutoff = now()->startOfDay()->addDays(7);
        $renewalsDue = Policy::with('client')
            ->whereNotNull('start_date')
            ->whereNotNull('frequency')
            ->get()
            ->mapWithKeys(function ($policy) use ($cutoff) {
                $renewal = $policy->nextRenewalDate();
                if (! $renewal || $renewal->gt($cutoff) || ! $policy->client) {
                    return [];
                }

                return [$policy->id => 'Renewal due: ' . $policy->client->name . ' — ' . $renewal->format('d M Y')];
            });

        $hotLeadsDue = Lead::where('temperature', 'hot')
            ->whereNull('converted_at')
            ->whereNotNull('next_contact')
            ->where('next_contact', '<=', now()->startOfDay())
            ->get()
            ->mapWithKeys(fn ($lead) => [$lead->id => 'Contact hot lead: ' . $lead->name]);

        return [
            'overdue_followup' => $overdueFollowUps,
            'renewal_due'      => $renewalsDue,
            'hot_lead'         => $hotLeadsDue,
        ];
    }

    private static function reconcile(int $userId, string $sourceType, \Illuminate\Support\Collection $signals): void
    {
        $existing = Task::where('source_type', $sourceType)->get()->keyBy('source_id');

        // Resolved: an active auto-task whose signal no longer applies.
        foreach ($existing as $sourceId => $task) {
            if (! $signals->has($sourceId)) {
                $task->forceDelete();
            }
        }

        // Already dismissed by the user — don't resurrect it.
        $dismissedIds = Task::onlyTrashed()
            ->where('source_type', $sourceType)
            ->pluck('source_id');

        foreach ($signals as $sourceId => $title) {
            if ($existing->has($sourceId) || $dismissedIds->contains($sourceId)) {
                continue;
            }

            $nextPosition = Task::where('status', 'backlog')->max('position') + 1;

            Task::create([
                'user_id'           => $userId,
                'title'             => $title,
                'status'            => 'backlog',
                'position'          => $nextPosition,
                'status_changed_at' => now(),
                'source_type'       => $sourceType,
                'source_id'         => $sourceId,
            ]);
        }
    }
}
