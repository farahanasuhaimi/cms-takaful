<x-app-layout>
    <x-slot name="title">Strategy Marketplace · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">Strategy Marketplace</x-slot>

    <x-slot name="actions">
        <div class="flex items-center gap-2">
            <a href="{{ route('strategies.index') }}"
               class="text-xs bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 px-3 py-1.5 rounded-lg transition">
                My Library
            </a>
            <a href="{{ route('marketplace.strategies.my') }}"
               class="text-xs bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 px-3 py-1.5 rounded-lg transition">
                My Listings
            </a>
        </div>
    </x-slot>

    <p class="text-sm text-gray-500 mb-4">
        Browse strategies shared by other agents. Buy with credits — strategy is copied to your library.
    </p>

    {{-- Filters --}}
    <form method="GET" action="{{ route('marketplace.strategies') }}"
          class="bg-white rounded-xl border border-gray-200 p-4 mb-5 flex flex-wrap gap-3 items-end">

        <div class="flex flex-col gap-1">
            <label class="text-xs text-gray-500">Category</label>
            <select name="category" class="text-xs border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                <option value="">All</option>
                @foreach(['prospecting'=>'Prospecting','content'=>'Content','objection_handling'=>'Objection Handling','follow_up'=>'Follow Up','referral'=>'Referral','closing'=>'Closing'] as $val => $label)
                    <option value="{{ $val }}" @selected(request('category') === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex flex-col gap-1">
            <label class="text-xs text-gray-500">Channel</label>
            <select name="channel" class="text-xs border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                <option value="">All</option>
                @foreach(['whatsapp'=>'WhatsApp','instagram'=>'Instagram','facebook'=>'Facebook','face_to_face'=>'Face to Face','general'=>'General'] as $val => $label)
                    <option value="{{ $val }}" @selected(request('channel') === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex flex-col gap-1">
            <label class="text-xs text-gray-500">Audience</label>
            <select name="audience" class="text-xs border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                <option value="">All</option>
                @foreach(['strangers'=>'Strangers','warm_leads'=>'Warm Leads','family_friends'=>'Family & Friends','corporate'=>'Corporate','general'=>'General'] as $val => $label)
                    <option value="{{ $val }}" @selected(request('audience') === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex flex-col gap-1">
            <label class="text-xs text-gray-500">Type</label>
            <select name="type" class="text-xs border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                <option value="">All</option>
                <option value="script" @selected(request('type') === 'script')>Script</option>
                <option value="flow"   @selected(request('type') === 'flow')>Flow</option>
            </select>
        </div>

        <button type="submit"
                class="text-xs bg-matcha-600 hover:bg-matcha-700 text-white px-3 py-1.5 rounded-lg transition">
            Filter
        </button>

        @if(request()->hasAny(['category','channel','audience','difficulty','type']))
            <a href="{{ route('marketplace.strategies') }}"
               class="text-xs text-gray-400 hover:text-gray-600 py-1.5">Clear</a>
        @endif
    </form>

    @if ($listings->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 px-5 py-16 text-center">
            <p class="text-sm text-gray-400">No strategies listed yet.</p>
            <p class="text-xs text-gray-400 mt-1">
                Go to <a href="{{ route('strategies.index') }}" class="text-matcha-600 hover:underline">Strategy Library</a>
                and list one of your strategies for sale.
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($listings as $listing)
                @php
                    $alreadyBought = isset($purchasedIds[$listing->id]);
                    $strategy = $listing->strategy;
                @endphp
                <div class="bg-white rounded-xl border border-gray-200 p-5 flex flex-col gap-3">

                    {{-- Header --}}
                    <div class="flex items-start justify-between gap-2">
                        <p class="font-semibold text-gray-800 leading-snug">{{ $listing->title }}</p>
                        <span class="flex-shrink-0 text-sm font-bold text-amber-600 whitespace-nowrap">
                            {{ $listing->price_credits }} cr
                        </span>
                    </div>

                    {{-- Strategy meta badges --}}
                    @if ($strategy)
                        <div class="flex flex-wrap gap-1.5">
                            <span class="text-xs px-2 py-0.5 rounded-full
                                {{ $strategy->type === 'flow' ? 'bg-violet-100 text-violet-600' : 'bg-sky-100 text-sky-600' }}">
                                {{ ucfirst($strategy->type) }}
                                @if ($strategy->type === 'flow')
                                    · {{ $strategy->steps->count() }} steps
                                @endif
                            </span>
                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">
                                {{ \App\Models\Strategy::categoryLabel($strategy->category) }}
                            </span>
                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">
                                {{ \App\Models\Strategy::channelLabel($strategy->channel) }}
                            </span>
                        </div>
                    @endif

                    {{-- Description --}}
                    @if ($listing->description)
                        <p class="text-xs text-gray-500 leading-relaxed">{{ $listing->description }}</p>
                    @endif

                    {{-- Content preview (angle_content legacy OR strategy content) --}}
                    @if ($strategy && $strategy->type === 'script' && $strategy->content)
                        <p class="text-xs text-gray-600 leading-relaxed line-clamp-4 bg-gray-50 rounded-lg p-3">
                            {{ $strategy->content }}
                        </p>
                    @elseif ($listing->angleContent)
                        <p class="text-xs text-gray-600 leading-relaxed line-clamp-4 bg-gray-50 rounded-lg p-3">
                            {{ $listing->angleContent->content }}
                        </p>
                    @endif

                    {{-- Footer --}}
                    <div class="flex items-center justify-between mt-auto pt-2 border-t border-gray-50">
                        <div>
                            <p class="text-xs text-gray-400">by {{ $listing->seller->name }}</p>
                            @if ($listing->purchases_count > 0)
                                <p class="text-xs text-gray-300">{{ $listing->purchases_count }} sold</p>
                            @endif
                        </div>

                        @if ($listing->seller_user_id === auth()->id())
                            <span class="text-xs text-gray-400 italic">Your listing</span>
                        @elseif ($alreadyBought)
                            <span class="text-xs text-matcha-600 font-medium">Purchased</span>
                        @else
                            <form method="POST" action="{{ route('marketplace.strategies.buy', $listing) }}">
                                @csrf
                                <button type="submit"
                                        class="text-xs bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg transition">
                                    Buy · {{ $listing->price_credits }} cr
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</x-app-layout>
