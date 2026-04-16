<x-app-layout>
    <x-slot name="title">Edit Angle · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">Edit Reach Angle</x-slot>

    <div class="max-w-lg">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <form method="POST" action="{{ route('angles.update', $angle) }}">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-strawberry-500">*</span></label>
                        <input type="text" id="title" name="title" value="{{ old('title', $angle->title) }}" required
                               class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400 @error('title') border-red-400 @enderror" />
                        @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="target_segment" class="block text-sm font-medium text-gray-700 mb-1">Target Segment</label>
                        <input type="text" id="target_segment" name="target_segment"
                               value="{{ old('target_segment', $angle->target_segment) }}"
                               class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400" />
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select id="status" name="status"
                                class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400">
                            @foreach (['active', 'paused', 'archived'] as $s)
                                <option value="{{ $s }}" {{ old('status', $angle->status) === $s ? 'selected' : '' }}>
                                    {{ ucfirst($s) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea id="description" name="description" rows="4"
                                  class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400">{{ old('description', $angle->description) }}</textarea>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-6">
                    <div class="flex items-center gap-3">
                        <button type="submit"
                                class="bg-matcha-600 hover:bg-matcha-800 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                            Update Angle
                        </button>
                        <a href="{{ route('angles.index') }}"
                           class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                    </div>
                    <div x-data="{ confirm: false }">
                        <button type="button" @click="confirm = true" x-show="!confirm"
                                class="text-xs text-strawberry-500 hover:text-strawberry-700">Delete</button>
                        <div x-show="confirm" class="flex items-center gap-2">
                            <span class="text-xs text-gray-600">Sure?</span>
                            <button type="submit" form="delete-angle-form" class="text-xs text-strawberry-600 font-medium hover:underline">Yes</button>
                            <button type="button" @click="confirm = false" class="text-xs text-gray-400">No</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Hidden delete form -->
    <form id="delete-angle-form" method="POST" action="{{ route('angles.destroy', $angle) }}" class="hidden">
        @csrf
        @method('DELETE')
    </form>

</x-app-layout>
