<x-app-layout>
    <x-slot name="title">Add Short Link · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">Add Short Link</x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <form method="POST" action="{{ route('short-links.store') }}">
                @csrf

                @include('short-links._form')

                <div class="flex items-center gap-3 mt-6">
                    <button type="submit"
                            class="bg-matcha-600 hover:bg-matcha-800 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                        Save Link
                    </button>
                    <a href="{{ route('short-links.index') }}"
                       class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
