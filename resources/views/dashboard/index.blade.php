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
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 px-5 py-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total Policyholders</p>
            <p class="text-3xl font-bold text-matcha-800 mt-1">{{ $totalClients }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 px-5 py-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Hot Leads</p>
            <p class="text-3xl font-bold text-strawberry-600 mt-1">{{ $hotLeads }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 px-5 py-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Warm Leads</p>
            <p class="text-3xl font-bold text-amber-500 mt-1">{{ $warmLeads }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 px-5 py-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Last Outreach</p>
            @if ($recentTouchpoints->count())
                <p class="text-sm font-semibold text-gray-700 mt-2">
                    {{ $recentTouchpoints->first()->contacted_at->format('d M Y') }}
                </p>
            @else
                <p class="text-sm text-gray-400 mt-2">No outreach yet</p>
            @endif
        </div>
        <div class="bg-white rounded-xl border border-gray-200 px-5 py-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Est. Commission (Yr 1)</p>
            @if ($totalEstimatedCommission > 0)
                <p class="text-3xl font-bold text-amber-600 mt-1">RM {{ number_format($totalEstimatedCommission, 0) }}</p>
            @else
                <p class="text-sm text-gray-400 mt-2">No data yet</p>
            @endif
        </div>
    </div>

    {{-- Renewal alert --}}
    @if ($renewingSoon->count())
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 mb-4">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-amber-800">Renewals in the Next 30 Days</h2>
                <span class="text-xs font-medium bg-amber-200 text-amber-800 px-2 py-0.5 rounded-full">
                    {{ $renewingSoon->count() }} {{ Str::plural('policy', $renewingSoon->count()) }}
                </span>
            </div>
            <ul class="divide-y divide-amber-100">
                @foreach ($renewingSoon as $policy)
                    @php $daysLeft = (int) now()->startOfDay()->diffInDays($policy->computed_renewal, false); @endphp
                    <li class="py-2.5 flex items-center justify-between">
                        <div>
                            <a href="{{ route('clients.show', $policy->client) }}"
                               class="text-sm font-medium text-gray-800 hover:text-matcha-600">
                                {{ $policy->client->name }}
                            </a>
                            <p class="text-xs text-gray-500">
                                {{ ucfirst(str_replace('_', ' ', $policy->plan_type)) }}
                                @if ($policy->plan_name) · {{ $policy->plan_name }} @endif
                            </p>
                        </div>
                        <div class="flex items-center gap-3 ml-2">
                            <span class="text-xs text-gray-500">{{ $policy->computed_renewal->format('d M Y') }}</span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full whitespace-nowrap
                                {{ $daysLeft <= 7 ? 'bg-strawberry-100 text-strawberry-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $daysLeft === 0 ? 'Today' : $daysLeft . 'd left' }}
                            </span>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Two-column: Recent Clients + Urgent Leads --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">

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
                                    {{ $client->name }}
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
                                <p class="text-sm font-medium text-gray-800">{{ $lead->name }}</p>
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
                                {{ $c->name }}
                            </a>
                            <span class="text-sm font-semibold text-amber-600 ml-2 whitespace-nowrap">
                                RM {{ number_format($c->total_commission, 2) }}
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
                            {{-- Channel dot --}}
                            <span class="mt-1.5 w-2 h-2 rounded-full flex-shrink-0
                                {{ $tp->channel === 'whatsapp' ? 'bg-green-400' :
                                   ($tp->channel === 'phone_call' ? 'bg-blue-400' :
                                   ($tp->channel === 'in_person' ? 'bg-matcha-400' : 'bg-gray-300')) }}">
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">
                                    {{ $tp->touchable?->name ?? '—' }}
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

</x-app-layout>
