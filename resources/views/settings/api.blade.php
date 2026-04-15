<x-app-layout>
    <x-slot name="title">API Settings · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">API Settings</x-slot>

    <div class="max-w-lg">
        <div class="bg-white rounded-xl border border-gray-200 p-6">

            <h2 class="text-sm font-semibold text-gray-800 mb-1">DeepSeek API</h2>
            <p class="text-xs text-gray-400 mb-6">Used for AI content generation in Reach Angles. Keys are stored in the database — never in code.</p>

            <form method="POST" action="{{ route('settings.api.update') }}">
                @csrf

                {{-- API Key --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">API Key</label>
                    <input type="password"
                           name="api_key"
                           value="{{ $apiKey }}"
                           placeholder="sk-..."
                           class="w-full rounded-lg border-gray-300 text-sm focus:ring-matcha-400 focus:border-matcha-400" />
                    <p class="text-xs text-gray-400 mt-1">Leave blank to keep the existing key unchanged.</p>
                    @error('api_key') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Model --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                    <input type="text" name="model" value="{{ $model }}"
                           class="w-full rounded-lg border-gray-300 text-sm focus:ring-matcha-400 focus:border-matcha-400" />
                    <p class="text-xs text-gray-400 mt-1">e.g. <code>deepseek-chat</code> or <code>deepseek-reasoner</code></p>
                    @error('model') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Base URL --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Base URL</label>
                    <input type="text" name="base_url" value="{{ $baseUrl }}"
                           class="w-full rounded-lg border-gray-300 text-sm focus:ring-matcha-400 focus:border-matcha-400" />
                    <p class="text-xs text-gray-400 mt-1">Change this to swap providers (any OpenAI-compatible endpoint).</p>
                    @error('base_url') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <button type="submit"
                        class="bg-matcha-600 hover:bg-matcha-800 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                    Save Settings
                </button>
            </form>
        </div>
    </div>

</x-app-layout>
