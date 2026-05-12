<x-app-layout>
    <x-slot name="title">Policy Marketplace · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">Policy Marketplace</x-slot>

    <p class="text-sm text-gray-500 mb-5">
        Browse plan products shared by agents. Star the useful ones, import to your own catalog for free.
    </p>

    @if ($products->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 px-5 py-16 text-center">
            <p class="text-sm text-gray-400">No plans shared yet.</p>
            <p class="text-xs text-gray-400 mt-1">
                Go to <a href="{{ route('plan-products.index') }}" class="text-matcha-600 hover:underline">Plan Catalog</a>
                and edit a plan to share it here.
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($products as $product)
                @php $isStarred = isset($starredIds[$product->id]); @endphp
                <div class="bg-white rounded-xl border border-gray-200 p-5 flex flex-col gap-3"
                     x-data="{ starred: {{ $isStarred ? 'true' : 'false' }}, count: {{ $product->stars_count }} }">

                    {{-- Header --}}
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-800 truncate">{{ $product->name }}</p>
                            <span class="inline-block mt-0.5 text-xs bg-matcha-50 text-matcha-700 rounded px-1.5 py-0.5">
                                {{ ucfirst(str_replace('_', ' ', $product->plan_type)) }}
                            </span>
                        </div>
                        {{-- Star button --}}
                        <button @click="
                                fetch('{{ route('marketplace.policies.star', $product) }}', {
                                    method: 'POST',
                                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                                })
                                .then(r => r.json())
                                .then(d => { starred = d.starred; count = d.count; })"
                                class="flex-shrink-0 flex items-center gap-1 text-sm transition"
                                :class="starred ? 'text-amber-500' : 'text-gray-300 hover:text-amber-400'">
                            <svg class="w-5 h-5" :fill="starred ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                            <span x-text="count" class="text-xs font-medium"></span>
                        </button>
                    </div>

                    {{-- Commission --}}
                    @if ($product->commission_first_year)
                        <p class="text-xs text-gray-500">
                            Commission: <span class="font-medium text-gray-700">{{ $product->commission_first_year }}%</span> (1st year)
                        </p>
                    @endif

                    {{-- Shared note --}}
                    @if ($product->shared_note)
                        <p class="text-xs text-gray-500 leading-relaxed">{{ $product->shared_note }}</p>
                    @endif

                    {{-- Footer --}}
                    <div class="flex items-center justify-between mt-auto pt-2 border-t border-gray-50">
                        <p class="text-xs text-gray-400">by {{ $product->user->name }}</p>
                        <form method="POST" action="{{ route('marketplace.policies.import', $product) }}">
                            @csrf
                            <button type="submit"
                                    class="text-xs bg-matcha-600 hover:bg-matcha-800 text-white px-3 py-1.5 rounded-lg transition">
                                Import
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</x-app-layout>
