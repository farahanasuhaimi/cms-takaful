<x-app-layout>
    <x-slot name="title">{{ $quotation->title }} · Social Card</x-slot>
    <x-slot name="pageTitle">Social Card</x-slot>
    <x-slot name="actions">
        <a href="{{ route('quotations.show', $quotation) }}"
           class="text-xs bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 px-3 py-1.5 rounded-lg transition">
            ← Back to Quotation
        </a>
    </x-slot>

    @vite(['resources/js/social-card.js'])

    <div class="flex flex-col lg:flex-row gap-6 items-start">

        {{-- Controls --}}
        <div class="w-full lg:w-72 flex-shrink-0 space-y-4">

            @if ($plans->count() > 1)
                <form method="GET" action="{{ route('quotations.social-card', $quotation) }}"
                      class="bg-white border border-gray-200 rounded-xl p-4">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Plan</label>
                    <select name="plan" onchange="this.form.submit()"
                            class="mt-1.5 w-full text-sm border-gray-200 rounded-lg focus:ring-matcha-400 focus:border-matcha-400">
                        @foreach ($plans as $p)
                            <option value="{{ $p->id }}" @selected($plan && $plan->id === $p->id)>{{ $p->plan_name }}</option>
                        @endforeach
                    </select>
                </form>
            @endif

            <form method="POST" action="{{ route('quotations.social-card.settings', $quotation) }}"
                  class="bg-white border border-gray-200 rounded-xl p-4 space-y-3">
                @csrf
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Contact Card</p>
                <input type="hidden" name="plan" value="{{ $plan->id ?? '' }}">
                <div>
                    <label class="text-xs text-gray-500">Display name</label>
                    <input type="text" name="card_name" value="{{ $card['name'] }}" maxlength="100"
                           class="mt-1 w-full text-sm border-gray-200 rounded-lg focus:ring-matcha-400 focus:border-matcha-400">
                </div>
                <div>
                    <label class="text-xs text-gray-500">Phone</label>
                    <input type="text" name="card_phone" value="{{ $card['phone'] }}" maxlength="30"
                           placeholder="013 252 2587"
                           class="mt-1 w-full text-sm border-gray-200 rounded-lg focus:ring-matcha-400 focus:border-matcha-400">
                </div>
                <div>
                    <label class="text-xs text-gray-500">Website</label>
                    <input type="text" name="card_website" value="{{ $card['website'] }}" maxlength="100"
                           placeholder="DrTakaful.com"
                           class="mt-1 w-full text-sm border-gray-200 rounded-lg focus:ring-matcha-400 focus:border-matcha-400">
                </div>
                <div>
                    <label class="text-xs text-gray-500">Bottom banner text</label>
                    <textarea name="card_cta" rows="2" maxlength="200"
                              class="mt-1 w-full text-sm border-gray-200 rounded-lg focus:ring-matcha-400 focus:border-matcha-400">{{ $card['cta'] }}</textarea>
                </div>
                <button type="submit"
                        class="w-full text-xs bg-matcha-100 hover:bg-matcha-200 text-matcha-800 font-medium px-3 py-2 rounded-lg transition">
                    Save contact card
                </button>
            </form>

            <button onclick="downloadSocialCard('{{ \Illuminate\Support\Str::slug($quotation->title) }}-card.png')"
                    class="w-full text-sm bg-strawberry-600 hover:bg-strawberry-800 text-white font-semibold px-4 py-2.5 rounded-lg transition shadow-sm">
                ⬇ Download as Image
            </button>
            <p class="text-xs text-gray-400 leading-relaxed">
                Downloads a 1080×1080 PNG — sized for Instagram/Facebook feed posts. Rujukan sahaja, bukan kontrak insurans.
            </p>
        </div>

        {{-- Card preview --}}
        <div class="flex-1 flex justify-center overflow-x-auto py-4">
            <div id="social-card" class="relative flex-shrink-0 overflow-hidden rounded-[28px] flex flex-col p-4 gap-3"
                 style="width:540px;height:540px;background:linear-gradient(135deg,#fceef2 0%,#fef9f6 45%,#e8f0eb 100%);">

                {{-- Decorative background layer (never affects content flow) --}}
                <div class="absolute inset-0 z-0 overflow-hidden">
                    <div class="absolute -top-10 -left-10 w-40 h-40 rounded-full bg-strawberry-100 opacity-60"></div>
                    <div class="absolute -bottom-16 -right-10 w-48 h-48 rounded-full bg-matcha-100 opacity-70"></div>
                    <svg class="absolute top-[210px] left-8 w-24 h-24 text-matcha-200 opacity-70" viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M50 90 C 50 60, 40 40, 20 20" stroke-linecap="round"/>
                        <path d="M35 45 C 45 40, 55 40, 60 30" stroke-linecap="round"/>
                        <path d="M28 60 C 38 57, 46 57, 52 50" stroke-linecap="round"/>
                        <ellipse cx="18" cy="16" rx="10" ry="6" transform="rotate(-40 18 16)" fill="currentColor" stroke="none" opacity="0.8"/>
                        <ellipse cx="58" cy="27" rx="8" ry="5" transform="rotate(-20 58 27)" fill="currentColor" stroke="none" opacity="0.8"/>
                    </svg>
                    <svg class="absolute top-[280px] left-[150px] w-6 h-6 text-strawberry-300 opacity-80" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 17.5s-6.5-4.2-8.5-8.1C.3 6.7 1.6 3.5 4.6 3c1.9-.3 3.7.7 4.4 2.2C9.7 3.7 11.5 2.7 13.4 3c3 .5 4.3 3.7 3.1 6.4-2 3.9-8.5 8.1-8.5 8.1z"/>
                    </svg>
                </div>

                {{-- Foreground content — normal flow, never overlaps regardless of text length --}}
                <div class="relative z-10 flex flex-col h-full gap-3 min-h-0">

                    {{-- Contact bar --}}
                    <div class="flex-shrink-0 bg-white/90 rounded-full shadow-sm px-4 py-2 flex items-center gap-3 text-[11px] font-medium text-gray-700 flex-wrap">
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3 text-strawberry-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/></svg>
                            {{ $card['name'] }}
                        </span>
                        @if ($card['phone'])
                            <span class="text-gray-300">|</span>
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3 text-matcha-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/></svg>
                                {{ $card['phone'] }}
                            </span>
                        @endif
                        @if ($card['website'])
                            <span class="text-gray-300">|</span>
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3 text-strawberry-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6H9V4.5a1.5 1.5 0 011.5-1.5h.5v2a1 1 0 001 1h1a1 1 0 011 1v.5a1 1 0 01-1 1h-.5a1 1 0 00-1 1v1a1 1 0 01-1 1H9a1 1 0 00-1 1v2.5a1 1 0 01-.485.857A6 6 0 014.332 8.027z" clip-rule="evenodd"/></svg>
                                {{ $card['website'] }}
                            </span>
                        @endif
                    </div>

                    {{-- Title --}}
                    <div class="flex-shrink-0 px-2">
                        <p class="{{ \Illuminate\Support\Str::length($quotation->title) > 45 ? 'text-lg' : 'text-2xl' }} font-extrabold leading-tight text-matcha-900 line-clamp-2">
                            {{ $quotation->title }}
                        </p>
                        @if ($plan)
                            <p class="text-strawberry-600 font-bold text-xs mt-1 line-clamp-1">{{ $plan->plan_name }}</p>
                        @endif
                    </div>

                    {{-- Table + highlights row --}}
                    <div class="flex-shrink-0 flex gap-3 px-2">
                        {{-- Comparison table --}}
                        <div class="w-[210px] flex-shrink-0 rounded-xl overflow-hidden shadow-sm border border-white/60 self-start">
                            <div class="grid grid-cols-2">
                                <div class="bg-strawberry-600 text-white text-[10px] font-bold text-center py-1.5">UMUR</div>
                                <div class="bg-matcha-700 text-white text-[10px] font-bold text-center py-1.5">BULANAN</div>
                            </div>
                            @foreach ($people as $person)
                                <div class="grid grid-cols-2 {{ $loop->even ? 'bg-white/80' : 'bg-white/50' }}">
                                    <div class="text-[11px] text-gray-700 text-center py-1.5 px-1">
                                        {{ $person->name }}@if($person->age) · {{ $person->age }}thn @endif
                                    </div>
                                    <div class="text-[12px] font-bold text-strawberry-700 text-center py-1.5">
                                        @php $amt = $premiumByPerson[$person->id] ?? null; @endphp
                                        {{ $amt !== null ? 'RM'.number_format($amt, 2) : '—' }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Highlights panel --}}
                        <div class="flex-1 min-w-0 space-y-1.5 self-start">
                            @foreach (array_slice($highlights, 0, 3) as $h)
                                <div class="flex items-start gap-1.5 bg-white/70 rounded-lg px-2 py-1.5">
                                    <svg class="w-3.5 h-3.5 text-matcha-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    <span class="text-[10px] leading-snug text-gray-700 line-clamp-2">{{ $h }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Spacer — absorbs leftover height so nothing below ever collides with content above --}}
                    <div class="flex-1 min-h-0"></div>

                    {{-- Price tag --}}
                    @if ($card['phone'] || $card['website'])
                        <div class="flex-shrink-0 flex justify-end px-2">
                            <div class="bg-amber-400 border-2 border-dashed border-amber-600 rounded-xl px-4 py-2.5 shadow-md text-center transform rotate-6">
                                @if ($card['website'])<p class="text-[11px] font-extrabold text-matcha-900 leading-tight">{{ $card['website'] }}</p>@endif
                                @if ($card['phone'])<p class="text-[11px] font-extrabold text-matcha-900 leading-tight">{{ $card['phone'] }}</p>@endif
                            </div>
                        </div>
                    @endif

                    {{-- Bottom CTA banner --}}
                    @if ($card['cta'])
                        <div class="flex-shrink-0 bg-matcha-900 rounded-xl px-4 py-3">
                            <p class="text-white text-[11px] leading-snug font-medium line-clamp-3">{{ $card['cta'] }}</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

</x-app-layout>
