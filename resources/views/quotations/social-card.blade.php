<x-app-layout>
    <x-slot name="title">{{ $quotation->title }} · Social Post</x-slot>
    <x-slot name="pageTitle">Social Post</x-slot>
    <x-slot name="actions">
        <a href="{{ route('quotations.show', $quotation) }}"
           class="text-xs bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 px-3 py-1.5 rounded-lg transition">
            ← Back to Quotation
        </a>
    </x-slot>

    @vite(['resources/js/social-card.js'])

    @if ($plans->isEmpty())
        <div class="bg-white border border-gray-200 rounded-xl p-6 text-sm text-gray-600">
            This quotation has no plans yet. Add a plan to the quotation first.
        </div>
    @else
    <div x-data="socialPost({
            quotationId: {{ $quotation->id }},
            people: @js($people),
            plans: @js($plans),
            selectedPlanId: {{ (int) $selectedPlanId }},
         })"
         class="flex flex-col lg:flex-row gap-6 items-start">

        {{-- ================= Controls ================= --}}
        <div class="w-full lg:w-80 flex-shrink-0 space-y-4">

            {{-- Layout --}}
            <div class="bg-white border border-gray-200 rounded-xl p-4 space-y-2">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Layout</p>
                <div class="grid grid-cols-3 gap-1 rounded-lg bg-gray-100 p-1">
                    @foreach (['hero' => 'Hero price', 'table' => 'Age table', 'family' => 'Family total'] as $key => $label)
                        <button type="button" @click="layout = '{{ $key }}'"
                                class="rounded-md py-1.5 text-xs font-semibold transition"
                                :class="layout === '{{ $key }}' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
                <p class="text-[11px] text-gray-400" x-show="layout === 'hero'">One big price as the hook.</p>
                <p class="text-[11px] text-gray-400" x-show="layout === 'table'">Price by age, up to 4 rows.</p>
                <p class="text-[11px] text-gray-400" x-show="layout === 'family'">Each person plus a family total, up to 5 rows.</p>
            </div>

            {{-- Plan + headline --}}
            <div class="bg-white border border-gray-200 rounded-xl p-4 space-y-3">
                @if ($plans->count() > 1)
                    <div>
                        <label for="sp-plan" class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Plan</label>
                        <select id="sp-plan" :value="planId" @change="selectPlan($event.target.value)"
                                class="mt-1.5 w-full text-sm border-gray-200 rounded-lg focus:ring-matcha-400 focus:border-matcha-400">
                            <template x-for="p in plans" :key="p.id">
                                <option :value="p.id" x-text="p.name" :selected="p.id === planId"></option>
                            </template>
                        </select>
                    </div>
                @endif
                <div>
                    <label for="sp-headline" class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Headline</label>
                    <input id="sp-headline" type="text" x-model="headline" maxlength="60"
                           class="mt-1.5 w-full text-sm border-gray-200 rounded-lg focus:ring-matcha-400 focus:border-matcha-400">
                </div>
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" x-model="showPlanName" class="rounded border-gray-300 text-matcha-600 focus:ring-matcha-400">
                    Show plan name
                </label>
            </div>

            {{-- People --}}
            <div class="bg-white border border-gray-200 rounded-xl p-4 space-y-2">
                <div class="flex items-baseline justify-between">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider"
                       x-text="layout === 'hero' ? 'Whose price?' : 'Who goes on the post'"></p>
                    <p x-show="layout !== 'hero'" class="text-[11px] text-gray-400"
                       x-text="'max ' + maxRows()"></p>
                </div>
                <p class="text-[11px] text-gray-400">Names stay here. The post shows only the label you type, e.g. "Umur 32" or "Ibu 33".</p>

                <template x-for="row in rows" :key="row.id">
                    <div class="flex items-center gap-2 rounded-lg border border-gray-100 px-2 py-1.5">
                        <input x-show="layout === 'hero'" type="radio" name="sp-hero" :value="row.id"
                               :checked="heroId === row.id" @change="heroId = row.id"
                               class="border-gray-300 text-matcha-600 focus:ring-matcha-400">
                        <input x-show="layout !== 'hero'" type="checkbox" x-model="row.on"
                               class="rounded border-gray-300 text-matcha-600 focus:ring-matcha-400">
                        <div class="min-w-0 flex-1">
                            <p class="text-[11px] text-gray-400 truncate"
                               :class="$store.privacy.enabled ? 'blur-sm select-none' : ''"
                               x-text="row.name + (row.age ? ' · ' + row.age : '')"></p>
                            <input type="text" x-model="row.label" maxlength="20" aria-label="Label on post"
                                   class="w-full text-sm border-0 border-b border-transparent focus:border-matcha-400 focus:ring-0 px-0 py-0.5">
                        </div>
                        <span class="text-xs font-semibold text-gray-600 tabular-nums" x-text="money(premium(row.id))"></span>
                    </div>
                </template>

                <p x-show="layout === 'family' && familyTotal().missing > 0" x-cloak class="text-[11px] text-strawberry-800 bg-strawberry-50 rounded-md px-2 py-1">
                    Someone on the post has no price for this plan, so the family total leaves them out.
                </p>
                <p x-show="overflowCount() > 0" x-cloak class="text-[11px] text-amber-700 bg-amber-50 rounded-md px-2 py-1">
                    <span x-text="overflowCount()"></span> ticked row(s) won't fit and are left off. Untick some, or make a second post.
                </p>
            </div>

            {{-- Highlights --}}
            <div class="bg-white border border-gray-200 rounded-xl p-4 space-y-2">
                <div class="flex items-baseline justify-between">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Selling points</p>
                    <p class="text-[11px] text-gray-400">up to 3</p>
                </div>
                <template x-for="(h, i) in highlights()" :key="i">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" x-model="h.on" class="rounded border-gray-300 text-matcha-600 focus:ring-matcha-400">
                        <input type="text" x-model="h.text" maxlength="80"
                               class="flex-1 min-w-0 text-sm border-gray-200 rounded-lg py-1 focus:ring-matcha-400 focus:border-matcha-400">
                        <button type="button" @click="removeHighlight(i)" class="text-gray-300 hover:text-strawberry-600 text-xs" aria-label="Remove">✕</button>
                    </div>
                </template>
                <form @submit.prevent="addHighlight()" class="flex gap-2">
                    <input type="text" x-model="customHighlight" maxlength="80" placeholder="Add your own…"
                           class="flex-1 min-w-0 text-sm border-gray-200 rounded-lg py-1 focus:ring-matcha-400 focus:border-matcha-400">
                    <button type="submit" class="text-xs font-medium text-matcha-800 bg-matcha-50 hover:bg-matcha-100 rounded-lg px-3">Add</button>
                </form>
                <p x-show="highlights().filter(h => h.on).length > 3" x-cloak class="text-[11px] text-amber-700">Only the first 3 ticked appear on the post.</p>
            </div>

            {{-- Download --}}
            <div class="space-y-2">
                <button onclick="downloadSocialCard('{{ \Illuminate\Support\Str::slug($quotation->title) }}-post.png')"
                        class="w-full text-sm bg-strawberry-600 hover:bg-strawberry-800 text-white font-semibold px-4 py-2.5 rounded-lg transition shadow-sm">
                    ⬇ Download post (1080×1350)
                </button>
                <div class="flex items-center justify-between text-[11px] text-gray-400">
                    <span>Your choices are remembered on this device.</span>
                    <button type="button" @click="resetPost()" class="hover:text-gray-700 underline">Reset</button>
                </div>
            </div>

            {{-- Contact card (shared by all quotations) --}}
            <details class="bg-white border border-gray-200 rounded-xl p-4 group">
                <summary class="text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer select-none">Contact &amp; footer text</summary>
                <form method="POST" action="{{ route('quotations.social-card.settings', $quotation) }}" class="mt-3 space-y-3">
                    @csrf
                    <input type="hidden" name="plan" :value="planId">
                    <div>
                        <label class="text-xs text-gray-500">Display name</label>
                        <input type="text" name="card_name" value="{{ $card['name'] }}" maxlength="100"
                               class="mt-1 w-full text-sm border-gray-200 rounded-lg focus:ring-matcha-400 focus:border-matcha-400">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Phone</label>
                        <input type="text" name="card_phone" value="{{ $card['phone'] }}" maxlength="30" placeholder="013 252 2587"
                               class="mt-1 w-full text-sm border-gray-200 rounded-lg focus:ring-matcha-400 focus:border-matcha-400">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Website</label>
                        <input type="text" name="card_website" value="{{ $card['website'] }}" maxlength="100" placeholder="DrTakaful.com"
                               class="mt-1 w-full text-sm border-gray-200 rounded-lg focus:ring-matcha-400 focus:border-matcha-400">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Bottom banner text (leave empty to hide)</label>
                        <textarea name="card_cta" rows="2" maxlength="200"
                                  class="mt-1 w-full text-sm border-gray-200 rounded-lg focus:ring-matcha-400 focus:border-matcha-400">{{ $card['cta'] }}</textarea>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Small print</label>
                        <textarea name="card_disclaimer" rows="2" maxlength="200"
                                  class="mt-1 w-full text-sm border-gray-200 rounded-lg focus:ring-matcha-400 focus:border-matcha-400">{{ $card['disclaimer'] }}</textarea>
                    </div>
                    <button type="submit"
                            class="w-full text-xs bg-matcha-100 hover:bg-matcha-200 text-matcha-800 font-medium px-3 py-2 rounded-lg transition">
                        Save contact &amp; footer
                    </button>
                </form>
            </details>
        </div>

        {{-- ================= Post preview (540×675 → 1080×1350) ================= --}}
        <div class="flex-1 w-full flex justify-center overflow-x-auto py-2">
            <div id="social-card" class="relative flex-shrink-0 overflow-hidden rounded-[28px]"
                 style="width:540px;height:675px;background:linear-gradient(160deg,#fceef2 0%,#fef9f6 50%,#e8f0eb 100%);">

                {{-- Decoration (behind content) --}}
                <div class="absolute inset-0 overflow-hidden">
                    <div class="absolute -top-14 -right-12 w-48 h-48 rounded-full bg-strawberry-100 opacity-60"></div>
                    <div class="absolute -bottom-20 -left-14 w-56 h-56 rounded-full bg-matcha-100 opacity-70"></div>
                </div>

                <div class="relative z-10 flex flex-col h-full px-9 pt-10 pb-6">

                    {{-- Headline --}}
                    <div class="flex-shrink-0">
                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-strawberry-600">Sebut Harga</p>
                        {{-- No clamping/clipping on post text: html2canvas draws text a few px
                             lower than the browser, so clipped boxes cut it in the PNG.
                             Input maxlengths keep text short instead. --}}
                        <p class="mt-1 font-extrabold leading-tight text-matcha-900"
                           :class="headline.length > 28 ? 'text-3xl' : 'text-4xl'" x-text="headline"></p>
                        <p x-show="showPlanName && plan" class="mt-1.5 text-sm font-semibold text-matcha-600" x-text="plan?.name"></p>
                    </div>

                    {{-- Body --}}
                    <div class="flex-1 min-h-0 flex flex-col justify-center py-5">

                        {{-- Hero price --}}
                        <template x-if="layout === 'hero' && hero()">
                            <div>
                                <p class="text-xl font-bold text-matcha-800" x-text="hero().label"></p>
                                <p class="mt-2 leading-none text-strawberry-600">
                                    <span class="font-black tracking-tight" :class="money(hero().amount).length > 7 ? 'text-[64px]' : 'text-[88px]'" x-text="money(hero().amount)"></span>
                                    <span class="text-2xl font-bold text-strawberry-400">/bulan</span>
                                </p>
                            </div>
                        </template>

                        {{-- Age table --}}
                        <template x-if="layout === 'table'">
                            <div class="rounded-2xl overflow-hidden shadow-sm border border-white/70 bg-white/60">
                                <div class="grid grid-cols-2 bg-matcha-800 text-white text-xs font-bold uppercase tracking-wider">
                                    {{-- pt < pb: html2canvas draws text lower, this centres it in the PNG --}}
                                    <div class="px-5 pt-2 pb-3">Peserta</div>
                                    <div class="px-5 pt-2 pb-3 text-right">Caruman / bulan</div>
                                </div>
                                <template x-for="(row, i) in shownRows()" :key="row.id">
                                    <div class="grid grid-cols-2 items-center" :class="i % 2 ? 'bg-white/50' : 'bg-white/90'">
                                        <div class="px-5 py-3 text-lg font-semibold text-gray-700" x-text="row.label"></div>
                                        <div class="px-5 py-3 text-right text-2xl font-extrabold text-strawberry-600 tabular-nums" x-text="money(premium(row.id))"></div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        {{-- Family total --}}
                        <template x-if="layout === 'family'">
                            <div class="rounded-2xl bg-white/80 shadow-sm px-6 py-4">
                                <template x-for="row in shownRows()" :key="row.id">
                                    <div class="flex items-center gap-2 py-1.5">
                                        <span class="text-lg font-semibold text-gray-700" x-text="row.label"></span>
                                        <span class="flex-1 border-b-2 border-dotted border-gray-200"></span>
                                        <span class="text-lg font-bold text-gray-800 tabular-nums" x-text="money(premium(row.id))"></span>
                                    </div>
                                </template>
                                <div class="mt-2 pt-3 border-t-2 border-matcha-800 flex items-baseline justify-between">
                                    <span class="text-sm font-bold uppercase tracking-wider text-matcha-800">Sekeluarga</span>
                                    <span class="text-strawberry-600">
                                        <span class="text-4xl font-black tabular-nums" x-text="money(familyTotal().total)"></span>
                                        <span class="text-base font-bold">/bulan</span>
                                    </span>
                                </div>
                            </div>
                        </template>

                        {{-- Selling points --}}
                        <div x-show="shownHighlights().length" class="mt-5 space-y-2">
                            <template x-for="h in shownHighlights()" :key="h.text">
                                <div class="flex items-start gap-2.5">
                                    <span class="mt-0.5 flex-shrink-0 w-5 h-5 rounded-full bg-matcha-600 text-white flex items-center justify-center">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    </span>
                                    <span class="text-[15px] leading-snug font-medium text-gray-800" x-text="h.text"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="flex-shrink-0 space-y-2.5">
                        @if ($card['cta'])
                            <div class="bg-matcha-900 rounded-xl px-4 py-3">
                                <p class="text-white text-[13px] leading-snug font-medium">{{ $card['cta'] }}</p>
                            </div>
                        @endif
                        <div class="flex items-center justify-center gap-x-3 gap-y-1 flex-wrap text-[13px] font-semibold text-matcha-900">
                            <span>{{ $card['name'] }}</span>
                            @if ($card['phone'])<span class="text-matcha-200">•</span><span>{{ $card['phone'] }}</span>@endif
                            @if ($card['website'])<span class="text-matcha-200">•</span><span>{{ $card['website'] }}</span>@endif
                        </div>
                        @if ($card['disclaimer'])
                            <p class="text-center text-[9px] leading-tight text-gray-400">{{ $card['disclaimer'] }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

</x-app-layout>
