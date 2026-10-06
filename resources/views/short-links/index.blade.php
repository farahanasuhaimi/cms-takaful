<x-app-layout>
    <x-slot name="title">Short Links · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">Short Links</x-slot>
    <x-slot name="actions">
        <a href="{{ route('short-links.create') }}"
           class="bg-matcha-600 hover:bg-matcha-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            + Add Link
        </a>
    </x-slot>

    @if ($links->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 px-5 py-12 text-center">
            <p class="text-sm text-gray-400">No short links yet. <a href="{{ route('short-links.create') }}" class="text-matcha-600 hover:underline">Add your first link.</a></p>
        </div>
    @else
        <div x-data="{ q: '' }" class="space-y-6">

            <input type="search" x-model="q" placeholder="Search code, title or notes…"
                   class="w-full sm:max-w-sm text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400" />

            @foreach ($links as $section => $group)
                @php
                    $haystacks = $group->map(fn ($l) => mb_strtolower("{$l->section} {$l->code} {$l->title} {$l->target} {$l->notes}"))->values();
                @endphp
                <div x-data="{ items: {{ json_encode($haystacks, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) }} }"
                     x-show="!q || items.some(s => s.includes(q.toLowerCase()))">
                    <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                        {{ $section }} <span class="text-gray-300 font-normal">· {{ $group->count() }}</span>
                    </h2>

                    <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100">
                        @foreach ($group as $i => $link)
                            <div x-data="{ copied: false }"
                                 x-show="!q || items[{{ $loop->index }}].includes(q.toLowerCase())"
                                 class="flex flex-col sm:flex-row sm:items-start gap-2 sm:gap-4 px-4 py-3">

                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-800">{{ $link->title }}</p>
                                    <a href="{{ $link->url }}" target="_blank" rel="noopener"
                                       class="text-xs text-matcha-600 hover:underline break-all">{{ $link->url }}</a>
                                    @if ($link->notes)
                                        <p class="mt-1 text-xs text-gray-500 whitespace-pre-line">{{ $link->notes }}</p>
                                    @endif
                                    @if ($link->target)
                                        <p class="mt-1 text-xs text-gray-300">→ {{ $link->target }}</p>
                                    @endif
                                </div>

                                <div class="flex items-center gap-3 flex-shrink-0">
                                    <button type="button"
                                            @click="navigator.clipboard.writeText({{ json_encode($link->url, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) }}); copied = true; setTimeout(() => copied = false, 2000)"
                                            class="text-xs font-medium px-3 py-1.5 rounded-lg border transition"
                                            :class="copied ? 'bg-matcha-600 border-matcha-600 text-white' : 'border-matcha-200 text-matcha-700 hover:bg-matcha-50'">
                                        <span x-text="copied ? 'Copied!' : 'Copy'">Copy</span>
                                    </button>
                                    <a href="{{ route('short-links.edit', $link) }}"
                                       class="text-xs text-gray-400 hover:text-matcha-600">Edit</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

        </div>
    @endif

</x-app-layout>
