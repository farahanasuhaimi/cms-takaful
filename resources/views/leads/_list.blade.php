{{-- One temperature group of leads: cards on mobile, table from sm up.
     Expects $leads, $tone ('hot'|'warm'), $focusPoints, $strategies. --}}
@php
    $toneClasses = [
        'hot'  => ['border' => 'border-strawberry-100', 'head' => 'bg-strawberry-50/60', 'hover' => 'hover:bg-strawberry-50/20'],
        'warm' => ['border' => 'border-amber-100',      'head' => 'bg-amber-50/60',      'hover' => 'hover:bg-amber-50/20'],
    ][$tone];
@endphp

<div class="bg-white rounded-xl border {{ $toneClasses['border'] }} overflow-hidden">

    {{-- Mobile: stacked cards --}}
    <ul class="sm:hidden divide-y divide-gray-100">
        @foreach ($leads as $lead)
            <li class="px-4 py-3" x-data="{
                tpOpen: false, convertOpen: false, fpOpen: false,
                tagged: {{ $lead->focusPoints->pluck('id')->toJson() }}
            }">
                <div class="flex items-start gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="font-medium text-gray-800"><x-pdpa-mask>{{ $lead->name }}</x-pdpa-mask></p>
                        <div class="flex flex-wrap items-center gap-1.5 mt-1">
                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">{{ ucfirst($lead->stage) }}</span>
                            @if ($lead->interest_area)
                                <span class="text-xs text-gray-500">{{ $lead->interest_area }}</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ ucfirst(str_replace('_', ' ', $lead->source)) }}
                            @if ($lead->next_contact)
                                · Next: <span class="{{ $lead->next_contact->lt(today()) ? 'text-strawberry-600 font-medium' : 'text-gray-500' }}">{{ $lead->next_contact->format('d M Y') }}</span>
                            @endif
                        </p>
                        @if ($lead->focusPoints->isNotEmpty())
                            <div class="flex flex-wrap gap-1 mt-1.5">
                                @foreach ($lead->focusPoints as $fp)
                                    <span class="text-xs bg-matcha-50 text-matcha-700 border border-matcha-100 px-2 py-0.5 rounded-full">{{ $fp->title }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    @if ($lead->phone)
                        <a href="https://wa.me/{{ $lead->phone }}" target="_blank" aria-label="WhatsApp"
                           class="flex-shrink-0 p-2 -mr-1 rounded-lg text-green-600 bg-green-50 hover:bg-green-100 transition">
                            <x-whatsapp-icon />
                        </a>
                    @endif
                </div>

                {{-- Actions — full-width tap targets --}}
                <div class="grid grid-cols-4 gap-1.5 mt-3">
                    <button @click="fpOpen = !fpOpen; tpOpen = false; convertOpen = false"
                            :class="fpOpen ? 'bg-indigo-100' : 'bg-indigo-50'"
                            class="text-xs font-medium text-indigo-600 py-2 rounded-lg transition">Tags</button>
                    <button @click="tpOpen = !tpOpen; fpOpen = false; convertOpen = false"
                            :class="tpOpen ? 'bg-matcha-100' : 'bg-matcha-50'"
                            class="text-xs font-medium text-matcha-700 py-2 rounded-lg transition">Log</button>
                    <a href="{{ route('leads.edit', $lead) }}"
                       class="text-xs font-medium text-gray-600 bg-gray-100 py-2 rounded-lg text-center transition">Edit</a>
                    <button @click="convertOpen = !convertOpen; tpOpen = false; fpOpen = false"
                            class="text-xs font-medium bg-matcha-600 hover:bg-matcha-800 text-white py-2 rounded-lg transition">Convert</button>
                </div>

                @include('leads._panels')
            </li>
        @endforeach
    </ul>

    {{-- Tablet/desktop: table --}}
    <div class="hidden sm:block overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100 {{ $toneClasses['head'] }} text-left">
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
            @foreach ($leads as $lead)
                <tr class="{{ $toneClasses['hover'] }} transition" x-data="{
                    tpOpen: false, convertOpen: false, fpOpen: false,
                    tagged: {{ $lead->focusPoints->pluck('id')->toJson() }}
                }">
                    <td class="px-5 py-3">
                        <p class="font-medium text-gray-800"><x-pdpa-mask>{{ $lead->name }}</x-pdpa-mask></p>
                        @if ($lead->focusPoints->isNotEmpty())
                            <div class="flex flex-wrap gap-1 mt-1">
                                @foreach ($lead->focusPoints as $fp)
                                    <span class="text-xs bg-matcha-50 text-matcha-700 border border-matcha-100 px-2 py-0.5 rounded-full">{{ $fp->title }}</span>
                                @endforeach
                            </div>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-gray-500">
                        @if ($lead->phone)
                            <a href="https://wa.me/{{ $lead->phone }}" target="_blank"
                               class="hover:text-green-600 transition"><x-pdpa-mask>{{ $lead->phone }}</x-pdpa-mask></a>
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
                            <button @click="fpOpen = !fpOpen; tpOpen = false; convertOpen = false"
                                    class="text-xs text-indigo-500 hover:text-indigo-700 transition">Tags</button>
                            <button @click="tpOpen = !tpOpen; fpOpen = false; convertOpen = false"
                                    class="text-xs text-matcha-600 hover:underline">Log</button>
                            <a href="{{ route('leads.edit', $lead) }}"
                               class="text-xs text-gray-400 hover:text-gray-600">Edit</a>
                            <button @click="convertOpen = !convertOpen; tpOpen = false; fpOpen = false"
                                    class="text-xs bg-matcha-600 hover:bg-matcha-800 text-white px-2.5 py-1 rounded-lg transition">
                                Convert
                            </button>
                        </div>

                        @include('leads._panels')
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>
