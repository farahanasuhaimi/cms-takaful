<x-app-layout>
    <x-slot name="title">Quotations · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">Quotations</x-slot>
    <x-slot name="actions">
        <a href="{{ route('quotations.create') }}"
           class="bg-matcha-600 hover:bg-matcha-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            + New Quotation
        </a>
    </x-slot>

    @if ($quotations->count())
        <div class="space-y-3">
            @foreach ($quotations as $q)
                <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center justify-between gap-4"
                     x-data="{ del: false }">
                    <div class="min-w-0">
                        <a href="{{ route('quotations.show', $q) }}"
                           class="text-sm font-semibold text-gray-800 hover:text-matcha-700 transition">
                            {{ $q->title }}
                        </a>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $q->people_count }} {{ Str::plural('person', $q->people_count) }} ·
                            {{ $q->plans_count }} {{ Str::plural('plan', $q->plans_count) }} ·
                            {{ $q->created_at->format('d M Y') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        <a href="{{ route('quotations.show', $q) }}"
                           class="text-xs text-matcha-600 hover:underline">View</a>
                        <div>
                            <button @click="del = !del" x-show="!del"
                                    class="text-xs text-strawberry-400 hover:text-strawberry-600">Delete</button>
                            <div x-show="del" class="flex items-center gap-2">
                                <span class="text-xs text-gray-500">Sure?</span>
                                <form method="POST" action="{{ route('quotations.destroy', $q) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-strawberry-600 font-medium">Yes</button>
                                </form>
                                <button @click="del = false" class="text-xs text-gray-400">No</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 px-5 py-12 text-center">
            <p class="text-sm text-gray-400">No quotations yet. <a href="{{ route('quotations.create') }}" class="text-matcha-600 hover:underline">Create your first one.</a></p>
        </div>
    @endif

</x-app-layout>
