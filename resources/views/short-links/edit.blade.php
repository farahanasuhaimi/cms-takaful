<x-app-layout>
    <x-slot name="title">Edit Short Link · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">Edit Short Link</x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <form method="POST" action="{{ route('short-links.update', $shortLink) }}">
                @csrf
                @method('PUT')

                @include('short-links._form')

                <div class="flex items-center justify-between mt-6">
                    <div class="flex items-center gap-3">
                        <button type="submit"
                                class="bg-matcha-600 hover:bg-matcha-800 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                            Update Link
                        </button>
                        <a href="{{ route('short-links.index') }}"
                           class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                    </div>

                    <div x-data="{ confirm: false }">
                        <button type="button" @click="confirm = true" x-show="!confirm"
                                class="text-xs text-strawberry-500 hover:text-strawberry-700">Delete</button>
                        <div x-show="confirm" class="flex items-center gap-2">
                            <span class="text-xs text-gray-600">Sure?</span>
                            <button type="submit" form="delete-link-form" class="text-xs text-strawberry-600 font-medium hover:underline">Yes</button>
                            <button type="button" @click="confirm = false" class="text-xs text-gray-400">No</button>
                        </div>
                    </div>
                </div>
            </form>

            <form id="delete-link-form" method="POST" action="{{ route('short-links.destroy', $shortLink) }}">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</x-app-layout>
