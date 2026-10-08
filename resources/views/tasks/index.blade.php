<x-app-layout>
    <x-slot name="title">Task Board · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">Task Board</x-slot>

    @php
        $columnMeta = [
            'backlog' => ['label' => 'Backlog', 'dot' => 'bg-gray-400',   'hint' => 'Most urgent on top',
                          'empty' => 'All clear. Follow-ups, hot leads and check-ins land here by themselves.'],
            'today'   => ['label' => 'Today',   'dot' => 'bg-amber-400',  'hint' => 'Unfinished cards return to Backlog tomorrow',
                          'empty' => 'Nothing planned yet. Pull 2 or 3 cards from Backlog.'],
            'doing'   => ['label' => 'Doing',   'dot' => 'bg-indigo-400', 'hint' => null,
                          'empty' => 'Tap Start on a Today card.'],
            'done'    => ['label' => 'Done',    'dot' => 'bg-matcha-400', 'hint' => "Last {$doneVisibleDays} days",
                          'empty' => 'Nothing finished yet today.'],
        ];

        $filters = ['all' => 'All'] + \App\Models\Task::SOURCE_LABELS + ['mine' => 'Mine'];
    @endphp

    <div x-data="kanbanBoard({
            tasks: @js($tasks),
            archivedDone: {{ (int) $archivedDone }},
            csrf: '{{ csrf_token() }}',
            urls: {
                store: '{{ route('tasks.store') }}',
                reorder: '{{ route('tasks.reorder') }}',
                task: (id) => '{{ url('tasks') }}/' + id,
            },
         })"
         class="space-y-4">

        {{-- Where the day stands --}}
        <div class="flex flex-wrap items-center gap-2 text-xs">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-white border border-gray-200 px-3 py-1 text-gray-600">
                <span class="font-semibold text-gray-800" x-text="count('today') + count('doing')"></span> planned today
            </span>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-white border border-gray-200 px-3 py-1 text-gray-600">
                <span class="font-semibold text-matcha-800" x-text="doneToday().length"></span> done today
            </span>
            <span x-show="overdueCount() > 0" x-cloak
                  class="inline-flex items-center gap-1.5 rounded-full bg-strawberry-50 border border-strawberry-200 px-3 py-1 text-strawberry-800">
                <span class="font-semibold" x-text="overdueCount()"></span> overdue
            </span>
        </div>

        {{-- Nudge when the day hasn't been planned --}}
        <div x-show="count('today') + count('doing') === 0 && count('backlog') > 0" x-cloak
             class="flex flex-wrap items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            <p class="flex-1 min-w-[12rem]">
                Nothing planned for today. Backlog is sorted with the most urgent first. Tap <strong>→ Today</strong> on 2 or 3 cards.
            </p>
            <button type="button" @click="tab = 'backlog'"
                    class="lg:hidden text-xs font-semibold rounded-md bg-white border border-amber-300 px-3 py-1.5 hover:bg-amber-100">
                Open Backlog
            </button>
        </div>

        {{-- Save errors --}}
        <div x-show="error" x-cloak
             class="flex items-start gap-3 rounded-lg border border-strawberry-200 bg-strawberry-100 px-4 py-3 text-sm text-strawberry-800">
            <p class="flex-1" x-text="error"></p>
            <button type="button" @click="error = null" class="text-strawberry-600 hover:text-strawberry-800" aria-label="Dismiss">✕</button>
        </div>

        {{-- Column tabs (phones and tablets) --}}
        <div class="lg:hidden sticky top-0 z-[5] grid grid-cols-4 gap-1 rounded-xl border border-gray-200 bg-white p-1" role="tablist">
            @foreach ($columnMeta as $status => $meta)
                <button type="button" role="tab" @click="tab = '{{ $status }}'"
                        :aria-selected="(tab === '{{ $status }}').toString()"
                        class="flex items-center justify-center gap-1.5 rounded-lg py-2 text-xs font-semibold transition"
                        :class="tab === '{{ $status }}' ? 'bg-matcha-800 text-white' : 'text-gray-500 hover:bg-gray-50'">
                    {{ $meta['label'] }}
                    <span class="tabular-nums text-[10px] font-medium rounded-full px-1.5"
                          :class="tab === '{{ $status }}' ? 'bg-white/20' : 'bg-gray-100'"
                          x-text="count('{{ $status }}')"></span>
                </button>
            @endforeach
        </div>

        {{-- Board --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 items-start">
            @foreach ($columnMeta as $status => $meta)
                <section class="lg:flex flex-col rounded-xl border bg-gray-50/60 lg:max-h-[calc(100dvh-13rem)] transition"
                         :class="[
                            tab === '{{ $status }}' ? 'flex' : 'hidden',
                            dropTarget === '{{ $status }}' ? 'border-matcha-400 ring-2 ring-matcha-200' : 'border-gray-200',
                         ]"
                         @dragover.prevent="dropTarget = '{{ $status }}'"
                         @dragleave.self="dropTarget = null"
                         @drop.prevent="onDrop('{{ $status }}', null)">

                    {{-- Header --}}
                    <header class="flex-shrink-0 px-4 pt-3 pb-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full {{ $meta['dot'] }}"></span>
                            <h2 class="text-sm font-semibold text-gray-700">{{ $meta['label'] }}</h2>
                            <span class="ml-auto text-xs font-medium text-gray-400 tabular-nums" x-text="count('{{ $status }}')"></span>
                        </div>
                        @if ($meta['hint'])
                            <p class="mt-0.5 text-[11px] text-gray-400">{{ $meta['hint'] }}</p>
                        @endif

                        @if ($status === 'backlog')
                            <div class="mt-2 -mx-1 flex gap-1 overflow-x-auto pb-1">
                                @foreach ($filters as $key => $label)
                                    <button type="button" @click="filter = '{{ $key }}'"
                                            x-show="'{{ $key }}' === 'all' || filterCount('{{ $key }}') > 0"
                                            class="flex-shrink-0 rounded-full border px-2.5 py-0.5 text-[11px] font-medium transition"
                                            :class="filter === '{{ $key }}'
                                                ? 'border-matcha-800 bg-matcha-800 text-white'
                                                : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300'">
                                        {{ $label }} <span class="tabular-nums opacity-70" x-text="filterCount('{{ $key }}')"></span>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </header>

                    {{-- Cards --}}
                    <div class="flex-1 overflow-y-auto px-2 pb-2 space-y-2 min-h-[6rem]">
                        @if ($status === 'done')
                            <template x-for="task in doneToday()" :key="task.id">
                                @include('tasks._card')
                            </template>

                            <template x-if="doneEarlier().length > 0">
                                <div class="pt-1">
                                    <button type="button" @click="showEarlierDone = !showEarlierDone"
                                            class="w-full flex items-center gap-2 px-2 py-1.5 text-xs font-medium text-gray-500 hover:text-gray-700">
                                        <span x-text="showEarlierDone ? '▾' : '▸'"></span>
                                        Earlier this week
                                        <span class="tabular-nums" x-text="'(' + doneEarlier().length + ')'"></span>
                                    </button>
                                    <div x-show="showEarlierDone" class="space-y-2">
                                        <template x-for="task in doneEarlier()" :key="task.id">
                                            @include('tasks._card')
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <p x-show="doneToday().length === 0" class="text-xs text-gray-400 text-center py-4">{{ $meta['empty'] }}</p>
                            <p x-show="archivedDone > 0" x-cloak class="text-[11px] text-gray-400 text-center">
                                <span x-text="archivedDone"></span> older done cards are hidden.
                            </p>
                        @else
                            <template x-for="task in column('{{ $status }}')" :key="task.id">
                                @include('tasks._card')
                            </template>

                            <p x-show="column('{{ $status }}').length === 0" class="text-xs text-gray-400 text-center py-4 px-4">
                                @if ($status === 'backlog')
                                    <span x-show="filter !== 'all'">Nothing in this filter.</span>
                                    <span x-show="filter === 'all'">{{ $meta['empty'] }}</span>
                                @else
                                    {{ $meta['empty'] }}
                                @endif
                            </p>
                        @endif
                    </div>

                    {{-- Quick add --}}
                    @if ($status !== 'done')
                        <form @submit.prevent="add('{{ $status }}')" class="flex-shrink-0 p-2 border-t border-gray-200">
                            <input type="text" x-model="newTitle.{{ $status }}" maxlength="255"
                                   placeholder="+ Add to {{ $meta['label'] }}"
                                   class="w-full text-sm bg-white border-gray-200 rounded-lg px-3 py-1.5 focus:ring-matcha-400 focus:border-matcha-400" />
                        </form>
                    @endif
                </section>
            @endforeach
        </div>

        {{-- Details sheet: bottom sheet on phones, dialog on desktop --}}
        <div x-show="sheet" x-cloak @keydown.escape.window="close()"
             class="fixed inset-0 z-40 flex items-end sm:items-center justify-center sm:p-4">
            <div class="absolute inset-0 bg-black/40" @click="close()"></div>

            <template x-if="sheet">
                <form @submit.prevent="saveSheet()" role="dialog" aria-modal="true" aria-label="Task details"
                      class="relative w-full sm:max-w-lg max-h-[90dvh] overflow-y-auto bg-white rounded-t-2xl sm:rounded-2xl shadow-xl p-5 pb-[calc(1.25rem+env(safe-area-inset-bottom))] space-y-4">

                    <div class="flex items-center gap-2">
                        <span x-show="sheet.source_label" x-text="sheet.source_label"
                              class="text-[10px] font-semibold uppercase tracking-wide text-matcha-800 bg-matcha-100 rounded px-1.5 py-0.5"></span>
                        <span x-show="!sheet.source_label" class="text-[10px] font-semibold uppercase tracking-wide text-gray-500">Your task</span>
                        <button type="button" @click="close()" class="ml-auto text-gray-400 hover:text-gray-700 p-1" aria-label="Close">✕</button>
                    </div>

                    {{-- Title --}}
                    <div>
                        <template x-if="sheet.source_type">
                            <div>
                                <p class="text-base font-medium text-gray-800 break-words" x-text="sheet.title"
                                   :class="$store.privacy.enabled ? 'blur-sm select-none' : ''"></p>
                                <p class="mt-1 text-xs text-gray-500">
                                    Made from your CRM records. It updates itself and clears once the follow-up is done.
                                    <a x-show="sheet.source_url" :href="sheet.source_url" target="_blank" rel="noopener"
                                       class="font-medium text-matcha-800 hover:underline">Open record →</a>
                                </p>
                            </div>
                        </template>
                        <template x-if="!sheet.source_type">
                            <div>
                                <label for="task-title" class="block text-xs font-medium text-gray-500 mb-1">Task</label>
                                <input id="task-title" type="text" x-model="sheet.title" required maxlength="255"
                                       class="w-full text-sm border-gray-300 rounded-lg focus:ring-matcha-400 focus:border-matcha-400">
                            </div>
                        </template>
                    </div>

                    {{-- Status --}}
                    <div>
                        <p class="text-xs font-medium text-gray-500 mb-1">Column</p>
                        <div class="grid grid-cols-4 gap-1 rounded-lg bg-gray-100 p-1">
                            @foreach ($columnMeta as $status => $meta)
                                <button type="button" @click="sheet.status = '{{ $status }}'"
                                        class="rounded-md py-1.5 text-xs font-semibold transition"
                                        :class="sheet.status === '{{ $status }}' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'">
                                    {{ $meta['label'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Due date + priority --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="task-due" class="block text-xs font-medium text-gray-500 mb-1">Due date</label>
                            <template x-if="!sheet.source_type">
                                <div class="flex items-center gap-2">
                                    <input id="task-due" type="date" x-model="sheet.due_date"
                                           class="flex-1 text-sm border-gray-300 rounded-lg focus:ring-matcha-400 focus:border-matcha-400">
                                    <button type="button" x-show="sheet.due_date" @click="sheet.due_date = ''"
                                            class="text-xs text-gray-400 hover:text-gray-700">Clear</button>
                                </div>
                            </template>
                            <template x-if="sheet.source_type">
                                <p class="text-sm text-gray-700 py-2"
                                   x-text="sheet.due_date
                                       ? new Date(sheet.due_date + 'T00:00').toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })
                                       : 'No date set'"></p>
                            </template>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-1">Priority</p>
                            <label class="flex items-center gap-2 py-2 text-sm text-gray-700 cursor-pointer">
                                <input type="checkbox" x-model="sheet.is_priority"
                                       class="rounded border-gray-300 text-amber-500 focus:ring-amber-400">
                                Pin to the top of Backlog
                            </label>
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label for="task-notes" class="block text-xs font-medium text-gray-500 mb-1">Notes</label>
                        <textarea id="task-notes" x-model="sheet.notes" rows="4" maxlength="5000"
                                  placeholder="What to say, what's blocking it, anything to remember…"
                                  class="w-full text-sm border-gray-300 rounded-lg focus:ring-matcha-400 focus:border-matcha-400"
                                  :class="sheet.source_type && $store.privacy.enabled ? 'blur-sm' : ''"></textarea>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-wrap items-center gap-2 pt-1">
                        <button type="button" @click="deleteSheet()"
                                class="text-sm font-medium rounded-lg px-3 py-2 transition"
                                :class="confirmDelete ? 'bg-strawberry-600 text-white' : 'text-strawberry-600 hover:bg-strawberry-50'"
                                x-text="confirmDelete
                                    ? (sheet.source_type ? 'Tap again to dismiss' : 'Tap again to delete')
                                    : (sheet.source_type ? 'Dismiss' : 'Delete')"></button>
                        <div class="ml-auto flex gap-2">
                            <button type="button" @click="close()"
                                    class="text-sm font-medium rounded-lg px-4 py-2 text-gray-600 hover:bg-gray-100">Cancel</button>
                            <button type="submit" :disabled="saving"
                                    class="text-sm font-semibold rounded-lg px-4 py-2 bg-matcha-800 text-white hover:bg-matcha-900 disabled:opacity-50">Save</button>
                        </div>
                        <p x-show="confirmDelete && sheet.source_type" class="w-full text-xs text-gray-500">
                            It won't come back while this follow-up is still open.
                        </p>
                    </div>
                </form>
            </template>
        </div>

    </div>
</x-app-layout>
