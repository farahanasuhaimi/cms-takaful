<?php

namespace App\Services;

use App\Models\Client;
use App\Models\DailyPost;
use App\Models\Lead;
use App\Models\Policy;
use App\Models\Quotation;
use App\Models\Touchpoint;
use Illuminate\Support\Collection;

/**
 * Turns the dashboard's raw counts into a few prioritised, plain-language
 * observations ("7 of 9 policies are Medical Card — you haven't quoted Hibah
 * in 90 days"). Rule-based on purpose: every sentence traces back to a query.
 */
class DashboardInsightService
{
    // Product lines worth analysing. Keys match policies.plan_type.
    public const LINES = [
        'medical'           => 'Medical Card',
        'critical_illness'  => 'Critical Illness',
        'hibah'             => 'Hibah',
        'personal_accident' => 'Personal Accident',
    ];

    // Free-text matching for quotation categories, lead interests and post topics.
    public const KEYWORDS = [
        'medical'           => '/medical|\bmc\b|kad perubatan|mediflex|health ?360|idaman|hospital/i',
        'critical_illness'  => '/critical|\bci\b|kritikal|penyakit|gaji|cancer|kanser|c-word/i',
        'hibah'             => '/hibah|legasi|sejuta makna|\blife\b|faraid|wasiat/i',
        'personal_accident' => '/personal accident|\bpa\b|kemalangan|accident/i',
    ];

    private const WINDOW_DAYS = 90;

    /** @return array{insights: Collection, lines: Collection} */
    public function build(): array
    {
        $since = now()->subDays(self::WINDOW_DAYS);
        $userId = auth()->id();

        $policies = Policy::get(['id', 'client_id', 'plan_type', 'created_at']);
        $clients  = Client::count();

        // Quotations carry no global user scope — filter explicitly.
        $quotes = Quotation::where('user_id', $userId)
            ->where('created_at', '>=', $since)
            ->with('plans:id,quotation_id,category,plan_name', 'plans.premiums')
            ->get();

        $posts = DailyPost::with('planProduct:id,plan_type')
            ->where('created_at', '>=', $since)
            ->get();

        $openLeads = Lead::whereNull('converted_at')->get();

        // Per-line activity: what's sold vs what's being worked on.
        $lines = collect(self::LINES)->map(function ($label, $key) use ($policies, $quotes, $posts, $openLeads) {
            return [
                'key'      => $key,
                'label'    => $label,
                'policies' => $policies->where('plan_type', $key)->count(),
                'quotes'   => $quotes->sum(fn ($q) => $this->proposalsFor($q, $key)),
                'posts'    => $posts->filter(fn ($p) => $p->planProduct?->plan_type === $key
                    || $this->matches($key, $p->topic))->count(),
                'leads'    => $openLeads->filter(fn ($l) => $this->matches($key, $l->interest_area))->count(),
            ];
        });

        $insights = collect();

        $this->portfolioMix($insights, $lines, $policies->count());
        $this->crossSellGap($insights, $policies);
        $this->neglectedLines($insights, $lines);
        $this->hotLeadsWithoutQuote($insights, $openLeads, $userId);
        $this->staleLeads($insights, $openLeads);
        $this->overdueFollowUps($insights);
        $this->momentum($insights, $policies, $clients);

        return [
            'insights' => $insights->sortBy('priority')->take(5)->values(),
            'lines'    => $lines->values(),
        ];
    }

    // ── Rules ──────────────────────────────────────────────────────────────

    private function portfolioMix(Collection $out, Collection $lines, int $total): void
    {
        if ($total < 3) {
            return;
        }

        $top = $lines->sortByDesc('policies')->first();
        $share = $top['policies'] / $total;

        if ($share < 0.6) {
            return;
        }

        $thinLines = $lines->where('key', '!=', $top['key'])
            ->filter(fn ($l) => $l['policies'] === 0);
        $thin = $thinLines->pluck('label');

        $out->push($this->insight(
            priority: 20,
            tone: 'opportunity',
            title: "Your book leans on {$top['label']}",
            body: "{$top['policies']} of your {$total} policies (" . round($share * 100) . "%) are {$top['label']}."
                . ($thin->isNotEmpty() ? ' You have no ' . $this->join($thin, 'or') . ' policies yet. Those are the easiest gaps to open, starting with people who already trust you.' : ''),
            action: ['Start a quotation', route('quotations.create')],
            play: ['lines' => $thinLines->keys()->all(), 'categories' => ['prospecting', 'content', 'referral']],
        ));
    }

