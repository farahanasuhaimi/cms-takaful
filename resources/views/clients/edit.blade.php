<x-app-layout>
    <x-slot name="title">Edit {{ $client->name }} · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">Edit Policyholder</x-slot>

    <div class="max-w-lg">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <form method="POST" action="{{ route('clients.update', $client) }}">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-strawberry-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name', $client->name) }}" required
                               class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400 @error('name') border-red-400 @enderror" />
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $client->phone) }}"
                               class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400" />
                    </div>

                    <div>
                        <label for="ic_no" class="block text-sm font-medium text-gray-700 mb-1">IC Number</label>
                        <input type="text" id="ic_no" name="ic_no" value="{{ old('ic_no', $client->ic_no) }}"
                               class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400" />
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $client->email) }}"
                               class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400" />
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea id="notes" name="notes" rows="3"
                                  class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400">{{ old('notes', $client->notes) }}</textarea>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-6">
                    <div class="flex items-center gap-3">
                        <button type="submit"
                                class="bg-matcha-600 hover:bg-matcha-800 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                            Update Client
                        </button>
                        <a href="{{ route('clients.show', $client) }}"
                           class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                    </div>

                    {{-- Delete --}}
                    <div x-data="{ confirm: false }">
                        <button type="button" @click="confirm = true"
                                x-show="!confirm"
                                class="text-xs text-strawberry-500 hover:text-strawberry-700">
                            Delete client
                        </button>
                        <div x-show="confirm" class="flex items-center gap-2">
                            <span class="text-xs text-gray-600">Are you sure?</span>
                            <button type="submit" form="delete-client-form" class="text-xs text-strawberry-600 font-medium hover:underline">Yes, delete</button>
                            <button type="button" @click="confirm = false" class="text-xs text-gray-400 hover:text-gray-600">Cancel</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Hidden delete form -->
    <form id="delete-client-form" method="POST" action="{{ route('clients.destroy', $client) }}" class="hidden">
        @csrf
        @method('DELETE')
    </form>

</x-app-layout>
