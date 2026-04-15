<x-app-layout>
    <x-slot name="title">Reach Angles · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">Reach Angles</x-slot>
    <x-slot name="actions">
        <a href="{{ route('angles.create') }}"
           class="bg-matcha-600 hover:bg-matcha-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            + Add Angle
        </a>
    </x-slot>

    {{-- AI Generate Modal --}}
    <div x-data="genModal()"
         x-show="show"
         x-cloak
         @keydown.escape.window="close()"
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/50" @click="close()"></div>

        {{-- Panel --}}
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[85vh] flex flex-col">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
                <div>
                    <h2 class="text-sm font-semibold text-gray-800" x-text="'✨ ' + angleTitle"></h2>
                    <p class="text-xs text-gray-400 mt-0.5">Generate content for this angle</p>
                </div>
                <button @click="close()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="flex-1 overflow-y-auto px-6 py-4 space-y-5">

                {{-- Error --}}
                <div x-show="error" class="bg-strawberry-50 border border-strawberry-200 text-strawberry-700 text-sm rounded-lg px-4 py-3" x-text="error"></div>

                {{-- Pinned contents --}}
                <template x-if="pinned.length > 0">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">📌 Pinned</p>
                        <div class="space-y-2">
                            <template x-for="c in pinned" :key="c.id">
                                <div class="bg-matcha-50 border border-matcha-100 rounded-lg p-3">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs font-medium text-matcha-700 capitalize" x-text="c.style"></span>
                                        <button @click="unpin(c)" class="text-xs text-gray-400 hover:text-strawberry-500 transition">Unpin</button>
                                    </div>
                                    <p class="text-sm text-gray-700 leading-relaxed" x-text="c.content"></p>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                {{-- Fresh results --}}
                <template x-if="fresh.length > 0">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">New Generation</p>
                        <div class="grid grid-cols-1 gap-3">
                            <template x-for="(c, i) in fresh" :key="c.id">
                                <div class="bg-white border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-semibold uppercase tracking-wide"
                                              :class="{
                                                  'text-blue-600': c.style === 'casual',
                                                  'text-purple-600': c.style === 'story',
                                                  'text-amber-600': c.style === 'factual'
                                              }"
                                              x-text="styleLabel(c.style)"></span>
                                        <button @click="togglePin(c, i)"
                                                :class="c.is_pinned ? 'text-matcha-600' : 'text-gray-300 hover:text-matcha-400'"
                                                class="transition text-lg leading-none">
                                            📌
                                        </button>
                                    </div>
                                    <p class="text-sm text-gray-700 leading-relaxed" x-text="c.content"></p>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                {{-- Empty state --}}
                <template x-if="fresh.length === 0 && pinned.length === 0 && !generating">
                    <div class="text-center py-8">
                        <p class="text-sm text-gray-400">Hit Generate to create 3 content variations for this angle.</p>
                    </div>
                </template>

            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-gray-100 flex-shrink-0 flex items-center gap-3">
                <button @click="generate()"
                        :disabled="generating"
                        class="flex items-center gap-2 bg-matcha-600 hover:bg-matcha-800 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                    <span x-show="generating">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                        </svg>
                    </span>
                    <span x-text="generating ? 'Generating...' : (fresh.length > 0 ? '↩ Regenerate' : '✨ Generate')"></span>
                </button>
                <p class="text-xs text-gray-400">5-min cooldown between generations</p>
            </div>
        </div>
    </div>

    {{-- Angles Grid --}}
    @if ($angles->count())
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($angles as $angle)
                <div class="bg-white rounded-xl border border-gray-200 p-5" x-data="{ del: false }">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-semibold text-gray-800">{{ $angle->title }}</h3>
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                    {{ $angle->status === 'active' ? 'bg-matcha-50 text-matcha-700' :
                                       ($angle->status === 'paused' ? 'bg-amber-50 text-amber-600' : 'bg-gray-100 text-gray-500') }}">
                                    {{ ucfirst($angle->status) }}
                                </span>
                            </div>
                            @if ($angle->target_segment)
                                <p class="text-xs text-gray-400 mt-0.5">{{ $angle->target_segment }}</p>
                            @endif
                            @if ($angle->description)
                                <p class="text-sm text-gray-600 mt-2 leading-relaxed">{{ $angle->description }}</p>
                            @endif
                        </div>
                        <div class="ml-4 flex-shrink-0 flex flex-col items-end gap-2">
                            <div class="text-right">
                                <p class="text-xl font-bold text-matcha-700">{{ $angle->clients_count }}</p>
                                <p class="text-xs text-gray-400">reached</p>
                            </div>
                            <button @click="$dispatch('open-gen', {
                                        id: {{ $angle->id }},
                                        title: '{{ addslashes($angle->title) }}',
                                        pinned: {{ $angle->pinnedContents->toJson() }}
                                    })"
                                    class="text-lg leading-none hover:scale-110 transition-transform"
                                    title="Generate content">
                                ✨
                            </button>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center gap-3 border-t border-gray-100 pt-3">
                        <a href="{{ route('angles.edit', $angle) }}"
                           class="text-xs text-matcha-600 hover:underline">Edit</a>
                        <div x-data="{ del: false }">
                            <button type="button" @click="del = true" x-show="!del"
                                    class="text-xs text-strawberry-400 hover:text-strawberry-600">Delete</button>
                            <div x-show="del" class="flex items-center gap-2">
                                <span class="text-xs text-gray-500">Sure?</span>
                                <form method="POST" action="{{ route('angles.destroy', $angle) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-strawberry-600 font-medium hover:underline">Yes</button>
                                </form>
                                <button @click="del = false" class="text-xs text-gray-400">No</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 px-5 py-12 text-center">
            <p class="text-sm text-gray-400">No reach angles yet. <a href="{{ route('angles.create') }}" class="text-matcha-600 hover:underline">Add your first angle.</a></p>
        </div>
    @endif

    <script>
        function genModal() {
            return {
                show: false,
                angleId: null,
                angleTitle: '',
                pinned: [],
                fresh: [],
                generating: false,
                error: null,

                init() {
                    window.addEventListener('open-gen', (e) => {
                        this.angleId    = e.detail.id;
                        this.angleTitle = e.detail.title;
                        this.pinned     = e.detail.pinned;
                        this.fresh      = [];
                        this.error      = null;
                        this.show       = true;
                    });
                },

                close() {
                    this.show = false;
                },

                async generate() {
                    this.generating = true;
                    this.error      = null;
                    try {
                        const res = await fetch(`/angles/${this.angleId}/generate`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                        });
                        const data = await res.json();
                        if (! data.success) throw new Error(data.message);
                        this.fresh = data.contents;
                    } catch (e) {
                        this.error = e.message;
                    } finally {
                        this.generating = false;
                    }
                },

                async togglePin(content, index) {
                    const res = await fetch(`/angle-contents/${content.id}/pin`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                    });
                    const data = await res.json();
                    this.fresh[index].is_pinned = data.is_pinned;

                    if (data.is_pinned) {
                        this.pinned.push({ ...content, is_pinned: true });
                    }
                },

                async unpin(content) {
                    const res = await fetch(`/angle-contents/${content.id}/pin`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                    });
                    const data = await res.json();
                    if (! data.is_pinned) {
                        this.pinned = this.pinned.filter(c => c.id !== content.id);
                    }
                },

                styleLabel(style) {
                    return { casual: '💬 Casual', story: '📖 Story', factual: '📊 Factual' }[style] ?? style;
                },
            };
        }
    </script>

</x-app-layout>
