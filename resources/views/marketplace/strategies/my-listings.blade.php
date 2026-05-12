<x-app-layout>
    <x-slot name="title">My Listings · Strategy Marketplace</x-slot>
    <x-slot name="pageTitle">My Strategy Listings</x-slot>

    <x-slot name="actions">
        <a href="{{ route('marketplace.strategies') }}"
           class="text-xs bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 px-3 py-1.5 rounded-lg transition">
            Browse Marketplace
        </a>
    </x-slot>

    {{-- Earnings summary --}}
    <div class="bg-amber-50 border border-amber-100 rounded-xl px-5 py-4 mb-5 flex items-center gap-4">
        <div>
            <p class="text-xs text-amber-700 font-medium uppercase tracking-wide">Total Earned</p>
            <p class="text-2xl font-bold text-amber-600">{{ $totalEarned }} <span class="text-base font-normal">credits</span></p>
        </div>
        <p class="text-xs text-amber-600/70 ml-auto">
            <a href="{{ route('account.credits') }}" class="hover:underline">View transaction history →</a>
        </p>
    </div>

    @if ($listings->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 px-5 py-16 text-center">
            <p class="text-sm text-gray-400">You haven't listed any strategies yet.</p>
            <p class="text-xs text-gray-400 mt-1">
                Go to <a href="{{ route('angles.library') }}" class="text-matcha-600 hover:underline">Content Library</a>
                and click <strong>Sell</strong> on any pinned content.
            </p>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($listings as $listing)
                <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-start gap-4">

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <p class="font-semibold text-gray-800 truncate">{{ $listing->title }}</p>
                            <span class="text-xs text-amber-600 font-bold whitespace-nowrap">{{ $listing->price_credits }} cr</span>
                        </div>
                        @if ($listing->description)
                            <p class="text-xs text-gray-500 mb-1">{{ $listing->description }}</p>
                        @endif
                        <p class="text-xs text-gray-400">
                            {{ $listing->purchases_count }} sold
                            · earned <strong class="text-amber-600">{{ $listing->purchases_count * $listing->price_credits }} cr</strong>
                            · listed {{ $listing->created_at->diffForHumans() }}
                        </p>
                    </div>

                    {{-- Remove --}}
                    <form method="POST" action="{{ route('marketplace.strategies.destroy', $listing) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                onclick="return confirm('Remove this listing from the marketplace?')"
                                class="text-xs text-red-400 hover:text-red-600 transition whitespace-nowrap">
                            Remove
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif

</x-app-layout>
