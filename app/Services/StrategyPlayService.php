<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Lead;
use App\Models\Strategy;
use App\Models\Touchpoint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * "Today's Play" — puts one strategy in front of the user each day, paired
 * with a real person to use it on, instead of leaving the library to be
 * remembered. Strategies that address the dashboard's top insight come
 * first; within that, never-used first; stable for the whole day.
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

    // Which strategy categories suit which kind of person — also drives the
    // "Suggested" group in the touchpoint forms.
    public const CATEGORIES_FOR = [
        'lead'   => ['follow_up', 'closing', 'objection_handling', 'prospecting'],
        'client' => ['referral', 'objection_handling', 'follow_up'],
    ];

    /**
     * @param  int         $skip      how many times "Show another" was pressed today
     * @param  Collection  $insights  from DashboardInsightService, highest priority first
     */
    public function forToday(int $skip = 0, ?Collection $insights = null): ?array
    {
        $strategies = $this->activeStrategies();

        if ($strategies->isEmpty()) {
            return null;
        }

        $uses = $this->usage();
        $insights = ($insights ?? collect())->filter(fn ($i) => ! empty($i['play']))->values();

        // The highest-priority insight each strategy helps with (null = none).
        $because = $strategies->mapWithKeys(fn ($s) => [
            $s->id => $insights->first(fn ($i) => $this->addresses($s, $i['play'])),
        ]);

        $day = now()->toDateString();
        $ordered = $strategies->sortBy([
            fn ($a, $b) => (int) is_null($because[$a->id]) <=> (int) is_null($because[$b->id]),
            fn ($a, $b) => ($uses[$a->id]->n ?? 0) <=> ($uses[$b->id]->n ?? 0),
            fn ($a, $b) => ($because[$a->id]['priority'] ?? 999) <=> ($because[$b->id]['priority'] ?? 999),
            fn ($a, $b) => crc32("{$day}:{$a->id}") <=> crc32("{$day}:{$b->id}"),
        ])->values();

        $position = $skip % $ordered->count();
        $strategy = $ordered[$position];
        $insight  = $because[$strategy->id];

        [$person, $personType, $reason] = $this->pickPerson($strategy, $insight['play'] ?? []);

        return [
            'strategy'   => $strategy,
            'script'     => $this->script($strategy),
            'person'     => $person,
            'personType' => $personType,
            'reason'     => $reason,
            'because'    => $insight ? $insight['title'] : null,
            'uses'       => (int) ($uses[$strategy->id]->n ?? 0),
            'lastUsed'   => isset($uses[$strategy->id]) ? Carbon::parse($uses[$strategy->id]->last_used) : null,
            'channel'    => self::CHANNEL_MAP[$strategy->channel] ?? 'other',
            'position'   => $position + 1,
            'total'      => $ordered->count(),
            'week'       => $this->week(),
        ];
    }

    /**
     * Last 7 days of logged plays (touchpoints that recorded a strategy),
     * plus the run of consecutive days ending today (or yesterday, so the
     * streak doesn't read 0 before today's play is done).
     */
    public function week(): array
    {
        $perDay = Touchpoint::whereNotNull('strategy_id')
            ->where('contacted_at', '>=', today()->subDays(6))
            ->get(['contacted_at'])
            ->countBy(fn ($t) => $t->contacted_at->toDateString());

        $days = collect(range(6, 0))->map(fn ($ago) => today()->subDays($ago))
            ->map(fn ($d) => ['date' => $d, 'count' => $perDay[$d->toDateString()] ?? 0]);

        $streak = 0;
        $cursor = $perDay->has(today()->toDateString()) ? today() : today()->subDay();
        $active = Touchpoint::whereNotNull('strategy_id')
            ->where('contacted_at', '>=', today()->subDays(60))
            ->get(['contacted_at'])
            ->map(fn ($t) => $t->contacted_at->toDateString())
            ->unique()->flip();
        while ($active->has($cursor->toDateString())) {
            $streak++;
            $cursor = $cursor->copy()->subDay();
        }

        return ['days' => $days, 'total' => $days->sum('count'), 'streak' => $streak];
    }

    /**
     * Options for a touchpoint form's strategy dropdown: strategies that suit
     * this kind of person first (never-used first), the rest after.
     *
     * @return array{suggested: Collection, other: Collection}
     */
    public function optionsFor(string $personType): array
    {
        $uses = $this->usage();
        $fits = self::CATEGORIES_FOR[$personType] ?? [];

        // CATEGORIES_FOR is ordered best-fit first.
        $fitRank = fn ($s) => ($i = array_search($s->category, $fits)) === false ? 99 : $i;

        $strategies = $this->activeStrategies(withSteps: false)
            ->each(fn ($s) => $s->uses_count = (int) ($uses[$s->id]->n ?? 0))
            ->sortBy([
                fn ($a, $b) => $fitRank($a) <=> $fitRank($b),
                fn ($a, $b) => $a->uses_count <=> $b->uses_count,
                fn ($a, $b) => strcasecmp($a->title, $b->title),
            ]);

        [$suggested, $other] = $strategies->partition(fn ($s) => in_array($s->category, $fits));

        return ['suggested' => $suggested->values(), 'other' => $other->values()];
    }

    // ── Internals ──────────────────────────────────────────────────────────

    private function activeStrategies(bool $withSteps = true): Collection
    {
        $q = Strategy::where(fn ($q) => $q->where('user_id', auth()->id())->orWhereNull('user_id'))
            ->where('status', 'active');

        return ($withSteps ? $q->with('steps') : $q)->get();
    }

    private function usage(): Collection
    {
        return Touchpoint::whereNotNull('strategy_id')
            ->selectRaw('strategy_id, count(*) as n, max(contacted_at) as last_used')
            ->groupBy('strategy_id')
            ->get()
            ->keyBy('strategy_id');
    }

    /** Does this strategy help with an insight's play hint? */
    private function addresses(Strategy $strategy, array $hint): bool
    {
        $categoryOk = empty($hint['categories']) || in_array($strategy->category, $hint['categories']);

        if (empty($hint['lines'])) {
            return $categoryOk;
        }

        $text = "{$strategy->title} {$strategy->description} {$strategy->content} "
            . $strategy->steps->pluck('script')->implode(' ');

        // A product-specific hint is only met by a strategy about that product.
        return $categoryOk && collect($hint['lines'])->contains(
            fn ($line) => DashboardInsightService::matches($line, $text)
        );
    }

    private function script(Strategy $strategy): ?string
    {
        $first = $strategy->steps->first();

        return trim((string) ($first?->script ?: $strategy->content)) ?: null;
    }

    /** @return array{0: Lead|Client|null, 1: ?string, 2: string} */
    private function pickPerson(Strategy $strategy, array $hint): array
    {
        if ($strategy->category === 'content') {
            return [null, null, 'A content play. Post it, then let the replies become leads.'];
        }

        $wantsLead = in_array($strategy->category, ['follow_up', 'closing'])
            || in_array($strategy->audience, ['warm_leads', 'family_friends']);

        $wantsClient = in_array($strategy->category, ['referral', 'objection_handling']) && ! $wantsLead;

        // The insight's own people come first (e.g. the hot lead with no quote,
        // the Medical-Card-only client) — that is the point of linking them.
        if ($wantsLead || ! empty($hint['leadIds'])) {
            $lead = $this->leadToWork($hint['leadIds'] ?? []);
            if ($lead && ($wantsLead || in_array($lead->id, $hint['leadIds'] ?? []))) {
                return [$lead, 'lead', $this->leadReason($lead)];
            }
        }

        if ($wantsClient || ! empty($hint['clientIds'])) {
            $client = $this->clientToWork($hint['clientIds'] ?? []);
            if ($client) {
                return [$client, 'client', in_array($client->id, $hint['clientIds'] ?? [])
                    ? 'Has Medical Card only, and you haven\'t spoken in the longest. Existing clients are the warmest door you have.'
                    : 'Policyholder you haven\'t spoken to the longest. Existing clients are the warmest door you have.'];
            }
        }

        return [null, null, $strategy->audience === 'strangers'
            ? 'For reaching new people. Pick one person from your contacts or comments today.'
            : 'No matching person right now. Use it on whoever comes to mind first.'];
    }

    private function leadReason(Lead $lead): string
    {
        $why = $lead->next_contact && $lead->next_contact->lte(today())
            ? 'is due for contact' . ($lead->next_contact->lt(today()) ? ' (since ' . $lead->next_contact->format('d M') . ')' : ' today')
            : 'has gone the longest without hearing from you';

        return ucfirst($lead->temperature ?: 'open') . " lead who {$why}.";
    }

    private function leadToWork(array $preferIds = []): ?Lead
    {
        $lastTouch = $this->lastTouchByPerson(Lead::class);

        return Lead::whereNull('converted_at')->get()
            ->sortBy([
                // people the top insight is about first
                fn ($a, $b) => (int) ! in_array($a->id, $preferIds) <=> (int) ! in_array($b->id, $preferIds),
                // due (or overdue) first
                fn ($a, $b) => (int) ! ($a->next_contact?->lte(today())) <=> (int) ! ($b->next_contact?->lte(today())),
                // hot before warm before anything else
                fn ($a, $b) => $this->heat($a) <=> $this->heat($b),
                // longest silence first (never contacted = oldest)
                fn ($a, $b) => ($lastTouch[$a->id] ?? '0') <=> ($lastTouch[$b->id] ?? '0'),
            ])
            ->first();
    }

    private function clientToWork(array $preferIds = []): ?Client
    {
        $lastTouch = $this->lastTouchByPerson(Client::class);

        return Client::get()
            ->sortBy([
                fn ($a, $b) => (int) ! in_array($a->id, $preferIds) <=> (int) ! in_array($b->id, $preferIds),
                fn ($a, $b) => ($lastTouch[$a->id] ?? '0') <=> ($lastTouch[$b->id] ?? '0'),
            ])
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
