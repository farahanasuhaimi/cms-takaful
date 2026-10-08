<x-app-layout>
    <x-slot name="title">Leads · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">Warm &amp; Hot Leads</x-slot>
    <x-slot name="actions">
        <a href="{{ route('leads.create') }}"
           class="bg-matcha-600 hover:bg-matcha-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            + New Lead
        </a>
    </x-slot>

    {{-- Hot Leads --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 mb-3">
            <h2 class="text-sm font-semibold text-gray-700">Hot Leads</h2>
            <span class="text-xs bg-strawberry-50 text-strawberry-600 font-medium px-2 py-0.5 rounded-full">
                {{ $hotLeads->count() }}
            </span>
        </div>

        @if ($hotLeads->count())
            @include('leads._list', ['leads' => $hotLeads, 'tone' => 'hot'])
        @else
            <div class="bg-white rounded-xl border border-gray-200 px-5 py-8 text-center">
                <p class="text-sm text-gray-400">No hot leads right now.</p>
            </div>
        @endif
    </div>

    {{-- Warm Leads --}}
    <div>
        <div class="flex items-center gap-2 mb-3">
            <h2 class="text-sm font-semibold text-gray-700">Warm Leads</h2>
            <span class="text-xs bg-amber-50 text-amber-600 font-medium px-2 py-0.5 rounded-full">
                {{ $warmLeads->count() }}
            </span>
        </div>

        @if ($warmLeads->count())
            @include('leads._list', ['leads' => $warmLeads, 'tone' => 'warm'])
        @else
            <div class="bg-white rounded-xl border border-gray-200 px-5 py-8 text-center">
                <p class="text-sm text-gray-400">No warm leads. <a href="{{ route('leads.create') }}" class="text-matcha-600 hover:underline">Add one.</a></p>
            </div>
        @endif
    </div>

</x-app-layout>
