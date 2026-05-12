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
        <div class="space-y-4">
            @foreach ($angles as $angle)
                <div class="bg-white rounded-xl border border-gray-200 p-5"
                     x-data="{
                         showLeads: false,
                         showClients: false,
                         showStrategies: false,
                         delConfirm: false
                     }">

                    {{-- Header --}}
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
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
                                <p class="text-sm text-gray-600 mt-1.5 leading-relaxed">{{ $angle->description }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0">
                            <a href="{{ route('angles.edit', $angle) }}"
                               class="text-xs text-matcha-600 hover:underline">Edit</a>
                            <button type="button" @click="delConfirm = !delConfirm"
                                    class="text-xs text-strawberry-400 hover:text-strawberry-600">Delete</button>
                        </div>
                    </div>

                    {{-- Delete confirm --}}
                    <div x-show="delConfirm" class="mt-3 flex items-center gap-2">
                        <span class="text-xs text-gray-500">Remove this angle?</span>
                        <form method="POST" action="{{ route('angles.destroy', $angle) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-strawberry-600 font-medium hover:underline">Yes, remove</button>
                        </form>
                        <button @click="delConfirm = false" class="text-xs text-gray-400">Cancel</button>
                    </div>

                    {{-- Divider --}}
                    <div class="border-t border-gray-100 mt-4 pt-4 grid grid-cols-1 sm:grid-cols-3 gap-4">

                        {{-- Linked Leads --}}
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Leads</p>
                            @if ($angle->leads->isEmpty())
                                <p class="text-xs text-gray-300 italic">None linked</p>
                            @else
                                <ul class="space-y-1 mb-2">
                                    @foreach ($angle->leads as $lead)
                                        <li class="flex items-center justify-between gap-2">
                                            <span class="text-xs text-gray-700 truncate">{{ $lead->name }}</span>
                                            <form method="POST" action="{{ route('angles.leads.detach', [$angle, $lead]) }}">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-gray-300 hover:text-strawberry-400 transition text-sm leading-none flex-shrink-0" title="Remove">×</button>
                                            </form>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            @if ($allLeads->isNotEmpty())
                                <button @click="showLeads = !showLeads"
                                        class="text-xs text-matcha-600 hover:text-matcha-800 font-medium transition">
                                    <span x-text="showLeads ? '▴ Close' : '+ Link Lead'"></span>
                                </button>
                                <div x-show="showLeads" x-transition class="mt-2">
                                    <form method="POST" id="lead-form-{{ $angle->id }}" class="flex gap-2 items-center">
                                        @csrf
                                        <select name="lead_id" class="text-xs border-gray-200 rounded-lg px-2 py-1.5 focus:ring-matcha-400 focus:border-matcha-400 flex-1">
                                            @foreach ($allLeads as $lead)
                                                @if (! $angle->leads->contains($lead->id))
                                                    <option value="{{ $lead->id }}">{{ $lead->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <button type="submit" formaction="{{ url('angles/'.$angle->id.'/leads/') }}"
                                                onclick="this.form.action='{{ url('angles/'.$angle->id.'/leads/') }}' + '/' + this.closest('div').querySelector('select').value"
                                                class="text-xs bg-matcha-600 hover:bg-matcha-800 text-white px-3 py-1.5 rounded-lg transition">
                                            Link
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>

                        {{-- Linked Strategies --}}
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Strategies</p>
                            @if ($angle->strategies->isEmpty())
                                <p class="text-xs text-gray-300 italic">None linked</p>
                            @else
                                <ul class="space-y-1 mb-2">
                                    @foreach ($angle->strategies as $strategy)
                                        <li class="flex items-center justify-between gap-2">
                                            <div class="min-w-0">
                                                <a href="{{ route('strategies.show', $strategy) }}"
                                                   class="text-xs text-matcha-700 hover:underline truncate block">{{ $strategy->title }}</a>
                                                <span class="text-xs text-gray-400">{{ App\Models\Strategy::categoryLabel($strategy->category) }}</span>
                                            </div>
                                            <form method="POST" action="{{ route('angles.strategies.detach', [$angle, $strategy]) }}">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-gray-300 hover:text-strawberry-400 transition text-sm leading-none flex-shrink-0" title="Remove">×</button>
                                            </form>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            @if ($allStrategies->isNotEmpty())
                                <button @click="showStrategies = !showStrategies"
                                        class="text-xs text-matcha-600 hover:text-matcha-800 font-medium transition">
                                    <span x-text="showStrategies ? '▴ Close' : '+ Link Strategy'"></span>
                                </button>
                                <div x-show="showStrategies" x-transition class="mt-2">
                                    <form method="POST" class="flex gap-2 items-center"
                                          @submit.prevent="
                                            const sel = $el.querySelector('select');
                                            $el.action = '{{ url('angles/'.$angle->id.'/strategies') }}/' + sel.value;
                                            $el.submit();
                                          ">
                                        @csrf
                                        <select name="strategy_id" class="text-xs border-gray-200 rounded-lg px-2 py-1.5 focus:ring-matcha-400 focus:border-matcha-400 flex-1">
                                            @foreach ($allStrategies as $strategy)
                                                @if (! $angle->strategies->contains($strategy->id))
                                                    <option value="{{ $strategy->id }}">{{ $strategy->title }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <button type="submit"
                                                class="text-xs bg-matcha-600 hover:bg-matcha-800 text-white px-3 py-1.5 rounded-lg transition">
                                            Link
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>

                        {{-- Linked Clients --}}
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Clients</p>
                            @if ($angle->clients->isEmpty())
                                <p class="text-xs text-gray-300 italic">None linked</p>
                            @else
                                <ul class="space-y-1 mb-2">
                                    @foreach ($angle->clients as $client)
                                        <li class="flex items-center justify-between gap-2">
                                            <span class="text-xs text-gray-700 truncate">{{ $client->name }}</span>
                                            <form method="POST" action="{{ route('angles.clients.detach', [$angle, $client]) }}">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-gray-300 hover:text-strawberry-400 transition text-sm leading-none flex-shrink-0" title="Remove">×</button>
                                            </form>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            @if ($allClients->isNotEmpty())
                                <button @click="showClients = !showClients"
                                        class="text-xs text-matcha-600 hover:text-matcha-800 font-medium transition">
                                    <span x-text="showClients ? '▴ Close' : '+ Link Client'"></span>
                                </button>
                                <div x-show="showClients" x-transition class="mt-2">
                                    <form method="POST" class="flex gap-2 items-center"
                                          @submit.prevent="
                                            const sel = $el.querySelector('select');
                                            $el.action = '{{ url('angles/'.$angle->id.'/clients') }}/' + sel.value;
                                            $el.submit();
                                          ">
                                        @csrf
                                        <select name="client_id" class="text-xs border-gray-200 rounded-lg px-2 py-1.5 focus:ring-matcha-400 focus:border-matcha-400 flex-1">
                                            @foreach ($allClients as $client)
                                                @if (! $angle->clients->contains($client->id))
                                                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <button type="submit"
                                                class="text-xs bg-matcha-600 hover:bg-matcha-800 text-white px-3 py-1.5 rounded-lg transition">
                                            Link
                                        </button>
                                    </form>
                                </div>
                            @endif
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
