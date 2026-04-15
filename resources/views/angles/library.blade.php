<x-app-layout>
    <x-slot name="title">Content Library · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">Content Library</x-slot>

    <div x-data="{ filter: 'all' }">

        {{-- Filter bar — horizontal scroll on mobile --}}
        <div class="flex items-center gap-2 overflow-x-auto pb-1 mb-5 -mx-6 px-6 lg:mx-0 lg:px-0">
            @foreach (['all' => 'All', 'casual' => '💬 Casual', 'story' => '📖 Story', 'factual' => '📊 Factual'] as $val => $label)
                <button @click="filter = '{{ $val }}'"
                        :class="filter === '{{ $val }}' ? 'bg-matcha-600 text-white' : 'bg-white text-gray-600 border border-gray-200'"
                        class="flex-shrink-0 text-xs font-medium px-3 py-1.5 rounded-full transition whitespace-nowrap">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Cards --}}
        @if ($pinned->isEmpty())
            <div class="bg-white rounded-xl border border-gray-200 px-5 py-12 text-center">
                <p class="text-sm text-gray-400">No pinned content yet. Go to <a href="{{ route('angles.index') }}" class="text-matcha-600 hover:underline">Reach Angles</a>, generate content, and 📌 pin what you want to keep.</p>
            </div>
        @else
            <div class="space-y-6">
                @foreach ($pinned as $angleId => $contents)
                    @php $angle = $contents->first()->angle @endphp
                    <div x-show="filter === 'all' || {{ $contents->pluck('style')->map(fn($s) => "filter === '$s'")->join(' || ') }}">

                        {{-- Angle heading --}}
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 mb-2">
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $angle->title }}</h3>
                            @if ($angle->target_segment)
                                <span class="text-xs text-gray-400">· {{ $angle->target_segment }}</span>
                            @endif
                        </div>

                        {{-- Content cards --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach ($contents as $content)
                                <div x-data="{ copied: false, visible: true }"
                                     x-show="visible && (filter === 'all' || filter === '{{ $content->style }}')"
                                     class="bg-white border border-gray-200 rounded-xl p-4">

                                    {{-- Card header --}}
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="text-xs font-semibold uppercase tracking-wide
                                            {{ $content->style === 'casual' ? 'text-blue-600' : ($content->style === 'story' ? 'text-purple-600' : 'text-amber-600') }}">
                                            {{ ['casual' => '💬 Casual', 'story' => '📖 Story', 'factual' => '📊 Factual'][$content->style] }}
                                        </span>
                                        <div class="flex items-center gap-3">
                                            {{-- Copy --}}
                                            <button @click="navigator.clipboard.writeText({{ json_encode($content->content) }}); copied = true; setTimeout(() => copied = false, 2000)"
                                                    class="text-xs font-medium transition min-w-[40px] text-right"
                                                    :class="copied ? 'text-matcha-600' : 'text-gray-400'">
                                                <span x-text="copied ? '✓ Copied' : 'Copy'"></span>
                                            </button>
                                            {{-- Unpin --}}
                                            <button @click="fetch('{{ route('angle-contents.pin', $content) }}', {
                                                        method: 'PATCH',
                                                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
                                                    }).then(() => visible = false)"
                                                    class="text-base text-gray-300 hover:text-strawberry-400 transition leading-none p-1 -mr-1"
                                                    title="Unpin">
                                                📌
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Content --}}
                                    <p class="text-sm text-gray-700 leading-relaxed">{{ $content->content }}</p>

                                    {{-- Meta --}}
                                    <p class="text-xs text-gray-300 mt-3">{{ $content->created_at->format('d M Y') }} · {{ $angle->title }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>

</x-app-layout>
