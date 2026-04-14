<x-app-layout>
    <x-slot name="title">Reach Angles · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">Reach Angles</x-slot>
    <x-slot name="actions">
        <a href="{{ route('angles.create') }}"
           class="bg-matcha-600 hover:bg-matcha-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            + Add Angle
        </a>
    </x-slot>

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
                        <div class="ml-4 text-right flex-shrink-0">
                            <p class="text-xl font-bold text-matcha-700">{{ $angle->clients_count }}</p>
                            <p class="text-xs text-gray-400">reached</p>
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

</x-app-layout>
