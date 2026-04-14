<x-app-layout>
    <x-slot name="title">New Client · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">New Policyholder</x-slot>

    <div class="max-w-lg">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <form method="POST" action="{{ route('clients.store') }}">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-strawberry-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                               class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400 @error('name') border-red-400 @enderror" />
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone <span class="text-gray-400 font-normal">(60xxxxxxxxx)</span></label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                               class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400" />
                        @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="ic_no" class="block text-sm font-medium text-gray-700 mb-1">IC Number</label>
                        <input type="text" id="ic_no" name="ic_no" value="{{ old('ic_no') }}"
                               class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400" />
                        @error('ic_no') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400" />
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea id="notes" name="notes" rows="3"
                                  class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400">{{ old('notes') }}</textarea>
                        @error('notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3 mt-6">
                    <button type="submit"
                            class="bg-matcha-600 hover:bg-matcha-800 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                        Save Client
                    </button>
                    <a href="{{ route('clients.index') }}"
                       class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>
