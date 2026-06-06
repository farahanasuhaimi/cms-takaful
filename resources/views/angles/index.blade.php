<x-app-layout>
    <x-slot name="title">Reach Angles · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">Reach Angles</x-slot>
    <x-slot name="actions">
        <a href="{{ route('angles.create') }}"
           class="bg-matcha-600 hover:bg-matcha-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            + Add Angle
        </a>
    </x-slot>

    @if ($angles->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 px-5 py-16 text-center">
            <p class="text-sm text-gray-400">No reach angles yet.</p>
            <p class="text-xs text-gray-400 mt-1">
                <a href="{{ route('angles.create') }}" class="text-matcha-600 hover:underline">Add your first angle.</a>
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($angles as $angle)
                <a href="{{ route('angles.show', $angle) }}"
                   class="bg-white rounded-xl border border-gray-200 p-5 flex flex-col gap-3 hover:border-matcha-300 transition group">

                    {{-- Header --}}
                    <div class="flex items-start justify-between gap-2">
                        <p class="font-semibold text-gray-800 leading-snug group-hover:text-matcha-700 transition">
                            {{ $angle->title }}
                        </p>
                        <span class="flex-shrink-0 text-xs font-medium px-2 py-0.5 rounded-full
                            {{ $angle->status === 'active' ? 'bg-matcha-50 text-matcha-700' :
                               ($angle->status === 'paused' ? 'bg-amber-50 text-amber-600' : 'bg-gray-100 text-gray-400') }}">
                            {{ ucfirst($angle->status) }}
                        </span>
                    </div>

                    {{-- Target segment --}}
                    @if ($angle->target_segment)
                        <p class="text-xs text-gray-400 -mt-1">{{ $angle->target_segment }}</p>
                    @endif

                    {{-- Description --}}
                    @if ($angle->description)
                        <p class="text-xs text-gray-500 leading-relaxed line-clamp-2">{{ $angle->description }}</p>
                    @endif

                    {{-- Footer counts --}}
                    <div class="flex items-center gap-3 mt-auto pt-3 border-t border-gray-50 text-xs text-gray-400">
                        @if ($angle->leads_count)
                            <span>{{ $angle->leads_count }} lead{{ $angle->leads_count !== 1 ? 's' : '' }}</span>
                        @endif
                        @if ($angle->clients_count)
                            <span>{{ $angle->clients_count }} client{{ $angle->clients_count !== 1 ? 's' : '' }}</span>
                        @endif
                        @if ($angle->usages_count)
                            <span>{{ $angle->usages_count }} use{{ $angle->usages_count !== 1 ? 's' : '' }}</span>
                        @endif
                        @if ($angle->pinned_count)
                            <span class="ml-auto text-matcha-500">📌 {{ $angle->pinned_count }}</span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    @endif

</x-app-layout>
