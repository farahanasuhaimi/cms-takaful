<x-app-layout>
    <x-slot name="title">Edit Lead · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">Edit Lead</x-slot>

    <div class="max-w-lg">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <form method="POST" action="{{ route('leads.update', $lead) }}">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-strawberry-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name', $lead->name) }}" required
                               class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400 @error('name') border-red-400 @enderror" />
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $lead->phone) }}"
                               class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="temperature" class="block text-sm font-medium text-gray-700 mb-1">Temperature</label>
                            <select id="temperature" name="temperature"
                                    class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400">
                                <option value="warm" {{ old('temperature', $lead->temperature) === 'warm' ? 'selected' : '' }}>Warm</option>
                                <option value="hot" {{ old('temperature', $lead->temperature) === 'hot' ? 'selected' : '' }}>Hot</option>
                            </select>
                        </div>
                        <div>
                            <label for="stage" class="block text-sm font-medium text-gray-700 mb-1">Stage</label>
                            <select id="stage" name="stage"
                                    class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400">
                                @foreach (['new','contacted','presented','negotiating','stalled'] as $s)
                                    <option value="{{ $s }}" {{ old('stage', $lead->stage) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="source" class="block text-sm font-medium text-gray-700 mb-1">Source</label>
                        <select id="source" name="source"
                                class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400">
                            @foreach (['referral','social_media','cold_outreach','event','walk_in','other'] as $src)
                                <option value="{{ $src }}" {{ old('source', $lead->source) === $src ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $src)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="interest_area" class="block text-sm font-medium text-gray-700 mb-1">Interest Area</label>
                        <input type="text" id="interest_area" name="interest_area"
                               value="{{ old('interest_area', $lead->interest_area) }}"
                               class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400" />
                    </div>

                    <div>
                        <label for="next_contact" class="block text-sm font-medium text-gray-700 mb-1">Next Contact Date</label>
                        <input type="date" id="next_contact" name="next_contact"
                               value="{{ old('next_contact', $lead->next_contact?->format('Y-m-d')) }}"
                               class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400" />
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea id="notes" name="notes" rows="3"
                                  class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400">{{ old('notes', $lead->notes) }}</textarea>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-6">
                    <div class="flex items-center gap-3">
                        <button type="submit"
                                class="bg-matcha-600 hover:bg-matcha-800 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                            Update Lead
                        </button>
                        <a href="{{ route('leads.index') }}"
                           class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                    </div>

                    <div x-data="{ confirm: false }">
                        <button type="button" @click="confirm = true" x-show="!confirm"
                                class="text-xs text-strawberry-500 hover:text-strawberry-700">Delete</button>
                        <div x-show="confirm" class="flex items-center gap-2">
                            <span class="text-xs text-gray-600">Sure?</span>
                            <form method="POST" action="{{ route('leads.destroy', $lead) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-strawberry-600 font-medium hover:underline">Yes</button>
                            </form>
                            <button @click="confirm = false" class="text-xs text-gray-400">No</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>
