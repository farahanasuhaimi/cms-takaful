<x-app-layout>
    <x-slot name="title">Task Board · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">Task Board</x-slot>

    @php
        $columnMeta = [
            'backlog' => ['label' => 'Backlog', 'accent' => 'gray',   'dot' => 'bg-gray-400'],
            'today'   => ['label' => 'Today',   'accent' => 'amber',  'dot' => 'bg-amber-400'],
            'doing'   => ['label' => 'Doing',   'accent' => 'indigo', 'dot' => 'bg-indigo-400'],
            'done'    => ['label' => 'Done',    'accent' => 'matcha', 'dot' => 'bg-matcha-400'],
        ];

        $initialColumns = collect($columnMeta)->keys()->mapWithKeys(fn ($status) => [
            $status => $columns[$status]->map(fn ($t) => [
                'id'           => $t->id,
                'title'        => $t->title,
                'source_type'  => $t->source_type,
                'source_label' => \App\Models\Task::SOURCE_LABELS[$t->source_type] ?? null,
                'source_url'   => $t->source_url,
            ])->values(),
        ]);
    @endphp

    <div x-data="kanbanBoard(
            {{ $initialColumns->toJson() }},
            '{{ route('tasks.reorder') }}',
            '{{ csrf_token() }}'
        )"
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-start">

        @foreach ($columnMeta as $status => $meta)
            <div class="bg-white rounded-xl border border-gray-200 flex flex-col max-h-[calc(100vh-8rem)]">

                {{-- Column header --}}
                <div class="flex items-center gap-2 px-4 py-3 border-b border-gray-100 flex-shrink-0">
                    <span class="w-2 h-2 rounded-full {{ $meta['dot'] }}"></span>
                    <h2 class="text-sm font-semibold text-gray-700">{{ $meta['label'] }}</h2>
                    <span class="ml-auto text-xs font-medium text-gray-400" x-text="columns.{{ $status }}.length"></span>
                </div>

                {{-- Drop zone --}}
                <div class="flex-1 overflow-y-auto p-2 space-y-2 min-h-[8rem]"
                     @dragover.prevent
                     @drop="onDrop('{{ $status }}', null)">
                    <template x-for="(task, index) in columns.{{ $status }}" :key="task.id">
                        <div draggable="true"
                             @dragstart="onDragStart(task, '{{ $status }}')"
                             @dragover.prevent.stop
                             @drop.stop="onDrop('{{ $status }}', index)"
                             class="group bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 cursor-move flex items-start justify-between gap-2 transition">
                            <div class="min-w-0">
                                <span x-show="task.source_label"
                                      x-text="task.source_label"
                                      class="inline-block mb-1 text-[10px] font-semibold uppercase tracking-wide text-matcha-700 bg-matcha-50 rounded px-1.5 py-0.5"></span>
                                <a x-show="task.source_url" x-cloak
                                   :href="task.source_url" target="_blank" rel="noopener" @click.stop
                                   x-text="task.title" class="break-words text-matcha-700 hover:underline"
                                   :class="task.source_type && $store.privacy.enabled ? 'blur-sm select-none pointer-events-none' : ''"></a>
                                <p x-show="!task.source_url" x-text="task.title" class="break-words"
                                   :class="task.source_type && $store.privacy.enabled ? 'blur-sm select-none' : ''"></p>
                            </div>
                            <form :action="'/tasks/' + task.id" method="POST"
                                  onsubmit="return confirm('Delete this task?')"
                                  class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-gray-300 hover:text-strawberry-500 text-xs leading-none">✕</button>
                            </form>
                        </div>
                    </template>
                    <p x-show="columns.{{ $status }}.length === 0" class="text-xs text-gray-300 text-center py-4">
                        Nothing here
                    </p>
                </div>

                {{-- Quick add --}}
                <form method="POST" action="{{ route('tasks.store') }}" class="p-2 border-t border-gray-100 flex-shrink-0">
                    @csrf
                    <input type="hidden" name="status" value="{{ $status }}">
                    <input type="text" name="title" required maxlength="255"
                           placeholder="+ Add task"
                           class="w-full text-sm border-gray-200 rounded-lg px-3 py-1.5 focus:ring-matcha-400 focus:border-matcha-400" />
                </form>

            </div>
        @endforeach

    </div>
</x-app-layout>
