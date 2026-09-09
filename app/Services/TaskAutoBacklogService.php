<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Lead;
use App\Models\Task;
use App\Models\Touchpoint;
use Illuminate\Database\Eloquent\Model;

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

    /**
     * Each signal is keyed by source_id => ['title' => ..., 'url' => ...|null].
     * url is null when there's no page to send the user to (e.g. no per-lead
     * show route) — the card just won't be a link in that case.
     */
    private static function collectSignals(int $userId): array
    {
        $overdueFollowUps = Touchpoint::with('touchable')
            ->whereNotNull('next_action_date')
            ->whereNotNull('next_action')
            ->where('next_action_date', '<', now()->startOfDay())
            ->get()
            ->mapWithKeys(fn ($tp) => [
                $tp->id => [
                    'title' => 'Follow up: ' . ($tp->touchable?->name ?? '—') . ' — ' . $tp->next_action,
                    'url'   => self::touchableUrl($tp->touchable),
                ],
            ]);

        $hotLeadsDue = Lead::where('temperature', 'hot')
            ->whereNull('converted_at')
            ->whereNotNull('next_contact')
            ->where('next_contact', '<=', now()->startOfDay())
            ->get()
            ->mapWithKeys(fn ($lead) => [
                $lead->id => [
                    'title' => 'Contact hot lead: ' . $lead->name,
                    'url'   => route('leads.edit', $lead->id),
                ],
            ]);

        $untouchedClients = Client::with(['touchpoints' => fn ($q) => $q->latest('contacted_at')->limit(1)])
            ->get()
            ->mapWithKeys(function ($client) {
                $last = $client->touchpoints->first();

                if (! $last) {
                    return [$client->id => ['title' => 'Check in: ' . $client->name . ' — never contacted', 'url' => route('clients.show', $client->id)]];
                }

                if ($last->next_action_date) {
                    // Has an active plan running — leave it to the
                    // overdue_followup signal if that plan gets missed.
                    if ($last->next_action_date->gte(now()->startOfDay())) {
                        return [];
                    }
                } elseif ($last->contacted_at->diffInDays(now()) < 14) {
                    return [];
                }

                $days = (int) $last->contacted_at->diffInDays(now());

                return [$client->id => [
                    'title' => 'Check in: ' . $client->name . " — no contact in {$days}d",
                    'url'   => route('clients.show', $client->id),
                ]];
            });

        return [
            'overdue_followup' => $overdueFollowUps,
            'hot_lead'         => $hotLeadsDue,
            'untouched_client' => $untouchedClients,
        ];
    }

    private static function touchableUrl(?Model $touchable): ?string
    {
        return match (true) {
            $touchable instanceof Client => route('clients.show', $touchable->id),
            $touchable instanceof Lead   => route('leads.edit', $touchable->id),
            default                      => null,
        };
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

        foreach ($signals as $sourceId => $signal) {
            if ($existing->has($sourceId) || $dismissedIds->contains($sourceId)) {
                continue;
            }

            $nextPosition = Task::where('status', 'backlog')->max('position') + 1;

            Task::create([
                'user_id'           => $userId,
                'title'             => $signal['title'],
                'status'            => 'backlog',
                'position'          => $nextPosition,
                'status_changed_at' => now(),
                'source_type'       => $sourceType,
                'source_id'         => $sourceId,
                'source_url'        => $signal['url'],
            ]);
        }
    }
}
