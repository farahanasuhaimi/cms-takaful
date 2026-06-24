<x-app-layout>
    <x-slot name="title">{{ $angle->title }} · Reach Angle</x-slot>
    <x-slot name="pageTitle">{{ $angle->title }}</x-slot>

    <x-slot name="actions">
        <div class="flex items-center gap-2" x-data="{ delConfirm: false }">
            <a href="{{ route('angles.index') }}"
               class="text-xs bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 px-3 py-1.5 rounded-lg transition">
                ← Angles
            </a>
            <a href="{{ route('angles.edit', $angle) }}"
               class="text-xs bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 px-3 py-1.5 rounded-lg transition">
                Edit
            </a>
            <button type="button" @click="delConfirm = !delConfirm"
                    class="text-xs text-strawberry-400 hover:text-strawberry-600 transition px-1">
                Delete
            </button>
            <div x-show="delConfirm" class="flex items-center gap-2">
                <span class="text-xs text-gray-500">Sure?</span>
                <form method="POST" action="{{ route('angles.destroy', $angle) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs text-strawberry-600 font-medium hover:underline">Yes</button>
                </form>
                <button @click="delConfirm = false" class="text-xs text-gray-400">No</button>
            </div>
        </div>
    </x-slot>

    {{-- Status + segment badges --}}
    <div class="flex flex-wrap gap-2 mb-5">
        <span class="text-xs font-medium px-2.5 py-1 rounded-full
            {{ $angle->status === 'active' ? 'bg-matcha-50 text-matcha-700' :
               ($angle->status === 'paused' ? 'bg-amber-50 text-amber-600' : 'bg-gray-100 text-gray-400') }}">
            {{ ucfirst($angle->status) }}
        </span>
        @if ($angle->target_segment)
            <span class="text-xs bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full">
                {{ $angle->target_segment }}
            </span>
        @endif
    </div>

    {{-- Description --}}
    @if ($angle->description)
        <p class="text-sm text-gray-600 mb-5 leading-relaxed">{{ $angle->description }}</p>
    @endif

    {{-- What to say (notes) --}}
    @if ($angle->notes)
        <div class="bg-white rounded-xl border border-gray-200 p-5 mb-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">What to Say</p>
            <p class="text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $angle->notes }}</p>
        </div>
    @endif

    {{-- Linked people + strategies --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">

        {{-- Leads --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5" x-data="{ open: false }">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Leads</p>
            @if ($angle->leads->isEmpty())
                <p class="text-xs text-gray-300 italic mb-3">None linked</p>
            @else
                <ul class="space-y-1.5 mb-3">
                    @foreach ($angle->leads as $lead)
                        <li class="flex items-center justify-between gap-2">
                            <span class="text-sm text-gray-700 truncate">{{ $lead->name }}</span>
                            <form method="POST" action="{{ route('angles.leads.detach', [$angle, $lead]) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-300 hover:text-strawberry-400 transition text-base leading-none flex-shrink-0" title="Remove">×</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @endif
            @if ($allLeads->isNotEmpty())
                <button @click="open = !open" class="text-xs text-matcha-600 hover:text-matcha-800 font-medium transition">
                    <span x-text="open ? '▴ Close' : '+ Link Lead'"></span>
                </button>
                <div x-show="open" x-transition class="mt-2">
                    <form method="POST" class="flex gap-2 items-center"
                          @submit.prevent="
                            const sel = $el.querySelector('select');
                            $el.action = '{{ url('angles/'.$angle->id.'/leads') }}/' + sel.value;
                            $el.submit();
                          ">
                        @csrf
                        <select class="text-xs border-gray-200 rounded-lg px-2 py-1.5 focus:ring-matcha-400 focus:border-matcha-400 flex-1">
                            @foreach ($allLeads as $lead)
                                @if (! $angle->leads->contains($lead->id))
                                    <option value="{{ $lead->id }}">{{ $lead->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        <button type="submit" class="text-xs bg-matcha-600 hover:bg-matcha-800 text-white px-3 py-1.5 rounded-lg transition">Link</button>
                    </form>
                </div>
            @endif
        </div>

        {{-- Strategies --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5" x-data="{ open: false }">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Strategies</p>
            @if ($angle->strategies->isEmpty())
                <p class="text-xs text-gray-300 italic mb-3">None linked</p>
            @else
                <ul class="space-y-1.5 mb-3">
                    @foreach ($angle->strategies as $strategy)
                        <li class="flex items-center justify-between gap-2">
                            <div class="min-w-0">
                                <a href="{{ route('strategies.show', $strategy) }}"
                                   class="text-sm text-matcha-700 hover:underline truncate block">{{ $strategy->title }}</a>
                                <span class="text-xs text-gray-400">{{ App\Models\Strategy::categoryLabel($strategy->category) }}</span>
                            </div>
                            <form method="POST" action="{{ route('angles.strategies.detach', [$angle, $strategy]) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-300 hover:text-strawberry-400 transition text-base leading-none flex-shrink-0" title="Remove">×</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @endif
            @if ($allStrategies->isNotEmpty())
                <button @click="open = !open" class="text-xs text-amber-600 hover:text-amber-800 font-medium transition">
                    <span x-text="open ? '▴ Close' : '+ Link Strategy'"></span>
                </button>
                <div x-show="open" x-transition class="mt-2">
                    <form method="POST" class="flex gap-2 items-center"
                          @submit.prevent="
                            const sel = $el.querySelector('select');
                            $el.action = '{{ url('angles/'.$angle->id.'/strategies') }}/' + sel.value;
                            $el.submit();
                          ">
                        @csrf
                        <select class="text-xs border-gray-200 rounded-lg px-2 py-1.5 focus:ring-matcha-400 focus:border-matcha-400 flex-1">
                            @foreach ($allStrategies as $strategy)
                                @if (! $angle->strategies->contains($strategy->id))
                                    <option value="{{ $strategy->id }}">{{ $strategy->title }}</option>
                                @endif
                            @endforeach
                        </select>
                        <button type="submit" class="text-xs bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg transition">Link</button>
                    </form>
                </div>
            @endif
            <a href="{{ route('strategies.create', ['angle_id' => $angle->id]) }}"
               class="block text-xs text-amber-600 hover:text-amber-800 mt-2 transition">+ New Strategy for this angle</a>
        </div>

        {{-- Clients --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5" x-data="{ open: false }">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Clients</p>
            @if ($angle->clients->isEmpty())
                <p class="text-xs text-gray-300 italic mb-3">None linked</p>
            @else
                <ul class="space-y-1.5 mb-3">
                    @foreach ($angle->clients as $client)
                        <li class="flex items-center justify-between gap-2">
                            <span class="text-sm text-gray-700 truncate">{{ $client->name }}</span>
                            <form method="POST" action="{{ route('angles.clients.detach', [$angle, $client]) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-300 hover:text-strawberry-400 transition text-base leading-none flex-shrink-0" title="Remove">×</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @endif
            @if ($allClients->isNotEmpty())
                <button @click="open = !open" class="text-xs text-matcha-600 hover:text-matcha-800 font-medium transition">
                    <span x-text="open ? '▴ Close' : '+ Link Client'"></span>
                </button>
                <div x-show="open" x-transition class="mt-2">
                    <form method="POST" class="flex gap-2 items-center"
                          @submit.prevent="
                            const sel = $el.querySelector('select');
                            $el.action = '{{ url('angles/'.$angle->id.'/clients') }}/' + sel.value;
                            $el.submit();
                          ">
                        @csrf
                        <select class="text-xs border-gray-200 rounded-lg px-2 py-1.5 focus:ring-matcha-400 focus:border-matcha-400 flex-1">
                            @foreach ($allClients as $client)
                                @if (! $angle->clients->contains($client->id))
                                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        <button type="submit" class="text-xs bg-matcha-600 hover:bg-matcha-800 text-white px-3 py-1.5 rounded-lg transition">Link</button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    {{-- AI Content --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5 mb-5">
        <div class="flex items-center justify-between mb-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">AI Content</p>
            <div class="flex items-center gap-3">
                @if ($angle->latestContents->isNotEmpty())
                    <a href="{{ route('angles.library') }}" class="text-xs text-gray-400 hover:text-matcha-600 transition">View Library →</a>
                @endif
                <form method="POST" action="{{ route('angle-contents.generate', $angle) }}">
                    @csrf
                    <button type="submit"
                            class="text-xs bg-matcha-600 hover:bg-matcha-800 text-white px-3 py-1.5 rounded-lg transition">
                        Generate
                    </button>
                </form>
            </div>
        </div>

        @if ($angle->latestContents->isEmpty())
            <p class="text-xs text-gray-300 italic">No content yet. Hit Generate to create casual / story / factual variations.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                @foreach ($angle->latestContents->sortBy('style') as $item)
                    <div x-data="{ copied: false, pinned: {{ $item->is_pinned ? 'true' : 'false' }} }"
                         class="border rounded-xl p-4 transition"
                         :class="pinned ? 'border-matcha-300 bg-matcha-50' : 'border-gray-200 bg-gray-50'">

                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-semibold uppercase tracking-wide
                                {{ $item->style === 'casual' ? 'text-blue-600' : ($item->style === 'story' ? 'text-purple-600' : 'text-amber-600') }}">
                                {{ ['casual' => '💬 Casual', 'story' => '📖 Story', 'factual' => '📊 Factual'][$item->style] }}
                            </span>
                            <button @click="
                                    fetch('{{ route('angle-contents.pin', $item) }}', {
                                        method: 'PATCH',
                                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
                                    }).then(() => pinned = !pinned)"
                                    :class="pinned ? 'text-matcha-500' : 'text-gray-300 hover:text-matcha-400'"
                                    class="text-base leading-none transition p-1 -mr-1" title="Pin to Library">
                                📌
                            </button>
                        </div>

                        <p class="text-sm text-gray-700 leading-relaxed">{{ $item->content }}</p>

                        <button @click="navigator.clipboard.writeText({{ json_encode($item->content, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) }}); copied = true; setTimeout(() => copied = false, 2000)"
                                class="mt-3 text-xs font-medium transition"
                                :class="copied ? 'text-matcha-600' : 'text-gray-400'">
                            <span x-text="copied ? '✓ Copied' : 'Copy'"></span>
                        </button>
                    </div>
                @endforeach
            </div>
            <p class="text-xs text-gray-300 mt-3">
                Batch {{ $angle->latestContents->first()->batch }} ·
                {{ $angle->latestContents->first()->created_at->format('d M Y') }} ·
                {{ $angle->latestContents->first()->model }}
            </p>
        @endif
    </div>

    {{-- Usage Trail --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5" x-data="{ logOpen: false }">
        <div class="flex items-center justify-between mb-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Usage Trail</p>
            <button @click="logOpen = !logOpen"
                    class="text-xs text-indigo-600 hover:text-indigo-800 font-medium transition">
                <span x-text="logOpen ? '▴ Close' : '+ Log Usage'"></span>
            </button>
        </div>

        {{-- Log form --}}
        <div x-show="logOpen" x-transition class="mb-5">
            <form method="POST" action="{{ route('angles.usages.store', $angle) }}"
                  class="bg-gray-50 rounded-lg p-4 space-y-3">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Date</label>
                        <input type="date" name="used_on" value="{{ date('Y-m-d') }}" required
                               class="w-full text-sm rounded-lg border-gray-200 focus:ring-indigo-400 focus:border-indigo-400 py-1.5">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Lead (optional)</label>
                        <select name="lead_id"
                                class="w-full text-sm rounded-lg border-gray-200 focus:ring-indigo-400 focus:border-indigo-400 py-1.5">
                            <option value="">— None —</option>
                            @foreach ($allLeads as $lead)
                                <option value="{{ $lead->id }}">{{ $lead->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Client (optional)</label>
                        <select name="client_id"
                                class="w-full text-sm rounded-lg border-gray-200 focus:ring-indigo-400 focus:border-indigo-400 py-1.5">
                            <option value="">— None —</option>
                            @foreach ($allClients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Notes</label>
                    <textarea name="notes" rows="2" placeholder="How did it go? Any reactions?"
                              class="w-full text-sm rounded-lg border-gray-200 focus:ring-indigo-400 focus:border-indigo-400 resize-none"></textarea>
                </div>
                <button type="submit"
                        class="text-xs bg-indigo-600 hover:bg-indigo-800 text-white px-4 py-1.5 rounded-lg transition">
                    Save
                </button>
            </form>
        </div>

        {{-- History --}}
        @if ($angle->usages->isEmpty())
            <p class="text-xs text-gray-300 italic">No usage recorded yet.</p>
        @else
            <div class="divide-y divide-gray-50">
                @foreach ($angle->usages as $usage)
                    <div class="flex items-start justify-between gap-3 py-3 first:pt-0 last:pb-0">
                        <div>
                            <p class="text-sm text-gray-700 font-medium">{{ $usage->used_on->format('d M Y') }}</p>
                            @if ($usage->lead)
                                <p class="text-xs text-gray-400 mt-0.5">{{ $usage->lead->name }} <span class="text-gray-300">· Lead</span></p>
                            @elseif ($usage->client)
                                <p class="text-xs text-gray-400 mt-0.5">{{ $usage->client->name }} <span class="text-gray-300">· Client</span></p>
                            @endif
                            @if ($usage->notes)
                                <p class="text-xs text-gray-500 mt-1 leading-relaxed">{{ $usage->notes }}</p>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('angles.usages.destroy', [$angle, $usage]) }}" class="flex-shrink-0">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-gray-300 hover:text-strawberry-400 transition text-lg leading-none mt-0.5" title="Remove">×</button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</x-app-layout>
