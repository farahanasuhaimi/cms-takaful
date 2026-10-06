<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Lead;
use App\Models\Strategy;
use App\Models\Touchpoint;

/**
 * "Today's Play" — puts one strategy in front of the user each day, paired
 * with a real person to use it on, instead of leaving the library to be
 * remembered. Stable for the whole day; never-used strategies come first.
 */
class StrategyPlayService
{
    // strategies.channel → touchpoints.channel
    public const CHANNEL_MAP = [
        'whatsapp'     => 'whatsapp',
        'instagram'    => 'dm_instagram',
        'facebook'     => 'dm_facebook',
        'face_to_face' => 'in_person',
        'general'      => 'other',
    ];

    /**
     * @param  int  $skip  how many times "Show another" was pressed today
     * @return array|null  strategy, script, person, personType, reason, uses, position, total
     */
    public function forToday(int $skip = 0): ?array
    {
        $userId = auth()->id();

        $strategies = Strategy::where(fn ($q) => $q->where('user_id', $userId)->orWhereNull('user_id'))
            ->where('status', 'active')
            ->with('steps')
            ->get();

        if ($strategies->isEmpty()) {
            return null;
        }

        $uses = Touchpoint::whereNotNull('strategy_id')
            ->selectRaw('strategy_id, count(*) as n, max(contacted_at) as last_used')
            ->groupBy('strategy_id')
            ->get()
            ->keyBy('strategy_id');

        // Least-used first; within the same usage count, a per-day shuffle so
        // the pick changes daily but stays put while the page is reloaded.
        $day = now()->toDateString();
        $ordered = $strategies->sortBy([
            fn ($a, $b) => ($uses[$a->id]->n ?? 0) <=> ($uses[$b->id]->n ?? 0),
            fn ($a, $b) => crc32("{$day}:{$a->id}") <=> crc32("{$day}:{$b->id}"),
        ])->values();

        $position = $skip % $ordered->count();
        $strategy = $ordered[$position];

        [$person, $personType, $reason] = $this->pickPerson($strategy);

        return [
            'strategy'   => $strategy,
            'script'     => $this->script($strategy),
            'person'     => $person,
            'personType' => $personType,
            'reason'     => $reason,
            'uses'       => (int) ($uses[$strategy->id]->n ?? 0),
            'lastUsed'   => isset($uses[$strategy->id]) ? \Illuminate\Support\Carbon::parse($uses[$strategy->id]->last_used) : null,
            'channel'    => self::CHANNEL_MAP[$strategy->channel] ?? 'other',
            'position'   => $position + 1,
            'total'      => $ordered->count(),
        ];
    }

    private function script(Strategy $strategy): ?string
    {
        $first = $strategy->steps->first();

        return trim((string) ($first?->script ?: $strategy->content)) ?: null;
    }

    /** @return array{0: Lead|Client|null, 1: ?string, 2: string} */
    private function pickPerson(Strategy $strategy): array
    {
        $wantsLead = in_array($strategy->category, ['follow_up', 'closing'])
            || in_array($strategy->audience, ['warm_leads', 'family_friends']);

        $wantsClient = in_array($strategy->category, ['referral', 'objection_handling'])
            && ! $wantsLead;

        if ($strategy->category === 'content') {
            return [null, null, 'A content play. Post it, then let the replies become leads.'];
        }

        if ($wantsLead) {
            $lead = $this->leadToWork();
            if ($lead) {
                $why = $lead->next_contact && $lead->next_contact->lte(today())
                    ? 'is due for contact' . ($lead->next_contact->lt(today()) ? ' (since ' . $lead->next_contact->format('d M') . ')' : ' today')
                    : 'is your ' . $lead->temperature . ' lead with the longest silence';

                return [$lead, 'lead', ucfirst($lead->temperature) . " lead who {$why}."];
            }
        }

        if ($wantsClient) {
            $client = $this->clientToWork();
            if ($client) {
                return [$client, 'client', 'Policyholder you haven\'t spoken to the longest. Existing clients are the warmest door you have.'];
            }
        }

        return [null, null, $strategy->audience === 'strangers'
            ? 'For reaching new people. Pick one person from your contacts or comments today.'
            : 'No matching person right now. Use it on whoever comes to mind first.'];
    }

    private function leadToWork(): ?Lead
    {
        $lastTouch = $this->lastTouchByPerson(Lead::class);

        return Lead::whereNull('converted_at')->get()
            ->sortBy([
                // due (or overdue) first
                fn ($a, $b) => (int) ! ($a->next_contact?->lte(today())) <=> (int) ! ($b->next_contact?->lte(today())),
                // hot before warm before anything else
                fn ($a, $b) => $this->heat($a) <=> $this->heat($b),
                // longest silence first (never contacted = oldest)
                fn ($a, $b) => ($lastTouch[$a->id] ?? '0') <=> ($lastTouch[$b->id] ?? '0'),
            ])
            ->first();
    }

    private function clientToWork(): ?Client
    {
        $lastTouch = $this->lastTouchByPerson(Client::class);

        return Client::get()
            ->sortBy(fn ($c) => $lastTouch[$c->id] ?? '0')
            ->first();
    }

    private function lastTouchByPerson(string $type): array
    {
        return Touchpoint::where('touchable_type', $type)
            ->selectRaw('touchable_id, max(contacted_at) as last')
            ->groupBy('touchable_id')
            ->pluck('last', 'touchable_id')
            ->all();
    }

    private function heat(Lead $lead): int
    {
        return ['hot' => 0, 'warm' => 1][$lead->temperature] ?? 2;
    }
}
