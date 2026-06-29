<x-app-layout>
    <x-slot name="title">Daily Posts · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">Daily Posts</x-slot>
    <x-slot name="actions">
        <button @click="$dispatch('open-modal')"
                class="bg-matcha-600 hover:bg-matcha-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            + Plan Post
        </button>
    </x-slot>

    <div x-data="{ modalOpen: false }" @open-modal.window="modalOpen = true">

        {{-- Error flash --}}
        @if (session('error'))
            <div class="mb-4 px-4 py-3 bg-strawberry-50 text-strawberry-800 border border-strawberry-200 rounded-lg text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Posts list --}}
        @if ($posts->count())
            <div class="space-y-2">
                @foreach ($posts as $post)
                    @php
                        $platformColour = match($post->platform) {
                            'instagram' => 'bg-purple-50 text-purple-700',
                            'facebook'  => 'bg-blue-50 text-blue-700',
                            'whatsapp'  => 'bg-green-50 text-green-700',
                            'tiktok'    => 'bg-gray-100 text-gray-700',
                            default     => 'bg-gray-100 text-gray-600',
                        };
                        $statusColour = match($post->status) {
                            'posted'  => 'bg-matcha-100 text-matcha-700',
                            'ready'   => 'bg-amber-50 text-amber-700',
                            default   => 'bg-gray-100 text-gray-500',
                        };
                    @endphp
                    <a href="{{ route('daily-posts.show', $post) }}"
                       class="flex items-center gap-4 bg-white border border-gray-200 rounded-xl px-5 py-4 hover:border-matcha-300 hover:shadow-sm transition group">

                        {{-- Date --}}
                        <div class="w-14 text-center flex-shrink-0">
                            <p class="text-xs text-gray-400 uppercase">{{ $post->post_date->format('M') }}</p>
                            <p class="text-2xl font-bold text-gray-800 leading-tight">{{ $post->post_date->format('d') }}</p>
                            <p class="text-xs text-gray-400">{{ $post->post_date->format('D') }}</p>
                        </div>

                        {{-- Topic --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate group-hover:text-matcha-700">{{ $post->topic }}</p>
                            @if ($post->caption)
                                <p class="text-xs text-gray-400 truncate mt-0.5">{{ Str::limit($post->caption, 80) }}</p>
                            @else
                                <p class="text-xs text-gray-300 mt-0.5 italic">No content yet — click to generate</p>
                            @endif
                        </div>

                        {{-- Badges --}}
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $platformColour }}">
                                {{ ucfirst($post->platform) }}
                            </span>
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $statusColour }}">
                                {{ ucfirst($post->status) }}
                            </span>
                        </div>

                    </a>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $posts->links() }}
            </div>
        @else
            <div class="text-center py-20 bg-white rounded-xl border border-gray-200">
                <p class="text-gray-400 text-sm mb-3">No posts planned yet.</p>
                <button @click="modalOpen = true"
                        class="text-sm text-matcha-600 hover:underline font-medium">
                    Plan your first post
                </button>
            </div>
        @endif

        {{-- New Post Modal --}}
        <div x-show="modalOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4"
             @click.self="modalOpen = false"
             style="display:none">

            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6"
                 @click.stop>
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-base font-semibold text-gray-800">Plan a Post</h2>
                    <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('daily-posts.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Date</label>
                        <input type="date" name="post_date"
                               value="{{ old('post_date', now()->format('Y-m-d')) }}"
                               class="w-full text-sm border-gray-200 rounded-lg px-3 py-2 focus:ring-matcha-400 focus:border-matcha-400"
                               required />
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Platform</label>
                        <select name="platform"
                                class="w-full text-sm border-gray-200 rounded-lg px-3 py-2 focus:ring-matcha-400 focus:border-matcha-400"
                                required>
                            <option value="">Choose platform...</option>
                            <option value="instagram" {{ old('platform') === 'instagram' ? 'selected' : '' }}>Instagram</option>
                            <option value="facebook"  {{ old('platform') === 'facebook'  ? 'selected' : '' }}>Facebook</option>
                            <option value="whatsapp"  {{ old('platform') === 'whatsapp'  ? 'selected' : '' }}>WhatsApp</option>
                            <option value="tiktok"    {{ old('platform') === 'tiktok'    ? 'selected' : '' }}>TikTok</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Topic</label>
                        <input type="text" name="topic"
                               value="{{ old('topic') }}"
                               placeholder="e.g. hospital income protection for young parents"
                               class="w-full text-sm border-gray-200 rounded-lg px-3 py-2 focus:ring-matcha-400 focus:border-matcha-400"
                               required />
                        <p class="text-xs text-gray-400 mt-1">Describe what this post is about — the AI will write the caption.</p>
                    </div>

                    @if ($angles->count())
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Reach Angle <span class="text-gray-400 font-normal">(optional)</span></label>
                        <select name="reach_angle_id"
                                class="w-full text-sm border-gray-200 rounded-lg px-3 py-2 focus:ring-matcha-400 focus:border-matcha-400">
                            <option value="">No angle — generate freely</option>
                            @foreach ($angles as $angle)
                                <option value="{{ $angle->id }}" {{ old('reach_angle_id') == $angle->id ? 'selected' : '' }}>
                                    {{ $angle->title }}{{ $angle->target_segment ? ' · ' . $angle->target_segment : '' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Linking an angle focuses the caption toward a specific audience and message.</p>
                    </div>
                    @endif

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="modalOpen = false"
                                class="text-sm text-gray-500 hover:text-gray-700 transition px-4 py-2">
                            Cancel
                        </button>
                        <button type="submit"
                                class="bg-matcha-600 hover:bg-matcha-800 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                            Plan Post
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </div>

</x-app-layout>
