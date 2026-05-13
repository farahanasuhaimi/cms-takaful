<x-app-layout>
    <x-slot name="title">{{ $quotation->title }} · Quotation</x-slot>
    <x-slot name="pageTitle">{{ $quotation->title }}</x-slot>
    <x-slot name="actions">
        <div class="flex items-center gap-2">
            <a href="{{ route('quotations.index') }}"
               class="text-xs bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 px-3 py-1.5 rounded-lg transition print:hidden">
                ← Back
            </a>
            <a href="{{ route('quotations.edit', $quotation) }}"
               class="text-xs bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 px-3 py-1.5 rounded-lg transition print:hidden">
                Edit
            </a>
            <form method="POST" action="{{ route('quotations.duplicate', $quotation) }}" class="print:hidden">
                @csrf
                <button type="submit"
                        class="text-xs bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 px-3 py-1.5 rounded-lg transition">
                    Duplicate
                </button>
            </form>
            <button onclick="window.print()"
                    class="text-xs bg-matcha-600 hover:bg-matcha-800 text-white font-medium px-3 py-1.5 rounded-lg transition print:hidden">
                Print / Save PDF
            </button>
        </div>
    </x-slot>

    {{-- Print-only header --}}
    <div class="hidden print:block mb-6 border-b border-gray-300 pb-4">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-lg font-bold text-gray-800">{{ $quotation->title }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Disediakan oleh: {{ auth()->user()->name }}</p>
            </div>
            <div class="text-right text-xs text-gray-500">
                <p>{{ now()->format('d M Y') }}</p>
                <p class="mt-0.5">AIA PUBLIC Takaful Bhd.</p>
            </div>
        </div>
    </div>

    {{-- Internal notes (screen only) --}}
    @if ($quotation->notes)
        <p class="text-sm text-gray-500 mb-5 print:hidden">{{ $quotation->notes }}</p>
    @endif

    {{-- Comparison Table --}}
    <div class="overflow-x-auto">
        <table class="w-full border-collapse text-sm print:text-xs">

            {{-- Category header row --}}
            <thead>
                <tr>
                    <th class="border border-gray-300 bg-gray-50 px-3 py-2" colspan="2"></th>
                    @foreach ($grouped as $category => $catPlans)
                        <th class="border border-gray-300 bg-matcha-700 text-white px-3 py-2 text-center font-semibold"
                            colspan="{{ $catPlans->count() }}">
                            {{ $category ?: 'Plans' }}
                        </th>
                    @endforeach
                </tr>

                {{-- Plan name header row --}}
                <tr>
                    <th class="border border-gray-300 bg-gray-50 px-3 py-2 text-left text-gray-500 font-medium w-28"></th>
                    <th class="border border-gray-300 bg-gray-50 px-3 py-2 text-center text-gray-500 font-medium w-16">Age</th>
                    @foreach ($plans as $plan)
                        <th class="border border-gray-300 bg-gray-100 px-3 py-2 text-center text-gray-700 font-semibold min-w-[120px]">
                            {{ $plan->plan_name }}
                        </th>
                    @endforeach
                </tr>
            </thead>

            <tbody>
                {{-- Person rows (premiums) --}}
                @foreach ($people as $person)
                    <tr class="{{ $loop->odd ? 'bg-blue-50' : 'bg-white' }}">
                        <td class="border border-gray-300 px-3 py-2 font-medium text-gray-800">{{ $person->name }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center text-gray-600">{{ $person->age }}</td>
                        @foreach ($plans as $plan)
                            @php $amount = $premiumMap[$plan->id][$person->id] ?? null; @endphp
                            <td class="border border-gray-300 px-3 py-2 text-center font-semibold text-matcha-700">
                                {{ $amount !== null ? 'RM'.number_format($amount, 2) : '—' }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach

                {{-- Total monthly row --}}
                @if ($people->count() > 1)
                    @php
                        $hasAnyPremium = collect($plans)->contains(fn($plan) =>
                            collect($people)->contains(fn($person) => isset($premiumMap[$plan->id][$person->id]))
                        );
                    @endphp
                    @if ($hasAnyPremium)
                        <tr class="bg-matcha-50 font-semibold">
                            <td class="border border-gray-300 px-3 py-2 text-gray-700 text-xs">Total</td>
                            <td class="border border-gray-300 px-3 py-2 text-center text-gray-500 text-xs">—</td>
                            @foreach ($plans as $plan)
                                @php
                                    $total = collect($people)->sum(fn($person) => $premiumMap[$plan->id][$person->id] ?? 0);
                                @endphp
                                <td class="border border-gray-300 px-3 py-2 text-center text-matcha-800 text-xs">
                                    {{ $total > 0 ? 'RM'.number_format($total, 2) : '—' }}
                                </td>
                            @endforeach
                        </tr>
                    @endif
                @endif

                {{-- Attribute rows — only shown when at least one plan has a value --}}
                @php
                    $attrs = [
                        'type'            => 'Type',
                        'room_board'      => 'Room & Board',
                        'coverage'        => 'Coverage',
                        'kenaikan'        => 'Kenaikan',
                        'plan_type'       => 'Plan',
                        'privilege'       => 'Privilege',
                        'umur_matang'     => 'Umur Matang',
                        'pampasan_matang' => 'Pampasan Matang',
                        'waiver'          => 'Waiver',
                    ];
                    $hasValue = fn($field) => $plans->contains(fn($p) => filled($p->$field));
                @endphp

                @foreach ($attrs as $field => $label)
                    @if ($field === 'waiver' || $hasValue($field))
                        <tr class="bg-white">
                            <td class="border border-gray-300 px-3 py-2 text-gray-500 text-xs"></td>
                            <td class="border border-gray-300 px-3 py-2 text-gray-600 font-medium text-xs">{{ $label }}</td>
                            @foreach ($plans as $plan)
                                <td class="border border-gray-300 px-3 py-2 text-center text-gray-700 text-xs">
                                    @if ($field === 'waiver')
                                        @if ($plan->waiver === 'yes') <span class="text-green-600 text-base">✅</span>
                                        @else <span class="text-red-500 text-base">❌</span>
                                        @endif
                                    @elseif ($field === 'kenaikan')
                                        @if (!$plan->kenaikan) <span class="text-red-500 text-base">❌</span>
                                        @elseif ($plan->kenaikan === 'yes') <span class="text-green-600 text-base">✅</span>
                                        @else {{ $plan->kenaikan }}
                                        @endif
                                    @else
                                        {{ $plan->$field ?: '—' }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endif
                @endforeach

                {{-- Notes row — only if any plan has notes --}}
                @if ($plans->filter(fn($p) => $p->notes)->isNotEmpty())
                    <tr class="bg-gray-50">
                        <td class="border border-gray-300 px-3 py-2 text-gray-500 text-xs"></td>
                        <td class="border border-gray-300 px-3 py-2 text-gray-600 font-medium text-xs">Notes</td>
                        @foreach ($plans as $plan)
                            <td class="border border-gray-300 px-3 py-2 text-center text-gray-500 text-xs">{{ $plan->notes ?: '' }}</td>
                        @endforeach
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    {{-- Prospect block --}}
    @if ($quotation->prospect_name || $quotation->prospect_phone || $quotation->prospect_notes)
        <div class="mt-6 border border-gray-200 rounded-xl p-5 bg-gray-50 print:mt-5 print:rounded-none print:border-gray-300">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Untuk Pertimbangan</p>
            @if ($quotation->prospect_name || $quotation->prospect_phone)
                <div class="flex flex-wrap gap-6 mb-3 text-sm">
                    @if ($quotation->prospect_name)
                        <div>
                            <span class="text-xs text-gray-400">Nama</span>
                            <p class="font-medium text-gray-800">{{ $quotation->prospect_name }}</p>
                        </div>
                    @endif
                    @if ($quotation->prospect_phone)
                        <div>
                            <span class="text-xs text-gray-400">No. Telefon</span>
                            <p class="font-medium text-gray-800">{{ $quotation->prospect_phone }}</p>
                        </div>
                    @endif
                </div>
            @endif
            @if ($quotation->prospect_notes)
                <p class="text-sm text-gray-600 leading-relaxed">{{ $quotation->prospect_notes }}</p>
            @endif
        </div>
    @endif

    {{-- Print-only disclaimer --}}
    <div class="hidden print:block mt-6 text-xs text-gray-400 border-t border-gray-200 pt-3">
        <p>Dokumen ini adalah ilustrasi sahaja dan bukan kontrak insurans. Caruman sebenar tertakluk kepada penilaian risiko dan terma polisi AIA PUBLIC Takaful Bhd. Sah sebagai rujukan sahaja.</p>
    </div>

    {{-- Footer --}}
    <div class="mt-6 text-xs text-gray-400 print:mt-3">
        Disediakan oleh {{ auth()->user()->name }} · {{ now()->format('d M Y') }}
    </div>

    {{-- Delete --}}
    <div class="mt-6 print:hidden" x-data="{ del: false }">
        <button @click="del = true" x-show="!del"
                class="text-xs text-gray-300 hover:text-strawberry-400 transition">Delete this quotation</button>
        <div x-show="del" class="flex items-center gap-2">
            <span class="text-xs text-gray-500">Delete permanently?</span>
            <form method="POST" action="{{ route('quotations.destroy', $quotation) }}">
                @csrf @method('DELETE')
                <button type="submit" class="text-xs text-strawberry-600 font-medium hover:underline">Yes, delete</button>
            </form>
            <button @click="del = false" class="text-xs text-gray-400">Cancel</button>
        </div>
    </div>

    {{-- Print-only watermark --}}
    <div class="watermark" aria-hidden="true">
        @for ($i = 0; $i < 20; $i++)
            <div class="watermark-tile">
                <span>{{ auth()->user()->name }}</span>
                <span>Rujukan Sahaja</span>
            </div>
        @endfor
    </div>

</x-app-layout>

<style>
/* ── Screen ── */
.watermark { display: none; }

/* ── Print ── */
@media print {
    aside, header, .print\:hidden { display: none !important; }
    body { background: white !important; }
    main { padding: 0 !important; overflow: visible !important; }
    table { font-size: 11px; }

    /* Watermark */
    .watermark {
        display: block;
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        z-index: 9999;
        pointer-events: none;
        overflow: hidden;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        gap: 0;
    }
    .watermark-tile {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 200px;
        height: 120px;
        transform: rotate(-30deg);
        opacity: 0.07;
        color: #000;
        font-size: 11px;
        font-weight: 600;
        text-align: center;
        line-height: 1.5;
        letter-spacing: 0.03em;
        flex-shrink: 0;
    }
}
</style>
