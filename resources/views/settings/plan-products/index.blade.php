<x-app-layout>
    <x-slot name="title">Plan Catalog · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">Plan Catalog</x-slot>
    <x-slot name="actions">
        <a href="{{ route('plan-products.create') }}"
           class="bg-matcha-600 hover:bg-matcha-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            + Register Plan
        </a>
    </x-slot>

    @if ($products->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 px-5 py-12 text-center">
            <p class="text-sm text-gray-400">No plans registered yet. <a href="{{ route('plan-products.create') }}" class="text-matcha-600 hover:underline">Register your first plan.</a></p>
        </div>
    @else
        <div class="space-y-6">
            @foreach ($products as $type => $group)
                <div>
                    {{-- Plan type heading --}}
                    <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                        {{ ucfirst(str_replace('_', ' ', $type)) }}
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach ($group as $product)
                            <div class="bg-white rounded-xl border border-gray-200 p-4">
                                <div class="flex items-start justify-between">
                                    <h3 class="text-sm font-semibold text-gray-800">{{ $product->name }}</h3>
                                    <div class="flex items-center gap-3 ml-2 flex-shrink-0">
                                        <a href="{{ route('plan-products.edit', $product) }}"
                                           class="text-xs text-matcha-600 hover:underline">Edit</a>
                                    </div>
                                </div>

                                {{-- Attributes --}}
                                @if ($product->attributes && count($product->attributes))
                                    <ul class="mt-2 space-y-1">
                                        @foreach ($product->attributes as $key => $value)
                                            <li class="flex items-center gap-2 text-xs">
                                                <span class="text-gray-400">{{ $key }}</span>
                                                <span class="text-gray-300">·</span>
                                                <span class="text-gray-700 font-medium">{{ $value }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif

                                @if ($product->commission_first_year)
                                    <p class="mt-2 text-xs text-amber-600 font-medium">
                                        Commission (yr 1): {{ number_format($product->commission_first_year, 2) }}%
                                    </p>
                                @endif

                                @if ($product->notes)
                                    <p class="mt-2 text-xs text-gray-400 border-t border-gray-100 pt-2">{{ $product->notes }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</x-app-layout>
