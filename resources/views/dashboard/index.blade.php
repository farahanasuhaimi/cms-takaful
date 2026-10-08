<x-app-layout>
    <x-slot name="title">Dashboard · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">Dashboard</x-slot>
    <x-slot name="actions">
        <a href="{{ route('clients.create') }}"
           class="bg-matcha-600 hover:bg-matcha-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            + New Client
        </a>
    </x-slot>

    {{-- Stats row --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 px-4 py-3 sm:px-5 sm:py-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total Policyholders</p>
            <p class="text-2xl sm:text-3xl font-bold text-matcha-800 mt-1">{{ $totalClients }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 px-4 py-3 sm:px-5 sm:py-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Hot Leads</p>
            <p class="text-2xl sm:text-3xl font-bold text-strawberry-600 mt-1">{{ $hotLeads }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 px-4 py-3 sm:px-5 sm:py-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Warm Leads</p>
            <p class="text-2xl sm:text-3xl font-bold text-amber-500 mt-1">{{ $warmLeads }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 px-4 py-3 sm:px-5 sm:py-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Last Outreach</p>
            @if ($recentTouchpoints->count())
                <p class="text-sm font-semibold text-gray-700 mt-2">
                    {{ $recentTouchpoints->first()->contacted_at->format('d M Y') }}
                </p>
            @else
                <p class="text-sm text-gray-400 mt-2">No outreach yet</p>
            @endif
        </div>
        <div class="col-span-2 lg:col-span-1 bg-white rounded-xl border border-gray-200 px-4 py-3 sm:px-5 sm:py-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Est. Commission (Yr 1)</p>
            @if ($totalEstimatedCommission > 0)
                <p class="text-2xl sm:text-3xl font-bold text-amber-600 mt-1">
                    <x-pdpa-mask>RM {{ number_format($totalEstimatedCommission, 0) }}</x-pdpa-mask>
                </p>
            @else
                <p class="text-sm text-gray-400 mt-2">No data yet</p>
            @endif
        </div>
    </div>

    {{-- Today's Play — one strategy a day, matched to a person, see StrategyPlayService --}}
    @if ($play)
        @php $s = $play['strategy']; @endphp
        <div class="bg-gradient-to-br from-matcha-800 to-matcha-600 text-white rounded-xl p-4 sm:p-5 mb-6" x-data="{ copied: false }">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                <p class="text-xs font-semibold uppercase tracking-wider text-matcha-100">Today's Play</p>
                <div class="flex items-center gap-3 text-xs text-matcha-100">
                    <span>{{ $play['position'] }} of {{ $play['total'] }}</span>
                    <a href="{{ route('dashboard', ['play' => request('play', 0) + 1]) }}" class="underline hover:text-white">Show another</a>
                </div>
            </div>

            @if ($play['because'])
                <p class="inline-block mb-2 text-xs font-medium bg-strawberry-600 text-white px-2.5 py-1 rounded-full">
                    Why today: {{ $play['because'] }}
                </p>
            @endif

            <h2 class="text-lg font-semibold leading-snug">{{ $s->title }}</h2>
            <p class="text-xs text-matcha-100 mt-1">
                {{ \App\Models\Strategy::categoryLabel($s->category) }} · {{ \App\Models\Strategy::channelLabel($s->channel) }}
                · {{ $play['uses'] ? 'used ' . $play['uses'] . '× (last ' . $play['lastUsed']->format('d M') . ')' : 'never used yet' }}
            </p>

            <div class="mt-3 bg-white/10 rounded-lg px-4 py-3">
                @if ($play['person'])
                    <p class="text-sm">
                        Use it on
                        <a href="{{ $play['personType'] === 'lead' ? route('leads.edit', $play['person']) : route('clients.show', $play['person']) }}"
                           class="font-semibold underline decoration-strawberry-400 underline-offset-2"><x-pdpa-mask>{{ $play['person']->name }}</x-pdpa-mask></a>
                    </p>
                @endif
                <p class="text-xs text-matcha-100 {{ $play['person'] ? 'mt-0.5' : '' }}">{{ $play['reason'] }}</p>
            </div>

            @if ($play['script'])
                @if ($play['angle'] ?? null)
                    <p class="mt-3 text-xs text-matcha-100">{{ \App\Models\Strategy::TEMPERATURES[$play['angle']] }} angle, written for this lead</p>
                @endif
                <p class="{{ ($play['angle'] ?? null) ? 'mt-1' : 'mt-3' }} text-sm text-white/90 whitespace-pre-line line-clamp-4">{{ $play['script'] }}</p>
            @endif

            <div class="mt-4 flex flex-wrap items-center gap-2">
                @if ($play['script'])
                    <button type="button"
                            @click="navigator.clipboard.writeText({{ json_encode($play['script'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) }}); copied = true; setTimeout(() => copied = false, 2000)"
                            class="bg-white text-matcha-800 text-sm font-medium px-4 py-2 rounded-lg hover:bg-matcha-50 transition">
                        <span x-text="copied ? 'Copied!' : 'Copy script'">Copy script</span>
                    </button>
                @endif

                @if ($play['person'])
                    <form method="POST"
                          action="{{ $play['personType'] === 'lead' ? route('leads.touchpoints.store', $play['person']) : route('clients.touchpoints.store', $play['person']) }}">
                        @csrf
                        <input type="hidden" name="contacted_at" value="{{ now()->toDateString() }}">
                        <input type="hidden" name="channel" value="{{ $play['channel'] }}">
                        <input type="hidden" name="topic" value="{{ \Illuminate\Support\Str::limit($s->title, 250) }}">
                        <input type="hidden" name="strategy_id" value="{{ $s->id }}">
                        <input type="hidden" name="return" value="dashboard">
                        <button type="submit" class="bg-strawberry-600 hover:bg-strawberry-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                            Done, log it
                        </button>
                    </form>
                @elseif ($s->category === 'content')
                    <a href="{{ route('daily-posts.index') }}" class="bg-strawberry-600 hover:bg-strawberry-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition">Make it a post</a>
                @endif

                <a href="{{ route('strategies.show', $s) }}" class="text-sm text-matcha-100 hover:text-white underline ml-1">Full strategy</a>
            </div>

            {{-- Last 7 days of logged plays --}}
            @php $week = $play['week']; @endphp
            <div class="mt-4 pt-3 border-t border-white/15 flex flex-wrap items-center gap-x-4 gap-y-2">
                <div class="flex items-end gap-1.5" aria-label="Plays in the last 7 days">
                    @foreach ($week['days'] as $d)
                        <div class="flex flex-col items-center gap-1" title="{{ $d['date']->format('D d M') }}: {{ $d['count'] }} {{ \Illuminate\Support\Str::plural('play', $d['count']) }}">
                            <span class="w-5 h-5 rounded-md {{ $d['count'] ? 'bg-strawberry-400' : 'bg-white/15' }} {{ $d['date']->isToday() ? 'ring-2 ring-white/70' : '' }}"></span>
                            <span class="text-[10px] text-matcha-100">{{ substr($d['date']->format('D'), 0, 1) }}</span>
                        </div>
                    @endforeach
                </div>
                <p class="text-xs text-matcha-100">
                    <span class="font-semibold text-white">{{ $week['total'] }}</span> {{ \Illuminate\Support\Str::plural('play', $week['total']) }} in the last 7 days
                    @if ($week['streak'] > 1)
                        · <span class="font-semibold text-white">{{ $week['streak'] }}-day streak</span>
                    @elseif ($week['total'] === 0)
                        · log today's to start a streak
                    @endif
                </p>
            </div>
        </div>
    @endif

    {{-- Analysis — what the numbers mean, see DashboardInsightService --}}
    @php
        $toneStyles = [
            'warning'     => ['dot' => 'bg-strawberry-400', 'label' => 'Needs attention', 'text' => 'text-strawberry-600'],
            'opportunity' => ['dot' => 'bg-amber-400',      'label' => 'Opportunity',     'text' => 'text-amber-600'],
            'good'        => ['dot' => 'bg-matcha-400',     'label' => 'Going well',      'text' => 'text-matcha-600'],
        ];
    @endphp
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="text-sm font-semibold text-gray-800 mb-1">What your numbers are saying</h2>
            <p class="text-xs text-gray-400 mb-4">Based on your policies, quotations, posts and leads from the last 90 days.</p>

            @forelse ($insights as $insight)
                @php $tone = $toneStyles[$insight['tone']]; @endphp
                <div class="flex gap-3 py-3 border-t border-gray-100 first-of-type:border-t-0">
                    <span class="mt-1.5 w-2 h-2 rounded-full flex-shrink-0 {{ $tone['dot'] }}"></span>
                    <div class="min-w-0 flex-1">
                        <p class="text-[11px] font-semibold uppercase tracking-wide {{ $tone['text'] }}">{{ $tone['label'] }}</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $insight['title'] }}</p>
                        <p class="text-sm text-gray-600 mt-0.5">{{ $insight['body'] }}</p>
                        @if ($insight['action'])
                            <a href="{{ $insight['action'][1] }}" class="inline-block mt-1.5 text-xs font-medium text-matcha-600 hover:underline">{{ $insight['action'][0] }} →</a>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-400 py-4">Nothing stands out right now. Add policies, quotations and leads, and observations will appear here.</p>
            @endforelse
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="text-sm font-semibold text-gray-800 mb-1">Sold vs promoted</h2>
            <p class="text-xs text-gray-400 mb-3">Policies held, against quotations and posts in the last 90 days.</p>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-[11px] text-gray-400 uppercase tracking-wide">
                            <th class="text-left font-medium pb-2">Product</th>
                            <th class="text-right font-medium pb-2">Policies</th>
                            <th class="text-right font-medium pb-2">Quotes</th>
                            <th class="text-right font-medium pb-2">Posts</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($productLines as $line)
                            <tr>
                                <td class="py-2 text-gray-700">{{ $line['label'] }}</td>
                                <td class="py-2 text-right font-semibold text-matcha-800">{{ $line['policies'] }}</td>
                                <td class="py-2 text-right {{ $line['quotes'] ? 'text-gray-700' : 'text-gray-300' }}">{{ $line['quotes'] }}</td>
                                <td class="py-2 text-right {{ $line['posts'] ? 'text-gray-700' : 'text-gray-300' }}">{{ $line['posts'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Action Required — Renewals + Overdue Follow-ups --}}
    @if ($renewingSoon->count() || $overdueFollowUps->count())
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">

        {{-- Renewals in 7 days --}}
        @if ($renewingSoon->count())
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-amber-800">Renewals This Week</h2>
                <span class="text-xs font-medium bg-amber-200 text-amber-800 px-2 py-0.5 rounded-full">
                    {{ $renewingSoon->count() }} {{ Str::plural('policy', $renewingSoon->count()) }}
                </span>
            </div>
            <ul class="divide-y divide-amber-100">
                @foreach ($renewingSoon as $policy)
                    @php $daysLeft = (int) now()->startOfDay()->diffInDays($policy->computed_renewal, false); @endphp
                    <li class="py-2.5 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2" x-data="{ confirm: false }">
                        <div class="min-w-0">
                            <a href="{{ route('clients.show', $policy->client) }}"
                               class="text-sm font-medium text-gray-800 hover:text-matcha-600">
                                <x-pdpa-mask>{{ $policy->client->name }}</x-pdpa-mask>
                            </a>
                            <p class="text-xs text-gray-500">
                                {{ ucfirst(str_replace('_', ' ', $policy->plan_type)) }}
                                @if ($policy->plan_name) · {{ $policy->plan_name }} @endif
                                · {{ $policy->computed_renewal->format('d M Y') }}
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center gap-3 sm:gap-2 flex-shrink-0">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full whitespace-nowrap
                                {{ $daysLeft <= 3 ? 'bg-strawberry-100 text-strawberry-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $daysLeft === 0 ? 'Today' : $daysLeft . 'd left' }}
                            </span>
                            {{-- Create follow-up touchpoint --}}
                            <form method="POST" action="{{ route('clients.policies.renewal-touchpoint', [$policy->client, $policy]) }}">
                                @csrf
                                <button type="submit" title="Create follow-up touchpoint"
                                        class="text-xs text-indigo-500 hover:text-indigo-700 font-medium transition whitespace-nowrap">
                                    + Follow-up
                                </button>
                            </form>
                            {{-- Mark as renewed --}}
                            <div x-show="!confirm">
                                <button @click="confirm = true"
                                        class="text-xs text-matcha-600 hover:text-matcha-800 font-medium transition whitespace-nowrap">
                                    Renewed ✓
                                </button>
                            </div>
                            <div x-show="confirm" class="flex items-center gap-1">
                                <form method="POST" action="{{ route('clients.policies.renew', [$policy->client, $policy]) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-xs text-matcha-600 font-semibold hover:underline">Yes</button>
                                </form>
                                <button @click="confirm = false" class="text-xs text-gray-400">No</button>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Overdue Follow-ups --}}
        @if ($overdueFollowUps->count())
        <div class="bg-strawberry-50 border border-strawberry-200 rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-strawberry-800">Overdue Follow-ups</h2>
                <span class="text-xs font-medium bg-strawberry-200 text-strawberry-800 px-2 py-0.5 rounded-full">
                    {{ $overdueFollowUps->count() }}
                </span>
            </div>
            <ul class="divide-y divide-strawberry-100">
                @foreach ($overdueFollowUps as $tp)
                    @php $daysOverdue = abs((int) now()->startOfDay()->diffInDays($tp->next_action_date)); @endphp
                    <li class="py-2.5 flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-800"><x-pdpa-mask>{{ $tp->touchable?->name ?? '—' }}</x-pdpa-mask></p>
                            <p class="text-xs text-gray-500 truncate">{{ $tp->next_action }}</p>
                        </div>
                        <span class="text-xs font-semibold bg-strawberry-100 text-strawberry-700 px-2 py-0.5 rounded-full whitespace-nowrap ml-2">
                            {{ $daysOverdue }}d overdue
                        </span>
                    </li>
                @endforeach
            </ul>
        </div>
        @endif

    </div>
    @endif

    {{-- Explore more toggle --}}
    <div class="mb-4" x-data="{ exploreOpen: localStorage.getItem('dashboard_explore_open') === 'true' }"
         x-init="$watch('exploreOpen', v => localStorage.setItem('dashboard_explore_open', v))">
        <button type="button" @click="exploreOpen = !exploreOpen"
                class="w-full flex items-center justify-center gap-2 text-sm font-medium text-matcha-700 bg-matcha-50 hover:bg-matcha-100 border border-matcha-200 rounded-xl py-2.5 transition">
            <span class="px-3 text-center" x-text="exploreOpen ? 'Hide details' : 'Explore more — leads, commission, plans & follow-ups'"></span>
            <svg class="w-4 h-4 transition-transform" :class="exploreOpen ? 'rotate-180' : ''"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

    <div x-show="exploreOpen" x-transition x-cloak>

    {{-- Two-column: Hot Leads + Recent Clients --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">

        {{-- Hot & Warm Leads --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-gray-700">Hot &amp; Warm Leads</h2>
                <a href="{{ route('leads.index') }}" class="text-xs text-matcha-600 hover:underline">View all</a>
            </div>
            @if ($urgentLeads->count())
                <ul class="divide-y divide-gray-100">
                    @foreach ($urgentLeads as $lead)
                        <li class="py-2.5 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-800"><x-pdpa-mask>{{ $lead->name }}</x-pdpa-mask></p>
                                @if ($lead->interest_area)
                                    <p class="text-xs text-gray-400">{{ $lead->interest_area }}</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 ml-2">
                                @if ($lead->next_contact)
                                    <span class="text-xs text-gray-400">{{ $lead->next_contact->format('d M') }}</span>
                                @endif
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full
                                    {{ $lead->temperature === 'hot' ? 'bg-strawberry-50 text-strawberry-600' : 'bg-amber-50 text-amber-600' }}">
                                    {{ ucfirst($lead->temperature) }}
                                </span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-gray-400">No active hot leads right now.</p>
            @endif
        </div>

        {{-- Recent Policyholders --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-gray-700">Recent Policyholders</h2>
                <a href="{{ route('clients.index') }}" class="text-xs text-matcha-600 hover:underline">View all</a>
            </div>
            @if ($recentClients->count())
                <ul class="divide-y divide-gray-100">
                    @foreach ($recentClients as $client)
                        <li class="py-2.5 flex items-start justify-between">
                            <div>
                                <a href="{{ route('clients.show', $client) }}"
                                   class="text-sm font-medium text-gray-800 hover:text-matcha-600">
                                    <x-pdpa-mask>{{ $client->name }}</x-pdpa-mask>
                                </a>
                                @if ($client->policies->count())
                                    <div class="flex flex-wrap gap-1 mt-0.5">
                                        @foreach ($client->policies->take(3) as $policy)
                                            <span class="inline-block text-xs bg-matcha-50 text-matcha-700 rounded px-1.5 py-0.5">
                                                {{ ucfirst(str_replace('_', ' ', $policy->plan_type)) }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            @php $last = $client->lastTouchpoint(); @endphp
                            @if ($last)
                                <span class="text-xs text-gray-400 whitespace-nowrap ml-2">
                                    {{ $last->contacted_at->format('d M') }}
                                </span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-gray-400">No clients yet. <a href="{{ route('clients.create') }}" class="text-matcha-600 hover:underline">Add your first policyholder.</a></p>
            @endif
        </div>

    </div>

    {{-- Two-column: Top Commission + Top Plan Conversion --}}
    @if ($topCommissionClients->count() || $topPlanProducts->count())
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">

        {{-- Top Commission Revenue --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Top Commission Revenue (Yr 1)</h2>
            @if ($topCommissionClients->count())
                <ul class="divide-y divide-gray-100">
                    @foreach ($topCommissionClients as $c)
                        <li class="py-2.5 flex items-center justify-between">
                            <a href="{{ route('clients.show', $c) }}"
                               class="text-sm font-medium text-gray-800 hover:text-matcha-600">
                                <x-pdpa-mask>{{ $c->name }}</x-pdpa-mask>
                            </a>
                            <span class="text-sm font-semibold text-amber-600 ml-2 whitespace-nowrap">
                                <x-pdpa-mask>RM {{ number_format($c->total_commission, 2) }}</x-pdpa-mask>
                            </span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-gray-400">No commission data yet. Add plan products with commission rates.</p>
            @endif
        </div>

        {{-- Top Plan Conversion --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Top Plans by Conversion</h2>
            @if ($topPlanProducts->count())
                <ul class="divide-y divide-gray-100">
                    @foreach ($topPlanProducts as $product)
                        <li class="py-2.5 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $product->name }}</p>
                                <p class="text-xs text-gray-400">{{ ucfirst(str_replace('_', ' ', $product->plan_type)) }}</p>
                            </div>
                            <span class="text-sm font-semibold text-matcha-600 ml-2 whitespace-nowrap">
                                {{ $product->policies_count }} {{ Str::plural('policy', $product->policies_count) }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-gray-400">No policies linked to plan products yet.</p>
            @endif
        </div>

    </div>
    @endif

    {{-- Two-column: Follow-up Log + Reach Angles --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Recent Touchpoints --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-gray-700">Follow-up Log</h2>
                <a href="{{ route('touchpoints.index') }}" class="text-xs text-matcha-600 hover:underline">View all</a>
            </div>
            @if ($recentTouchpoints->count())
                <ul class="divide-y divide-gray-100">
                    @foreach ($recentTouchpoints as $tp)
                        <li class="py-2.5 flex items-start gap-3">
                            <span class="mt-1.5 w-2 h-2 rounded-full flex-shrink-0
                                {{ $tp->channel === 'whatsapp' ? 'bg-green-400' :
                                   ($tp->channel === 'phone_call' ? 'bg-blue-400' :
                                   ($tp->channel === 'in_person' ? 'bg-matcha-400' : 'bg-gray-300')) }}">
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">
                                    <x-pdpa-mask>{{ $tp->touchable?->name ?? '—' }}</x-pdpa-mask>
                                </p>
                                <p class="text-xs text-gray-500 truncate">{{ $tp->topic }}</p>
                            </div>
                            <span class="text-xs text-gray-400 whitespace-nowrap">
                                {{ $tp->contacted_at->format('d M') }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-gray-400">No touchpoints logged yet.</p>
            @endif
        </div>

        {{-- Active Reach Angles --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-gray-700">Reach Angles</h2>
                <a href="{{ route('angles.index') }}" class="text-xs text-matcha-600 hover:underline">View all</a>
            </div>
            @if ($activeAngles->count())
                <ul class="divide-y divide-gray-100">
                    @foreach ($activeAngles as $angle)
                        <li class="py-2.5">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $angle->title }}</p>
                                    @if ($angle->target_segment)
                                        <p class="text-xs text-gray-400">{{ $angle->target_segment }}</p>
                                    @endif
                                </div>
                                <span class="text-xs text-matcha-600 font-medium ml-2 whitespace-nowrap">
                                    {{ $angle->clients_count }} reached
                                </span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-gray-400">No active reach angles. <a href="{{ route('angles.create') }}" class="text-matcha-600 hover:underline">Add one.</a></p>
            @endif
        </div>

    </div>

    </div>{{-- end exploreOpen --}}
    </div>{{-- end explore-more wrapper --}}

</x-app-layout>
