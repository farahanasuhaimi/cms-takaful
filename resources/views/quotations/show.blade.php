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

    {{-- Notes --}}
    @if ($quotation->notes)
        <p class="text-sm text-gray-500 mb-5 print:mb-3">{{ $quotation->notes }}</p>
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

                {{-- Attribute rows --}}
                @php
                    $attrs = [
                        'type'            => 'Type',
                        'coverage'        => 'Coverage',
                        'umur_matang'     => 'Umur Matang',
                        'pampasan_matang' => 'Pampasan Matang',
                        'kenaikan'        => 'Kenaikan',
                        'plan_type'       => 'Plan',
                        'privilege'       => 'Privilege',
                        'waiver'          => 'Waiver',
                    ];
                @endphp

                @foreach ($attrs as $field => $label)
                    <tr class="bg-white">
                        <td class="border border-gray-300 px-3 py-2 text-gray-500 text-xs" colspan="1"></td>
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
                                @elseif ($field === 'plan_type')
                                    {{ $plan->plan_type === 'investment' ? 'Investment' : 'No Investment' }}
                                @else
                                    {{ $plan->$field ?: '—' }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
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

    {{-- Footer --}}
    <div class="mt-6 text-xs text-gray-400 print:mt-4">
        Prepared by {{ auth()->user()->name }} · {{ now()->format('d M Y') }}
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

</x-app-layout>

<style>
@media print {
    aside, header, .print\:hidden { display: none !important; }
    body { background: white !important; }
    main { padding: 0 !important; overflow: visible !important; }
    table { font-size: 11px; }
}
</style>
