<x-app-layout>
    <x-slot name="title">{{ $quotation->title }} · Comparison</x-slot>
    <x-slot name="pageTitle">Comparison</x-slot>
    <x-slot name="actions">
        <div class="flex items-center gap-2">
            <a href="{{ route('quotations.show', $quotation) }}"
               class="text-xs bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 px-3 py-1.5 rounded-lg transition print:hidden">
                ← Back to Quotation
            </a>
            <button onclick="window.print()"
                    class="text-xs bg-matcha-600 hover:bg-matcha-800 text-white font-medium px-3 py-1.5 rounded-lg transition print:hidden">
                Print / Save PDF
            </button>
        </div>
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@400;500;700&display=swap');
        .comparison-doc { font-family: 'DM Sans', sans-serif; }
        .comparison-doc .font-serif-brand { font-family: 'DM Serif Display', serif; }

        @page { size: A4 portrait; margin: 15mm 12mm; }
        @media print {
            aside, header, nav, .print\:hidden { display: none !important; }
            body { background: white !important; }
            main { padding: 0 !important; margin: 0 !important; overflow: visible !important; }
        }
    </style>

    <div class="comparison-doc max-w-3xl mx-auto">

        @if ($plans->isEmpty())
            <div class="bg-white border border-gray-200 rounded-2xl p-8 text-center">
                <p class="text-gray-600 font-medium">No Hibah plans found in this quotation yet.</p>
                <p class="text-sm text-gray-400 mt-2">
                    This comparison layout is currently predefined for Hibah — set a plan's
                    <span class="font-medium text-gray-500">Category</span> field to "Hibah" in
                    <a href="{{ route('quotations.edit', $quotation) }}" class="text-matcha-700 underline">Edit</a>
                    to see it here. Other categories can get their own predefined layout later.
                </p>
            </div>
        @else

            {{-- Hero header --}}
            <div class="rounded-3xl p-8 text-white" style="background: linear-gradient(135deg, #1a3324 0%, #2d5a3d 60%, #4a7c59 100%);">
                <span class="inline-block bg-strawberry-600 text-white text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full">
                    Draf Perbandingan
                </span>
                <h1 class="font-serif-brand text-3xl mt-4 leading-tight">{{ $quotation->title }}</h1>
                <p class="text-strawberry-300 font-semibold text-sm mt-1 uppercase tracking-wide">
                    Perbandingan Hibah — {{ $plans->pluck('plan_name')->unique()->implode(' vs ') }}
                </p>
                @if ($people->isNotEmpty())
                    <p class="text-matcha-100 text-sm mt-4">
                        {{ $people->map(fn($p) => $p->name . ($p->age ? ' · ' . $p->age . ' tahun' : ''))->implode('   |   ') }}
                    </p>
                @endif
            </div>

            {{-- Plan boxes --}}
            <div class="grid gap-5 mt-8" style="grid-template-columns: repeat({{ min($plans->count(), 3) }}, minmax(0, 1fr));">
                @foreach ($plans as $plan)
                    <div class="bg-white rounded-2xl border border-matcha-100 shadow-sm p-6 text-center">
                        <p class="font-serif-brand text-strawberry-700 text-lg leading-tight">{{ $plan->plan_name }}</p>
                        @if ($plan->coverage)
                            <span class="inline-block bg-matcha-50 text-matcha-800 text-xs font-semibold rounded-full px-3 py-1 mt-2">
                                Perlindungan: {{ $plan->coverage }}
                            </span>
                        @endif
                        <div class="border-t border-gray-100 my-4"></div>
                        <p class="text-xs text-gray-400 uppercase tracking-wider">Caruman Bulanan</p>
                        <div class="mt-2 border-2 border-dashed border-gray-200 rounded-xl py-3 text-gray-300 font-bold text-xl">
                            RM ______
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($plans->count() === 2)
                @php [$planA, $planB] = $plans->values()->all(); @endphp

                {{-- Perbezaan Utama bar --}}
                <div class="rounded-2xl p-5 text-center text-white mt-6" style="background:#1a3324;">
                    <p class="font-semibold">Naik taraf {{ $planA->plan_name }} ke {{ $planB->plan_name }}</p>
                    <p class="text-strawberry-300 text-sm italic mt-1">Perbezaan caruman bulanan: diisi sendiri</p>
                </div>

                {{-- 3-column key stat strip --}}
                <div class="bg-white rounded-2xl border border-gray-200 p-5 mt-5">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 text-center">Perbezaan Utama</p>
                    <div class="grid grid-cols-3 divide-x divide-gray-100 text-center">
                        <div>
                            <p class="text-xs text-gray-400">Perlindungan {{ $planA->plan_name }}</p>
                            <p class="font-bold text-matcha-800 mt-1">{{ $planA->coverage ?: '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Umur Matang</p>
                            <p class="font-bold text-matcha-800 mt-1">{{ $planA->umur_matang ?: $planB->umur_matang ?: '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Perlindungan {{ $planB->plan_name }}</p>
                            <p class="font-bold text-matcha-800 mt-1">{{ $planB->coverage ?: '—' }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Benefit category cards --}}
            @php
                $fieldRows = function (array $fields) use ($plans) {
                    $rows = [];
                    foreach ($fields as $field => $label) {
                        if (! $plans->contains(fn($p) => filled($p->$field))) continue;
                        $rows[$label] = $plans->map(fn($p) => [
                            'plan'  => $p->plan_name,
                            'value' => $field === 'waiver'
                                ? ($p->waiver === 'yes' ? '✓ Ya' : '✗ Tiada')
                                : ($p->$field ?: '—'),
                        ])->all();
                    }
                    return $rows;
                };

                $card1 = $fieldRows(['coverage' => 'Jumlah Perlindungan', 'pampasan_matang' => 'Pampasan Matang']);
                $card2 = $fieldRows(['type' => 'Jenis Pelan', 'plan_type' => 'Tempoh Pelan', 'umur_matang' => 'Umur Matang', 'kenaikan' => 'Kenaikan Perlindungan']);
                $card3 = $fieldRows(['privilege' => 'Privilej', 'waiver' => 'Waiver Caruman']);

                $dynamicKeys = collect();
                foreach ($plans as $p) {
                    foreach (array_keys($p->attributes ?? []) as $k) {
                        if (! $dynamicKeys->contains($k)) $dynamicKeys->push($k);
                    }
                }
                $card4 = [];
                foreach ($dynamicKeys as $key) {
                    $card4[$key] = $plans->map(fn($p) => [
                        'plan'  => $p->plan_name,
                        'value' => filled($p->attributes[$key] ?? null) ? $p->attributes[$key] : '—',
                    ])->all();
                }
                foreach ($plans as $p) {
                    if ($p->notes) $card4['Nota — ' . $p->plan_name] = [['plan' => $p->plan_name, 'value' => $p->notes]];
                }
            @endphp

            <div class="grid sm:grid-cols-2 gap-5 mt-8">
                @foreach ([
                    'Perlindungan Utama' => $card1,
                    'Tempoh Pelan'       => $card2,
                    'Faedah Tambahan'    => $card3,
                    'Maklumat Lanjut'    => $card4,
                ] as $title => $rows)
                    @if (! empty($rows))
                        <div class="bg-white rounded-2xl border border-gray-200 p-5">
                            <p class="font-serif-brand text-matcha-800 font-bold text-sm uppercase tracking-wide border-b border-gray-100 pb-2 mb-3">
                                {{ $title }}
                            </p>
                            <div class="space-y-3">
                                @foreach ($rows as $label => $values)
                                    <div>
                                        <p class="text-xs text-gray-400">{{ $label }}</p>
                                        @foreach ($values as $v)
                                            <p class="text-sm text-gray-700">
                                                @if ($plans->count() > 1)
                                                    <span class="text-gray-400">{{ $v['plan'] }}:</span>
                                                @endif
                                                {{ $v['value'] }}
                                            </p>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Maklumat Penting --}}
            <div class="bg-matcha-50 border border-matcha-200 rounded-2xl p-5 mt-8 text-xs text-gray-600 space-y-1.5">
                <p class="font-semibold text-matcha-800 uppercase tracking-wide text-[11px] mb-1">Maklumat Penting</p>
                <p>• Hibah adalah pelan takaful berasingan (standalone), bukan sebahagian daripada pelan perubatan.</p>
                <p>• Caruman tertakluk kepada perubahan disebabkan oleh umur dan/atau penilaian underwriting semasa permohonan.</p>
                <p>• Sila rujuk Product Disclosure Sheet (PDS) rasmi untuk terma dan syarat penuh.</p>
            </div>

            {{-- Footer --}}
            <div class="flex items-end justify-between border-t border-gray-200 pt-4 mt-6 text-xs text-gray-400">
                <div>
                    <p>Disediakan oleh:</p>
                    <p class="font-semibold text-gray-600">{{ $card['name'] }}</p>
                    @if ($card['phone'])<p>{{ $card['phone'] }}</p>@endif
                </div>
                <div class="text-right">
                    <p>Draf untuk semakan. Tertakluk kepada terma,</p>
                    <p>penilaian dan kelulusan AIA.</p>
                </div>
            </div>

        @endif
    </div>

</x-app-layout>
