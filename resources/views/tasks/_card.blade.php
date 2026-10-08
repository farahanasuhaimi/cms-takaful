{{-- One Task Board card. Rendered inside an Alpine x-for with `task` in scope. --}}
<article draggable="true"
         @dragstart="onDragStart(task)" @dragend="onDragEnd()"
         @dragover.prevent.stop
         @drop.prevent.stop="onDrop(task.status, task.id)"
         class="rounded-lg border bg-white px-3 py-2.5 text-sm shadow-sm transition lg:cursor-grab"
         :class="{
            'border-strawberry-200 bg-strawberry-50': due(task)?.tone === 'overdue',
            'border-gray-200 hover:border-gray-300': due(task)?.tone !== 'overdue',
            'opacity-40': dragging && dragging.id === task.id,
         }">

    <div class="flex items-start gap-2">
        {{-- Priority star --}}
        <button type="button" @click="togglePriority(task)"
                :aria-pressed="task.is_priority.toString()"
                :title="task.is_priority ? 'Remove priority' : 'Mark as priority'"
                class="-ml-1 mt-px p-1 rounded flex-shrink-0 transition"
                :class="task.is_priority ? 'text-amber-400' : 'text-gray-200 hover:text-amber-300'">
            <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M9.05 2.93c.3-.92 1.6-.92 1.9 0l1.07 3.29a1 1 0 00.95.69h3.46c.97 0 1.37 1.24.59 1.81l-2.8 2.03a1 1 0 00-.36 1.12l1.07 3.29c.3.92-.76 1.69-1.54 1.12l-2.8-2.03a1 1 0 00-1.18 0l-2.8 2.03c-.78.57-1.84-.2-1.54-1.12l1.07-3.29a1 1 0 00-.36-1.12L2.98 8.72c-.78-.57-.38-1.81.59-1.81h3.46a1 1 0 00.95-.69l1.07-3.29z"/>
            </svg>
        </button>

        <div class="min-w-0 flex-1">
            {{-- Why this card exists / how urgent --}}
            <div x-show="task.source_label || due(task)" class="flex flex-wrap items-center gap-1 mb-1">
                <span x-show="task.source_label" x-text="task.source_label"
                      class="text-[10px] font-semibold uppercase tracking-wide rounded px-1.5 py-0.5"
                      :class="{
                         'text-strawberry-800 bg-strawberry-100': task.source_type === 'overdue_followup',
                         'text-amber-800 bg-amber-100': task.source_type === 'hot_lead',
                         'text-matcha-800 bg-matcha-100': task.source_type === 'untouched_client',
                      }"></span>
                <template x-if="due(task)">
                    <span x-text="due(task).label"
                          class="text-[10px] font-semibold rounded px-1.5 py-0.5"
                          :class="{
                             'text-white bg-strawberry-600': due(task).tone === 'overdue',
                             'text-amber-900 bg-amber-200': due(task).tone === 'today',
                             'text-gray-700 bg-gray-100': due(task).tone === 'soon',
                             'text-gray-500 bg-gray-50': due(task).tone === 'later',
                          }"></span>
                </template>
            </div>

            {{-- Title: CRM cards link to their record; your own cards open the details --}}
            <template x-if="task.source_url">
                <a :href="task.source_url" target="_blank" rel="noopener" @click.stop
                   x-text="task.title"
                   class="block break-words hover:underline"
                   :class="[
                      $store.privacy.enabled ? 'blur-sm select-none pointer-events-none' : '',
                      task.status === 'done' ? 'line-through text-gray-400' : 'text-matcha-800',
                   ]"></a>
            </template>
            <template x-if="!task.source_url">
                <button type="button" @click="open(task)" x-text="task.title"
                        class="block w-full text-left break-words"
                        :class="[
                           task.source_type && $store.privacy.enabled ? 'blur-sm select-none' : '',
                           task.status === 'done' ? 'line-through text-gray-400' : 'text-gray-800',
                        ]"></button>
            </template>

            <p x-show="task.notes" x-text="task.notes"
               class="mt-1 text-xs text-gray-500 whitespace-pre-line line-clamp-2 break-words"
               :class="task.source_type && $store.privacy.enabled ? 'blur-sm select-none' : ''"></p>
        </div>

        <span x-text="ageLabel(task)"
              :title="task.status === 'done' ? '' : 'Time in this column'"
              class="flex-shrink-0 text-[10px] font-medium tabular-nums mt-1"
              :class="{
                 'text-gray-300': ageTone(task) === 'fresh',
                 'text-amber-500': ageTone(task) === 'aging',
                 'text-strawberry-600': ageTone(task) === 'stale',
              }"></span>
    </div>

    {{-- One-tap moves (the only way to move cards on a phone) --}}
    <div class="mt-2 flex items-center gap-1.5">
        <template x-if="task.status === 'backlog'">
            <button type="button" @click="move(task, 'today')"
                    class="text-xs font-medium rounded-md px-2.5 py-1 bg-amber-50 text-amber-800 hover:bg-amber-100">→ Today</button>
        </template>
        <template x-if="task.status === 'today'">
            <button type="button" @click="move(task, 'doing')"
                    class="text-xs font-medium rounded-md px-2.5 py-1 bg-indigo-50 text-indigo-700 hover:bg-indigo-100">Start</button>
        </template>
        <template x-if="task.status !== 'done'">
            <button type="button" @click="move(task, 'done')"
                    class="text-xs font-medium rounded-md px-2.5 py-1 bg-matcha-50 text-matcha-800 hover:bg-matcha-100">✓ Done</button>
        </template>
        <template x-if="task.status === 'done'">
            <button type="button" @click="move(task, 'today')"
                    class="text-xs font-medium rounded-md px-2.5 py-1 bg-gray-50 text-gray-600 hover:bg-gray-100">Undo</button>
        </template>

        <button type="button" @click="open(task)" aria-label="Details"
                class="ml-auto text-gray-400 hover:text-gray-700 rounded-md px-2 py-1 hover:bg-gray-50">
            <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zm6 0a2 2 0 11-4 0 2 2 0 014 0zm6 0a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
        </button>
    </div>
</article>
