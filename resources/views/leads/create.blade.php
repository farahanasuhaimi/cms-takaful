<x-app-layout>
    <x-slot name="title">New Lead · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">New Lead</x-slot>

    <div class="max-w-lg">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <form method="POST" action="{{ route('leads.store') }}">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-strawberry-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                               class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400 @error('name') border-red-400 @enderror" />
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                               class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="temperature" class="block text-sm font-medium text-gray-700 mb-1">Temperature <span class="text-strawberry-500">*</span></label>
                            <select id="temperature" name="temperature"
                                    class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400">
                                <option value="warm" {{ old('temperature') === 'warm' ? 'selected' : '' }}>Warm</option>
                                <option value="hot" {{ old('temperature') === 'hot' ? 'selected' : '' }}>Hot</option>
                            </select>
                        </div>
                        <div>
                            <label for="stage" class="block text-sm font-medium text-gray-700 mb-1">Stage <span class="text-strawberry-500">*</span></label>
                            <select id="stage" name="stage"
                                    class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400">
                                @foreach (['new','contacted','presented','negotiating','stalled'] as $s)
                                    <option value="{{ $s }}" {{ old('stage') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="source" class="block text-sm font-medium text-gray-700 mb-1">Source <span class="text-strawberry-500">*</span></label>
                        <select id="source" name="source"
                                class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400">
                            @foreach (['referral','social_media','cold_outreach','event','walk_in','other'] as $src)
                                <option value="{{ $src }}" {{ old('source') === $src ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $src)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="interest_area" class="block text-sm font-medium text-gray-700 mb-1">Interest Area</label>
                        <input type="text" id="interest_area" name="interest_area" value="{{ old('interest_area') }}"
                               placeholder="e.g. Medical Card + CI"
                               class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400" />
                    </div>

                    <div>
                        <label for="next_contact" class="block text-sm font-medium text-gray-700 mb-1">Next Contact Date</label>
                        <input type="date" id="next_contact" name="next_contact" value="{{ old('next_contact') }}"
                               class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400" />
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea id="notes" name="notes" rows="3"
                                  class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="flex items-center gap-3 mt-6">
                    <button type="submit"
                            class="bg-matcha-600 hover:bg-matcha-800 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                        Save Lead
                    </button>
                    <a href="{{ route('leads.index') }}"
                       class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>
