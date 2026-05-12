<x-app-layout>
    <x-slot name="title">{{ $strategy->title }} · Strategy</x-slot>
    <x-slot name="pageTitle">{{ $strategy->title }}</x-slot>

    <x-slot name="actions">
        <div class="relative flex items-center gap-2" x-data="{ sellOpen: false }">
            <a href="{{ route('strategies.index') }}"
               class="text-xs bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 px-3 py-1.5 rounded-lg transition">
                ← Library
            </a>
            @if ($strategy->user_id === auth()->id())
                <a href="{{ route('strategies.edit', $strategy) }}"
                   class="text-xs bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 px-3 py-1.5 rounded-lg transition">
                    Edit
                </a>
                @if ($listing)
                    <span class="text-xs bg-amber-50 border border-amber-200 text-amber-600 font-medium px-3 py-1.5 rounded-lg">
                        ✓ Listed · {{ $listing->price_credits }} credits
                    </span>
                @else
                    <button @click="sellOpen = !sellOpen"
                            class="text-xs bg-amber-500 hover:bg-amber-600 text-white font-medium px-3 py-1.5 rounded-lg transition">
                        Sell on Marketplace
                    </button>
                @endif
            @endif

            {{-- Sell dropdown --}}
            @if ($strategy->user_id === auth()->id() && ! $listing)
                <div x-show="sellOpen"
                     x-transition
                     @click.outside="sellOpen = false"
                     class="absolute top-full right-0 mt-2 z-40 bg-white border border-gray-200 rounded-xl shadow-lg p-4 w-80">
                    <p class="text-sm font-semibold text-gray-800 mb-3">List on Marketplace</p>
                    <form method="POST" action="{{ route('marketplace.strategies.store') }}" class="space-y-3">
                        @csrf
                        <input type="hidden" name="strategy_id" value="{{ $strategy->id }}">
                        <input type="hidden" name="title" value="{{ $strategy->title }}">
                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">Description <span class="text-gray-300">(optional)</span></label>
                            <textarea name="description" rows="2" maxlength="1000"
                                      class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-amber-400 resize-none"
                                      placeholder="What makes this strategy useful?"></textarea>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">Price (credits)</label>
                            <input type="number" name="price_credits" min="1" max="500" value="10"
                                   class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-amber-400">
                        </div>
                        <div class="flex gap-2 pt-1">
                            <button type="submit"
                                    class="flex-1 text-sm bg-amber-500 hover:bg-amber-600 text-white font-medium py-2 rounded-lg transition">
                                List for Sale
                            </button>
                            <button type="button" @click="sellOpen = false"
                                    class="text-sm text-gray-400 hover:text-gray-600 px-3 py-2 transition">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </x-slot>

    {{-- Meta badges --}}
    <div class="flex flex-wrap gap-2 mb-5">
        <span class="text-xs px-2.5 py-1 rounded-full
            {{ $strategy->type === 'flow' ? 'bg-violet-100 text-violet-600' : 'bg-sky-100 text-sky-600' }}">
            {{ ucfirst($strategy->type) }}
        </span>
        <span class="text-xs bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full">
            {{ \App\Models\Strategy::categoryLabel($strategy->category) }}
        </span>
        <span class="text-xs bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full">
            {{ \App\Models\Strategy::channelLabel($strategy->channel) }}
        </span>
        <span class="text-xs bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full">
            {{ \App\Models\Strategy::audienceLabel($strategy->audience) }}
        </span>
        <span class="text-xs bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full capitalize">
            {{ $strategy->difficulty }}
        </span>
        @if ($strategy->source === 'ai_guided')
            <span class="text-xs bg-matcha-50 text-matcha-600 px-2.5 py-1 rounded-full">AI Guided</span>
        @elseif ($strategy->source === 'provided')
            <span class="text-xs bg-amber-50 text-amber-600 px-2.5 py-1 rounded-full">Provided</span>
        @endif
    </div>

    @if ($strategy->description)
        <p class="text-sm text-gray-600 mb-5 leading-relaxed">{{ $strategy->description }}</p>
    @endif

    {{-- Script content --}}
    @if ($strategy->type === 'script' && $strategy->content)
        <div class="bg-white rounded-xl border border-gray-200 p-5 mb-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-3">Script</p>
            <p class="text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $strategy->content }}</p>
        </div>
    @endif

    {{-- Flow steps --}}
    @if ($strategy->type === 'flow')
        <div class="mb-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-3">Flow Steps</p>

            @if ($steps->isEmpty())
                <div class="bg-white rounded-xl border border-gray-200 px-5 py-8 text-center text-sm text-gray-400">
                    No steps yet.
                    @if ($strategy->user_id === auth()->id())
                        Add the first step below.
                    @endif
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($steps as $step)
                        <div class="bg-white rounded-xl border border-gray-200 p-5">
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex-shrink-0 w-7 h-7 bg-matcha-100 text-matcha-700 rounded-full flex items-center justify-center text-xs font-bold">
                                        {{ $step->step_order }}
                                    </span>
                                    <p class="font-semibold text-sm text-gray-800">{{ $step->title }}</p>
                                </div>
                                @if ($strategy->user_id === auth()->id())
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        <button type="button"
                                                onclick="toggleEditStep({{ $step->id }})"
                                                class="text-xs text-gray-400 hover:text-matcha-600 transition">Edit</button>
                                        <form method="POST"
                                              action="{{ route('strategies.steps.destroy', [$strategy, $step]) }}">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    onclick="return confirm('Remove this step?')"
                                                    class="text-xs text-red-400 hover:text-red-600 transition">Remove</button>
                                        </form>
                                    </div>
                                @endif
                            </div>

                            <p class="text-sm text-gray-700 whitespace-pre-wrap leading-relaxed mb-3">{{ $step->script }}</p>

                            @if ($step->timing_note)
                                <p class="text-xs text-gray-400 italic mb-2">⏱ {{ $step->timing_note }}</p>
                            @endif

                            <div class="flex flex-col sm:flex-row gap-2">
                                @if ($step->branch_yes)
                                    <div class="flex-1 bg-green-50 border border-green-100 rounded-lg px-3 py-2">
                                        <p class="text-xs font-medium text-green-700 mb-0.5">If YES</p>
                                        <p class="text-xs text-green-600">{{ $step->branch_yes }}</p>
                                    </div>
                                @endif
                                @if ($step->branch_no)
                                    <div class="flex-1 bg-red-50 border border-red-100 rounded-lg px-3 py-2">
                                        <p class="text-xs font-medium text-red-500 mb-0.5">If NO / No Response</p>
                                        <p class="text-xs text-red-400">{{ $step->branch_no }}</p>
                                    </div>
                                @endif
                            </div>

                            {{-- Inline edit form --}}
                            @if ($strategy->user_id === auth()->id())
                                <div id="edit-step-{{ $step->id }}" class="hidden mt-4 pt-4 border-t border-gray-100">
                                    <form method="POST"
                                          action="{{ route('strategies.steps.update', [$strategy, $step]) }}">
                                        @csrf @method('PUT')
                                        <div class="space-y-3">
                                            <div>
                                                <label class="text-xs text-gray-500 mb-1 block">Title</label>
                                                <input type="text" name="title" value="{{ $step->title }}"
                                                       class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                                            </div>
                                            <div>
                                                <label class="text-xs text-gray-500 mb-1 block">Script</label>
                                                <textarea name="script" rows="4"
                                                          class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">{{ $step->script }}</textarea>
                                            </div>
                                            <div>
                                                <label class="text-xs text-gray-500 mb-1 block">Timing Note</label>
                                                <input type="text" name="timing_note" value="{{ $step->timing_note }}"
                                                       class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400"
                                                       placeholder="e.g. Wait 2 days">
                                            </div>
                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="text-xs text-green-600 mb-1 block">If YES →</label>
                                                    <textarea name="branch_yes" rows="2"
                                                              class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400"
                                                              placeholder="Next step if positive response">{{ $step->branch_yes }}</textarea>
                                                </div>
                                                <div>
                                                    <label class="text-xs text-red-400 mb-1 block">If NO →</label>
                                                    <textarea name="branch_no" rows="2"
                                                              class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400"
                                                              placeholder="Next step if no response">{{ $step->branch_no }}</textarea>
                                                </div>
                                            </div>
                                            <div class="flex gap-2">
                                                <button type="submit"
                                                        class="text-xs bg-matcha-600 hover:bg-matcha-700 text-white px-3 py-1.5 rounded-lg transition">
                                                    Save
                                                </button>
                                                <button type="button"
                                                        onclick="toggleEditStep({{ $step->id }})"
                                                        class="text-xs text-gray-400 hover:text-gray-600 transition">
                                                    Cancel
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Add step form --}}
            @if ($strategy->user_id === auth()->id())
                <div class="mt-4">
                    <button type="button" onclick="toggleAddStep()"
                            id="add-step-btn"
                            class="text-xs text-matcha-600 hover:text-matcha-700 font-medium transition">
                        + Add Step
                    </button>

                    <div id="add-step-form" class="hidden mt-3 bg-white rounded-xl border border-gray-200 p-5">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-3">New Step</p>
                        <form method="POST" action="{{ route('strategies.steps.store', $strategy) }}">
                            @csrf
                            <div class="space-y-3">
                                <div>
                                    <label class="text-xs text-gray-500 mb-1 block">Title <span class="text-red-400">*</span></label>
                                    <input type="text" name="title" required
                                           class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400"
                                           placeholder="e.g. Opening Message">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 mb-1 block">Script <span class="text-red-400">*</span></label>
                                    <textarea name="script" rows="4" required
                                              class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400"
                                              placeholder="What to say or do at this step"></textarea>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 mb-1 block">Timing Note</label>
                                    <input type="text" name="timing_note"
                                           class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400"
                                           placeholder="e.g. Same conversation / Wait 2 days">
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="text-xs text-green-600 mb-1 block">If YES →</label>
                                        <textarea name="branch_yes" rows="2"
                                                  class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400"
                                                  placeholder="Next step if positive"></textarea>
                                    </div>
                                    <div>
                                        <label class="text-xs text-red-400 mb-1 block">If NO →</label>
                                        <textarea name="branch_no" rows="2"
                                                  class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400"
                                                  placeholder="Next step if no response"></textarea>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <button type="submit"
                                            class="text-xs bg-matcha-600 hover:bg-matcha-700 text-white px-4 py-1.5 rounded-lg transition">
                                        Add Step
                                    </button>
                                    <button type="button" onclick="toggleAddStep()"
                                            class="text-xs text-gray-400 hover:text-gray-600 transition">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- Delist option (shown only when already listed) --}}
    @if ($strategy->user_id === auth()->id() && $listing)
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-amber-700">Listed on Marketplace · {{ $listing->price_credits }} credits</p>
                <p class="text-xs text-amber-500 mt-0.5">{{ $listing->purchases_count ?? 0 }} sold</p>
            </div>
            <form method="POST" action="{{ route('marketplace.strategies.destroy', $listing) }}">
                @csrf @method('DELETE')
                <button type="submit"
                        onclick="return confirm('Remove from marketplace?')"
                        class="text-xs text-red-400 hover:text-red-600 transition">
                    Delist
                </button>
            </form>
        </div>
    @endif

<script>
function toggleEditStep(id) {
    const el = document.getElementById('edit-step-' + id);
    el.classList.toggle('hidden');
}
function toggleAddStep() {
    document.getElementById('add-step-form').classList.toggle('hidden');
}
</script>

</x-app-layout>
