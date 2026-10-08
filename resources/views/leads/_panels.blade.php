{{-- Inline Tags / Convert / Log panels for one lead. Parent element must provide
     x-data with tpOpen, convertOpen, fpOpen and tagged. Expects $lead, $focusPoints, $strategies. --}}

{{-- Focus Points panel --}}
@if ($focusPoints->isNotEmpty())
<div x-show="fpOpen" x-transition class="mt-3 p-3 bg-indigo-50 rounded-lg border border-indigo-100 text-left">
    <p class="text-xs text-gray-500 mb-2">Tap to tag which points resonated:</p>
    <div class="flex flex-wrap gap-1.5">
        @foreach ($focusPoints as $fp)
            <button type="button"
                    @click="
                        const isTagged = tagged.includes({{ $fp->id }});
                        fetch(isTagged
                            ? '{{ route('leads.focus-points.detach', [$lead, $fp]) }}'
                            : '{{ route('leads.focus-points.attach', [$lead, $fp]) }}',
                        {
                            method: isTagged ? 'DELETE' : 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json'
                            }
                        }).then(() => {
                            tagged = isTagged
                                ? tagged.filter(id => id !== {{ $fp->id }})
                                : [...tagged, {{ $fp->id }}];
                        })"
                    :class="tagged.includes({{ $fp->id }})
                        ? 'bg-matcha-100 text-matcha-700 border-matcha-300'
                        : 'bg-white text-gray-500 border-gray-200'"
                    class="text-xs px-2.5 py-1 rounded-full border transition">
                {{ $fp->title }}
            </button>
        @endforeach
    </div>
</div>
@endif

{{-- Inline convert confirm --}}
<div x-show="convertOpen" x-transition class="mt-3 p-3 bg-matcha-50 rounded-lg border border-matcha-200 text-left">
    <p class="text-xs font-medium text-gray-700 mb-2">Convert <span class="text-matcha-700"><x-pdpa-mask>{{ $lead->name }}</x-pdpa-mask></span> to Policyholder?</p>
    <form method="POST" action="{{ route('leads.convert', $lead) }}">
        @csrf
        <div class="mb-2">
            <label class="block text-xs text-gray-600 mb-1">IC Number <span class="text-gray-400">(optional — can add later)</span></label>
            <input type="text" name="ic_no" placeholder="e.g. 900101-14-5678"
                   class="w-full text-xs rounded border-gray-300 focus:ring-matcha-400 focus:border-matcha-400" />
        </div>
        <p class="text-xs text-gray-400 mb-2">Contact history will be carried over automatically.</p>
        <div class="flex gap-2">
            <button type="submit"
                    class="text-xs bg-matcha-600 hover:bg-matcha-800 text-white px-3 py-1.5 rounded transition">
                Yes, Convert
            </button>
            <button type="button" @click="convertOpen = false"
                    class="text-xs text-gray-400 hover:text-gray-600">Cancel</button>
        </div>
    </form>
</div>

{{-- Inline touchpoint form --}}
<div x-show="tpOpen" x-transition class="mt-3 p-3 bg-matcha-50 rounded-lg border border-matcha-100 text-left">
    <form method="POST" action="{{ route('leads.touchpoints.store', $lead) }}">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            <div>
                <label class="block text-xs text-gray-600 mb-1">Date</label>
                <input type="datetime-local" name="contacted_at"
                       value="{{ now()->format('Y-m-d\TH:i') }}"
                       class="w-full text-xs rounded border-gray-300 focus:ring-matcha-400 focus:border-matcha-400" />
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">Channel</label>
                <select name="channel"
                        class="w-full text-xs rounded border-gray-300 focus:ring-matcha-400 focus:border-matcha-400">
                    @foreach (['whatsapp','phone_call','in_person','dm_instagram','dm_facebook','email','other'] as $ch)
                        <option value="{{ $ch }}">{{ ucfirst(str_replace('_',' ',$ch)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs text-gray-600 mb-1">Topic</label>
                <input type="text" name="topic" required
                       class="w-full text-xs rounded border-gray-300 focus:ring-matcha-400 focus:border-matcha-400" />
            </div>
            @if ($strategies['suggested']->count() || $strategies['other']->count())
            <div class="sm:col-span-2">
                <label class="block text-xs text-gray-600 mb-1">Strategy Used <span class="text-gray-400">(optional)</span></label>
                <select name="strategy_id"
                        class="w-full text-xs rounded border-gray-300 focus:ring-matcha-400 focus:border-matcha-400">
                    <option value="">— None —</option>
                    @include('strategies._options', ['options' => $strategies, 'personLabel' => 'lead'])
                </select>
            </div>
            @endif
        </div>
        <div class="mt-2 flex gap-2">
            <button type="submit"
                    class="text-xs bg-matcha-600 text-white px-3 py-1.5 rounded transition hover:bg-matcha-800">Save</button>
            <button type="button" @click="tpOpen = false"
                    class="text-xs text-gray-400 hover:text-gray-600">Cancel</button>
        </div>
    </form>
</div>
