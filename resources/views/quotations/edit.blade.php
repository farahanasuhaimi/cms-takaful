<x-app-layout>
    <x-slot name="title">Edit Quotation · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">Edit Quotation</x-slot>
    <x-slot name="actions">
        <a href="{{ route('quotations.show', $quotation) }}"
           class="text-xs bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 px-3 py-1.5 rounded-lg transition">
            ← Back
        </a>
    </x-slot>

    <form method="POST" action="{{ route('quotations.update', $quotation) }}" id="qform" @submit.prevent="submit">
        @csrf
        @method('PUT')
        <input type="hidden" name="data" id="q-data">
    </form>

    <div x-data="quotationBuilder({{ json_encode($initial, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) }}, {{ json_encode($planCatalog, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) }})" class="space-y-6">

        @include('quotations._form-fields')

        {{-- Submit --}}
        <div class="flex items-center gap-3">
            <button type="button" @click="submit()"
                    class="bg-matcha-600 hover:bg-matcha-800 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition">
                Save Changes
            </button>
            <a href="{{ route('quotations.show', $quotation) }}" class="text-sm text-gray-400 hover:text-gray-600 transition">Cancel</a>
        </div>

    </div>

</x-app-layout>
