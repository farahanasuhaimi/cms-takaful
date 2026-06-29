<x-app-layout>
    <x-slot name="title">{{ $post->topic }} · Daily Posts</x-slot>
    <x-slot name="pageTitle">Daily Post</x-slot>
    <x-slot name="actions">
        <a href="{{ route('daily-posts.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700 transition px-3 py-2">
            ← All Posts
        </a>
    </x-slot>

    <div class="max-w-2xl mx-auto space-y-4" x-data="{ copied: null }">

        {{-- Error flash --}}
        @if (session('error'))
            <div class="px-4 py-3 bg-strawberry-50 text-strawberry-800 border border-strawberry-200 rounded-lg text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Post header card --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        @php
                            $platformColour = match($post->platform) {
                                'instagram' => 'bg-purple-50 text-purple-700',
                                'facebook'  => 'bg-blue-50 text-blue-700',
                                'whatsapp'  => 'bg-green-50 text-green-700',
                                'tiktok'    => 'bg-gray-100 text-gray-700',
                                default     => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $platformColour }}">
                            {{ ucfirst($post->platform) }}
                        </span>
                        <span class="text-xs text-gray-400">{{ $post->post_date->format('d M Y, l') }}</span>
                    </div>
                    <h2 class="text-base font-semibold text-gray-800">{{ $post->topic }}</h2>
                </div>

                {{-- Status toggle --}}
                <form method="POST" action="{{ route('daily-posts.update', $post) }}" class="flex-shrink-0">
                    @csrf @method('PATCH')
                    @php
                        $nextStatus = match($post->status) {
                            'draft'  => 'ready',
                            'ready'  => 'posted',
                            'posted' => 'draft',
                        };
                        $statusLabel = match($post->status) {
                            'draft'  => 'Mark Ready',
                            'ready'  => 'Mark Posted',
                            'posted' => 'Reopen Draft',
                        };
                        $statusBadge = match($post->status) {
                            'draft'  => 'bg-gray-100 text-gray-500',
                            'ready'  => 'bg-amber-50 text-amber-700',
                            'posted' => 'bg-matcha-100 text-matcha-700',
                        };
                    @endphp
                    <input type="hidden" name="status" value="{{ $nextStatus }}" />
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $statusBadge }}">
                            {{ ucfirst($post->status) }}
                        </span>
                        <button type="submit"
                                class="text-xs text-matcha-600 hover:text-matcha-800 font-medium transition whitespace-nowrap">
                            {{ $statusLabel }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- Reach Angle --}}
            @if ($post->reachAngle || $angles->count())
            <div class="mt-3 pt-3 border-t border-gray-100"
                 x-data="{ editAngle: false }">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-400">Angle:</span>
                        @if ($post->reachAngle)
                            <span class="text-xs font-medium text-matcha-700 bg-matcha-50 px-2 py-0.5 rounded-full">
                                {{ $post->reachAngle->title }}
                            </span>
                            @if ($post->reachAngle->target_segment)
                                <span class="text-xs text-gray-400">· {{ $post->reachAngle->target_segment }}</span>
                            @endif
                        @else
                            <span class="text-xs text-gray-400 italic">None</span>
                        @endif
                    </div>
                    <button @click="editAngle = !editAngle"
                            class="text-xs text-gray-400 hover:text-matcha-600 transition">
                        <span x-show="!editAngle">Change</span>
                        <span x-show="editAngle" x-cloak>Cancel</span>
                    </button>
                </div>
                <div x-show="editAngle" x-cloak class="mt-2">
                    <form method="POST" action="{{ route('daily-posts.update', $post) }}" class="flex items-center gap-2">
                        @csrf @method('PATCH')
                        <select name="reach_angle_id"
                                class="flex-1 text-sm border-gray-200 rounded-lg px-3 py-1.5 focus:ring-matcha-400 focus:border-matcha-400">
                            <option value="">No angle</option>
                            @foreach ($angles as $angle)
                                <option value="{{ $angle->id }}" {{ $post->reach_angle_id == $angle->id ? 'selected' : '' }}>
                                    {{ $angle->title }}{{ $angle->target_segment ? ' · ' . $angle->target_segment : '' }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit"
                                class="text-xs bg-matcha-600 hover:bg-matcha-800 text-white font-medium px-3 py-1.5 rounded-lg transition whitespace-nowrap">
                            Save
                        </button>
                    </form>
                </div>
            </div>
            @endif

            {{-- Generate button --}}
            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                <p class="text-xs text-gray-400">
                    {{ $post->hasContent() ? 'Content generated · regenerate to get a fresh version' : 'No content yet — click generate to write the caption and image prompt.' }}
                </p>
                <form method="POST" action="{{ route('daily-posts.generate', $post) }}">
                    @csrf
                    <button type="submit"
                            class="bg-matcha-600 hover:bg-matcha-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                        ✦ {{ $post->hasContent() ? 'Regenerate' : 'Generate' }}
                    </button>
                </form>
            </div>
        </div>

        @if ($post->hasContent())

            {{-- Caption --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Caption</h3>
                    <button @click="navigator.clipboard.writeText($refs.caption.innerText); copied = 'caption'"
                            class="text-xs text-matcha-600 hover:text-matcha-800 font-medium transition">
                        <span x-show="copied !== 'caption'">Copy</span>
                        <span x-show="copied === 'caption'" x-cloak>Copied ✓</span>
                    </button>
                </div>
                <p x-ref="caption" class="text-sm text-gray-800 whitespace-pre-line leading-relaxed">{{ $post->caption }}</p>
            </div>

            {{-- Image Prompts --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Image Prompts</h3>
                <p class="text-xs text-gray-400 mb-4">Pick one and paste into Canva AI, DALL-E, or any image generator.</p>
                @php $labels = ['Neutral', 'Emotional']; @endphp
                <div class="space-y-3">
                    @foreach ($post->image_prompt as $i => $prompt)
                        <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-50 border border-gray-100">
                            <span class="text-xs font-semibold px-1.5 py-0.5 rounded mt-0.5 flex-shrink-0
                                {{ $i === 0 ? 'bg-gray-200 text-gray-600' : 'bg-strawberry-50 text-strawberry-600' }}">
                                {{ $labels[$i] }}
                            </span>
                            <p class="flex-1 text-sm text-gray-600 italic leading-relaxed" x-ref="img{{ $i }}">{{ $prompt }}</p>
                            <button @click="navigator.clipboard.writeText($refs.img{{ $i }}.innerText); copied = 'img{{ $i }}'"
                                    class="flex-shrink-0 text-xs text-matcha-600 hover:text-matcha-800 font-medium transition whitespace-nowrap">
                                <span x-show="copied !== 'img{{ $i }}'">Copy</span>
                                <span x-show="copied === 'img{{ $i }}'" x-cloak>Copied ✓</span>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Edit caption manually --}}
            <details class="bg-white rounded-xl border border-gray-200 p-5">
                <summary class="text-xs text-gray-400 cursor-pointer hover:text-gray-600 select-none">Edit caption manually</summary>
                <form method="POST" action="{{ route('daily-posts.update', $post) }}" class="mt-4 space-y-3">
                    @csrf @method('PATCH')
                    <textarea name="caption" rows="5"
                              class="w-full text-sm border-gray-200 rounded-lg px-3 py-2 focus:ring-matcha-400 focus:border-matcha-400">{{ $post->caption }}</textarea>
                    <div class="flex justify-end">
                        <button type="submit"
                                class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium px-4 py-2 rounded-lg transition">
                            Save Caption
                        </button>
                    </div>
                </form>
            </details>

        @endif

        {{-- Delete --}}
        <div class="flex justify-end pt-2">
            <form method="POST" action="{{ route('daily-posts.destroy', $post) }}"
                  onsubmit="return confirm('Delete this post?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-xs text-gray-400 hover:text-strawberry-600 transition">
                    Delete post
                </button>
            </form>
        </div>

    </div>

</x-app-layout>
