<x-app-layout>
    <x-slot name="title">Leads · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">Warm &amp; Hot Leads</x-slot>
    <x-slot name="actions">
        <a href="{{ route('leads.create') }}"
           class="bg-matcha-600 hover:bg-matcha-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            + New Lead
        </a>
    </x-slot>

    {{-- Hot Leads --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 mb-3">
            <h2 class="text-sm font-semibold text-gray-700">Hot Leads</h2>
            <span class="text-xs bg-strawberry-50 text-strawberry-600 font-medium px-2 py-0.5 rounded-full">
                {{ $hotLeads->count() }}
            </span>
        </div>

        @if ($hotLeads->count())
            <div class="bg-white rounded-xl border border-strawberry-100 overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-strawberry-50/60 text-left">
                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Name</th>
                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Phone</th>
                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Interest</th>
                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Stage</th>
                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Next Contact</th>
                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Source</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($hotLeads as $lead)
                            <tr class="hover:bg-strawberry-50/20 transition" x-data="{ tpOpen: false }">
                                <td class="px-5 py-3 font-medium text-gray-800">{{ $lead->name }}</td>
                                <td class="px-5 py-3 text-gray-500">
                                    @if ($lead->phone)
                                        <a href="https://wa.me/{{ $lead->phone }}" target="_blank"
                                           class="hover:text-green-600 transition">{{ $lead->phone }}</a>
                                    @else —
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-gray-500 text-xs">{{ $lead->interest_area ?? '—' }}</td>
                                <td class="px-5 py-3">
                                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">
                                        {{ ucfirst($lead->stage) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-xs text-gray-500">
                                    {{ $lead->next_contact ? $lead->next_contact->format('d M Y') : '—' }}
                                </td>
                                <td class="px-5 py-3 text-xs text-gray-400">{{ ucfirst(str_replace('_', ' ', $lead->source)) }}</td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-2 justify-end">
                                        <button @click="tpOpen = !tpOpen"
                                                class="text-xs text-matcha-600 hover:underline">Log</button>
                                        <a href="{{ route('leads.edit', $lead) }}"
                                           class="text-xs text-gray-400 hover:text-gray-600">Edit</a>
                                        <form method="POST" action="{{ route('leads.convert', $lead) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="text-xs bg-matcha-600 hover:bg-matcha-800 text-white px-2.5 py-1 rounded-lg transition">
                                                Convert
                                            </button>
                                        </form>
                                    </div>
                                    {{-- Inline touchpoint form --}}
                                    <div x-show="tpOpen" x-transition class="mt-3 p-3 bg-matcha-50 rounded-lg border border-matcha-100 col-span-full">
                                        <form method="POST" action="{{ route('leads.touchpoints.store', $lead) }}">
                                            @csrf
                                            <div class="grid grid-cols-2 gap-2">
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
                                                <div class="col-span-2">
                                                    <label class="block text-xs text-gray-600 mb-1">Topic</label>
                                                    <input type="text" name="topic" required
                                                           class="w-full text-xs rounded border-gray-300 focus:ring-matcha-400 focus:border-matcha-400" />
                                                </div>
                                            </div>
                                            <div class="mt-2 flex gap-2">
                                                <button type="submit"
                                                        class="text-xs bg-matcha-600 text-white px-3 py-1.5 rounded transition hover:bg-matcha-800">Save</button>
                                                <button type="button" @click="tpOpen = false"
                                                        class="text-xs text-gray-400 hover:text-gray-600">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="bg-white rounded-xl border border-gray-200 px-5 py-8 text-center">
                <p class="text-sm text-gray-400">No hot leads right now.</p>
            </div>
        @endif
    </div>

    {{-- Warm Leads --}}
    <div>
        <div class="flex items-center gap-2 mb-3">
            <h2 class="text-sm font-semibold text-gray-700">Warm Leads</h2>
            <span class="text-xs bg-amber-50 text-amber-600 font-medium px-2 py-0.5 rounded-full">
                {{ $warmLeads->count() }}
            </span>
        </div>

        @if ($warmLeads->count())
            <div class="bg-white rounded-xl border border-amber-100 overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-amber-50/60 text-left">
                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Name</th>
                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Phone</th>
                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Interest</th>
                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Stage</th>
                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Next Contact</th>
                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Source</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($warmLeads as $lead)
                            <tr class="hover:bg-amber-50/20 transition" x-data="{ tpOpen: false }">
                                <td class="px-5 py-3 font-medium text-gray-800">{{ $lead->name }}</td>
                                <td class="px-5 py-3 text-gray-500">
                                    @if ($lead->phone)
                                        <a href="https://wa.me/{{ $lead->phone }}" target="_blank"
                                           class="hover:text-green-600 transition">{{ $lead->phone }}</a>
                                    @else —
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-gray-500 text-xs">{{ $lead->interest_area ?? '—' }}</td>
                                <td class="px-5 py-3">
                                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">
                                        {{ ucfirst($lead->stage) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-xs text-gray-500">
                                    {{ $lead->next_contact ? $lead->next_contact->format('d M Y') : '—' }}
                                </td>
                                <td class="px-5 py-3 text-xs text-gray-400">{{ ucfirst(str_replace('_', ' ', $lead->source)) }}</td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-2 justify-end">
                                        <button @click="tpOpen = !tpOpen"
                                                class="text-xs text-matcha-600 hover:underline">Log</button>
                                        <a href="{{ route('leads.edit', $lead) }}"
                                           class="text-xs text-gray-400 hover:text-gray-600">Edit</a>
                                        <form method="POST" action="{{ route('leads.convert', $lead) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="text-xs bg-matcha-600 hover:bg-matcha-800 text-white px-2.5 py-1 rounded-lg transition">
                                                Convert
                                            </button>
                                        </form>
                                    </div>
                                    <div x-show="tpOpen" x-transition class="mt-3 p-3 bg-matcha-50 rounded-lg border border-matcha-100">
                                        <form method="POST" action="{{ route('leads.touchpoints.store', $lead) }}">
                                            @csrf
                                            <div class="grid grid-cols-2 gap-2">
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
                                                <div class="col-span-2">
                                                    <label class="block text-xs text-gray-600 mb-1">Topic</label>
                                                    <input type="text" name="topic" required
                                                           class="w-full text-xs rounded border-gray-300 focus:ring-matcha-400 focus:border-matcha-400" />
                                                </div>
                                            </div>
                                            <div class="mt-2 flex gap-2">
                                                <button type="submit"
                                                        class="text-xs bg-matcha-600 text-white px-3 py-1.5 rounded transition hover:bg-matcha-800">Save</button>
                                                <button type="button" @click="tpOpen = false"
                                                        class="text-xs text-gray-400 hover:text-gray-600">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="bg-white rounded-xl border border-gray-200 px-5 py-8 text-center">
                <p class="text-sm text-gray-400">No warm leads. <a href="{{ route('leads.create') }}" class="text-matcha-600 hover:underline">Add one.</a></p>
            </div>
        @endif
    </div>

</x-app-layout>