    private function crossSellGap(Collection $out, Collection $policies): void
    {
        $byClient = $policies->groupBy('client_id')->map(fn ($p) => $p->pluck('plan_type')->unique());

        $medicalOnlyIds = $byClient->filter(fn ($types) => $types->contains('medical')
            && ! $types->contains('critical_illness')
            && ! $types->contains('hibah'))->keys();
        $medicalOnly = $medicalOnlyIds->count();

        if ($medicalOnly === 0) {
            return;
        }

        $out->push($this->insight(
            priority: 10,
            tone: 'opportunity',
            title: 'Cross-sell to your Medical Card holders',
            body: "{$medicalOnly} " . str('client')->plural($medicalOnly) . " " . ($medicalOnly === 1 ? 'has' : 'have')
                . ' Medical Card but no CI or Hibah. Medical Card pays the hospital, not the bills at home. An income-replacement (CI) or Hibah conversation is the natural next step.',
            action: ['View policyholders', route('clients.index')],
            play: ['lines' => ['critical_illness', 'hibah'], 'categories' => ['objection_handling', 'referral'], 'clientIds' => $medicalOnlyIds->all()],
        ));
    }

    private function neglectedLines(Collection $out, Collection $lines): void
    {
        // Only flag the core lines, and only when nothing at all happened on them.
        $idle = $lines->only(['critical_illness', 'hibah', 'medical'])
            ->filter(fn ($l) => $l['quotes'] === 0 && $l['posts'] === 0);

        // A brand-new account has nothing to compare against — don't nag.
        $anyActivity = $lines->sum(fn ($l) => $l['policies'] + $l['quotes'] + $l['posts']) > 0;
        if ($idle->isEmpty() || ! $anyActivity) {
            return;
        }

        $busy = $lines->sortByDesc(fn ($l) => $l['quotes'] + $l['posts'])->first();
        $busyNote = ($busy['quotes'] + $busy['posts']) > 0 && ! $idle->has($busy['key'])
            ? " Most of your recent effort went to {$busy['label']} ({$busy['quotes']} " . str('proposal')->plural($busy['quotes']) . ", {$busy['posts']} " . str('post')->plural($busy['posts']) . ').'
            : '';

        $out->push($this->insight(
            priority: 15,
            tone: 'warning',
            title: 'No promotion on ' . $this->join($idle->pluck('label')),
            body: 'No quotations or content about ' . $this->join($idle->pluck('label')) . ' in the last ' . self::WINDOW_DAYS . ' days.'
                . $busyNote . ' A product nobody hears about doesn\'t get bought.',
            action: ['Plan a post', route('daily-posts.index')],
            play: ['lines' => $idle->keys()->all(), 'categories' => ['content', 'prospecting']],
        ));
    }

    private function hotLeadsWithoutQuote(Collection $out, Collection $openLeads, int $userId): void
    {
        $hot = $openLeads->where('temperature', 'hot');
        if ($hot->isEmpty()) {
            return;
        }

        $quoted = Quotation::where('user_id', $userId)->whereIn('lead_id', $hot->pluck('id'))->pluck('lead_id')->unique();
        $unquoted = $hot->whereNotIn('id', $quoted);

        if ($unquoted->isEmpty()) {
            return;
        }

        $n = $unquoted->count();
        $out->push($this->insight(
            priority: 5,
            tone: 'warning',
            title: "{$n} hot " . str('lead')->plural($n) . ' with no quotation',
            body: ($n === 1 ? 'A lead you marked hot has' : "{$n} leads you marked hot have")
                . ' never received a quotation. Hot leads cool fast, so a concrete number is usually what moves them.',
            action: ['Open leads', route('leads.index')],
            play: ['categories' => ['follow_up', 'closing'], 'leadIds' => $unquoted->pluck('id')->all()],
        ));
    }

