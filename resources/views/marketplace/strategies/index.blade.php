<x-app-layout>
    <x-slot name="title">Strategy Marketplace · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">Strategy Marketplace</x-slot>

    <x-slot name="actions">
        <a href="{{ route('marketplace.strategies.my') }}"
           class="text-xs bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 px-3 py-1.5 rounded-lg transition">
            My Listings
        </a>
    </x-slot>

    <p class="text-sm text-gray-500 mb-5">
        Browse AI-generated strategies shared by other agents. Buy with credits — the content is copied to your library.
    </p>

    @if ($listings->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 px-5 py-16 text-center">
            <p class="text-sm text-gray-400">No strategies listed yet.</p>
            <p class="text-xs text-gray-400 mt-1">
                Go to <a href="{{ route('angles.library') }}" class="text-matcha-600 hover:underline">Content Library</a>
                and list a pinned strategy for sale.
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($listings as $listing)
                @php $alreadyBought = isset($purchasedIds[$listing->id]); @endphp
                <div class="bg-white rounded-xl border border-gray-200 p-5 flex flex-col gap-3">

                    {{-- Header --}}
                    <div class="flex items-start justify-between gap-2">
                        <p class="font-semibold text-gray-800 leading-snug">{{ $listing->title }}</p>
                        <span class="flex-shrink-0 text-sm font-bold text-amber-600 whitespace-nowrap">
                            {{ $listing->price_credits }} cr
                        </span>
                    </div>

                    {{-- Description --}}
                    @if ($listing->description)
                        <p class="text-xs text-gray-500 leading-relaxed">{{ $listing->description }}</p>
                    @endif

                    {{-- Content preview --}}
                    @if ($listing->angleContent)
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
