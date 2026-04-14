<x-app-layout>
    <x-slot name="title">New Reach Angle · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">New Reach Angle</x-slot>

    <div class="max-w-lg">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <form method="POST" action="{{ route('angles.store') }}">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-strawberry-500">*</span></label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" required
                               placeholder="e.g. Critical Illness Awareness"
                               class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400 @error('title') border-red-400 @enderror" />
                        @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="target_segment" class="block text-sm font-medium text-gray-700 mb-1">Target Segment</label>
                        <input type="text" id="target_segment" name="target_segment" value="{{ old('target_segment') }}"
                               placeholder="e.g. Working adults 30–45"
                               class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400" />
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select id="status" name="status"
                                class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400">
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="paused" {{ old('status') === 'paused' ? 'selected' : '' }}>Paused</option>
                            <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea id="description" name="description" rows="4"
                                  placeholder="What's this angle about? What message does it carry?"
                                  class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="flex items-center gap-3 mt-6">
                    <button type="submit"
                            class="bg-matcha-600 hover:bg-matcha-800 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                        Save Angle
                    </button>
                    <a href="{{ route('angles.index') }}"
                       class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>