    private function staleLeads(Collection $out, Collection $openLeads): void
    {
        if ($openLeads->isEmpty()) {
            return;
        }

        $recent = Touchpoint::where('touchable_type', Lead::class)
            ->where('contacted_at', '>=', now()->subDays(14))
            ->pluck('touchable_id')->unique();

        // A Next Contact date of today or later means the lead is scheduled, not forgotten.
        // A past date doesn't count, so a missed one brings the lead back.
        $staleIds = $openLeads->whereNotIn('id', $recent)
            ->filter(fn ($l) => $l->created_at < now()->subDays(14))
            ->reject(fn ($l) => $l->next_contact && $l->next_contact->gte(today()))
            ->pluck('id');
        $stale = $staleIds->count();

        if ($stale === 0) {
            return;
        }

        $out->push($this->insight(
            priority: 30,
            tone: 'warning',
            title: "{$stale} " . str('lead')->plural($stale) . ' gone quiet',
            body: "{$stale} of your {$openLeads->count()} open " . str('lead')->plural($openLeads->count())
                . ' had no contact in the last 14 days. A short check-in keeps them from going cold.',
            action: ['Open leads', route('leads.index')],
            play: ['categories' => ['follow_up'], 'leadIds' => $staleIds->all()],
        ));
    }

    private function overdueFollowUps(Collection $out): void
    {
        $n = Touchpoint::overdueFollowUps()->count();

        if ($n === 0) {
            return;
        }

        $out->push($this->insight(
            priority: 8,
            tone: 'warning',
            title: "{$n} overdue " . str('follow-up')->plural($n),
            body: "You planned {$n} " . str('follow-up')->plural($n) . ' that ' . ($n === 1 ? 'is' : 'are')
                . ' now past due. Each one is someone expecting to hear from you.',
            action: ['See follow-ups', route('touchpoints.index')],
            play: ['categories' => ['follow_up']],
        ));
    }

    private function momentum(Collection $out, Collection $policies, int $clients): void
    {
        if ($policies->isEmpty()) {
            return;
        }

        $last30 = $policies->where('created_at', '>=', now()->subDays(30))->count();
        $prev30 = $policies->whereBetween('created_at', [now()->subDays(60), now()->subDays(30)])->count();

        if ($last30 > $prev30 && $last30 > 0) {
            $out->push($this->insight(
                priority: 40,
                tone: 'good',
                title: 'Sales are picking up',
                body: "{$last30} new " . str('policy')->plural($last30) . " in the last 30 days, up from {$prev30} the month before. Whatever you changed recently is working.",
            ));
        } elseif ($last30 === 0 && $clients > 0) {
            $out->push($this->insight(
                priority: 35,
                tone: 'warning',
                title: 'No new policies in 30 days',
                body: 'Nothing new was recorded this month'
                    . ($prev30 > 0 ? " (you had {$prev30} the month before)." : '.')
                    . ' If you did close something, record it so this stays accurate.',
                action: ['View policyholders', route('clients.index')],
            ));
        }
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    /**
     * Proposals a quotation makes for one product line: each person quoted a premium
     * on a plan in that line counts once. One quotation for 7 people = 7 proposals.
     * A matching quotation with no premiums filled in yet still counts as 1.
     */
    private function proposalsFor(Quotation $q, string $line): int
    {
        $plans = $q->plans->filter(fn ($p) => $this->matches($line, "{$p->category} {$p->plan_name}"));

        if ($plans->isEmpty()) {
            return $this->matches($line, $q->title) ? 1 : 0;
        }

        $people = $plans->flatMap->premiums
            ->filter(fn ($pr) => (float) $pr->amount > 0)
            ->pluck('quotation_person_id')
            ->unique()
            ->count();

        return max($people, 1);
    }

    public static function matches(string $line, ?string $text): bool
    {
        return $text !== null && $text !== '' && preg_match(self::KEYWORDS[$line], $text) === 1;
    }

    private function join(Collection $labels, string $last = 'and'): string
    {
        $labels = $labels->values();

        return $labels->count() <= 1
            ? (string) $labels->first()
            : $labels->slice(0, -1)->implode(', ') . " {$last} " . $labels->last();
    }

    /**
     * $play is a hint for Today's Play: strategy 'categories' and product 'lines'
     * that would address this insight, plus the 'leadIds'/'clientIds' it is about.
     */
    private function insight(int $priority, string $tone, string $title, string $body, ?array $action = null, ?array $play = null): array
    {
        return compact('priority', 'tone', 'title', 'body', 'action', 'play');
    }
}
