{{-- Shared by create.blade.php and edit.blade.php — relies on the surrounding
     x-data="quotationBuilder(...)" scope and the $leads/$clients passed to both views. --}}

{{-- Title --}}
<div class="bg-white rounded-xl border border-gray-200 p-5">
    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-2">Quotation Title</label>
    <input x-model="title" type="text" placeholder="e.g. Quotation for Amir & Benny"
           class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
</div>

{{-- Prospect --}}
<div class="bg-white rounded-xl border border-gray-200 p-5">
    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Prospect</p>
    <div class="grid grid-cols-2 gap-3 mb-3">
        <div>
            <label class="text-xs text-gray-400 mb-1 block">Name</label>
            <input x-model="prospect_name" type="text" placeholder="e.g. Ahmad bin Ali"
                   class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
        </div>
        <div>
            <label class="text-xs text-gray-400 mb-1 block">Phone Number</label>
            <input x-model="prospect_phone" type="text" placeholder="e.g. 012-3456789"
                   class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
        </div>
    </div>
    <div>
        <label class="text-xs text-gray-400 mb-1 block">Notes for Prospect <span class="text-gray-300">(optional — shown on printout)</span></label>
        <textarea x-model="prospect_notes" rows="2" placeholder="e.g. Perlindungan ini sesuai untuk anda sebagai penyara keluarga..."
                  class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400 resize-none"></textarea>
    </div>
</div>

{{-- Link to Lead / Client --}}
<div class="bg-white rounded-xl border border-gray-200 p-5">
    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
        Link to Lead or Client <span class="text-gray-300 font-normal normal-case">(optional)</span>
    </p>
    <select x-model="linked_person"
            class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
        <option value="">— No link —</option>
        @if ($leads->count())
            <optgroup label="Leads">
                @foreach ($leads as $l)
                    <option value="lead_{{ $l->id }}">{{ $l->name }}</option>
                @endforeach
            </optgroup>
        @endif
        @if ($clients->count())
            <optgroup label="Clients">
                @foreach ($clients as $c)
                    <option value="client_{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </optgroup>
        @endif
    </select>
</div>

{{-- People --}}
<div class="bg-white rounded-xl border border-gray-200 p-5">
    <div class="flex items-center justify-between mb-3">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">People</p>
        <button type="button" @click="addPerson()"
                class="text-xs text-matcha-600 hover:text-matcha-800 font-medium transition">+ Add Person</button>
    </div>
    <div class="space-y-2">
        <template x-for="(person, i) in people" :key="i">
            <div class="flex items-center gap-2">
                <input x-model="person.name" type="text" placeholder="Name"
                       class="flex-1 text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                <input x-model="person.age" type="number" placeholder="Age" min="1" max="99"
                       class="w-20 text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                <button type="button" @click="removePerson(i)"
                        class="text-gray-300 hover:text-strawberry-400 transition text-lg leading-none px-1"
                        x-show="people.length > 1">×</button>
            </div>
        </template>
    </div>
</div>

{{-- Plans --}}
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-1">Plans to Compare</p>
        <button type="button" @click="addPlan()"
                class="text-xs text-matcha-600 hover:text-matcha-800 font-medium transition">+ Add Plan</button>
    </div>

    <template x-for="(plan, j) in plans" :key="j">
        <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">

            {{-- Plan header --}}
            {{-- Catalog picker --}}
            <template x-if="planCatalog.length > 0">
                <div>
                    <label class="text-xs text-gray-400 mb-1 block">Load from Plan Catalog</label>
                    <select @change="loadFromCatalog(j, $event.target.value); $event.target.value = ''"
                            class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400 bg-matcha-50">
                        <option value="">— pick a plan to auto-fill fields —</option>
                        <template x-for="p in planCatalog" :key="p.id">
                            <option :value="p.id" x-text="p.name + ' (' + p.category + ')'"></option>
                        </template>
                    </select>
                </div>
            </template>

            <div class="flex items-start justify-between gap-3">
                <div class="flex-1 grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-400 mb-1 block">Category</label>
                        <input x-model="plan.category" type="text" placeholder="e.g. Hibah, PA, Medical"
                               class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                    </div>
                    <div>
                        <label class="text-xs text-gray-400 mb-1 block">Plan Name <span class="text-strawberry-400">*</span></label>
                        <input x-model="plan.plan_name" type="text" placeholder="e.g. Sejuta Makna"
                               class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                    </div>
                </div>
                <button type="button" @click="removePlan(j)"
                        x-show="plans.length > 1"
                        class="text-gray-300 hover:text-strawberry-400 transition text-lg leading-none mt-5 flex-shrink-0">×</button>
            </div>

            {{-- Premiums per person --}}
            <div>
                <label class="text-xs text-gray-400 mb-2 block">Monthly Premium (RM)</label>
                <div class="space-y-2">
                    <template x-for="(person, i) in people" :key="i">
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-gray-600 w-32 truncate" x-text="person.name || ('Person ' + (i+1))"></span>
                            <input type="number" x-model="plan.premiums[i]" step="0.01" min="0" placeholder="0.00"
                                   class="w-32 text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                        </div>
                    </template>
                </div>
            </div>

            {{-- Attributes --}}
            <div class="border-t border-gray-100 pt-4 grid grid-cols-2 sm:grid-cols-3 gap-3">
                <div>
                    <label class="text-xs text-gray-400 mb-1 block">Type</label>
                    <input x-model="plan.type" type="text" :list="'dl-'+j+'-type'" placeholder="e.g. Stand-alone, ILP Hibah"
                           class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                    <datalist :id="'dl-'+j+'-type'">
                        <template x-for="opt in (plan.opts?.type || [])" :key="opt"><option :value="opt"></option></template>
                    </datalist>
                </div>
                <div>
                    <label class="text-xs text-gray-400 mb-1 block">Room &amp; Board</label>
                    <input x-model="plan.room_board" type="text" :list="'dl-'+j+'-rb'" placeholder="e.g. RM180/malam"
                           class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                    <datalist :id="'dl-'+j+'-rb'">
                        <template x-for="opt in (plan.opts?.room_board || [])" :key="opt"><option :value="opt"></option></template>
                    </datalist>
                </div>
                <div>
                    <label class="text-xs text-gray-400 mb-1 block">Coverage</label>
                    <input x-model="plan.coverage" type="text" :list="'dl-'+j+'-cov'" placeholder="e.g. RM350k"
                           class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                    <datalist :id="'dl-'+j+'-cov'">
                        <template x-for="opt in (plan.opts?.coverage || [])" :key="opt"><option :value="opt"></option></template>
                    </datalist>
                </div>
                <div>
                    <label class="text-xs text-gray-400 mb-1 block">Kenaikan</label>
                    <input x-model="plan.kenaikan" type="text" :list="'dl-'+j+'-ken'" placeholder="e.g. Setiap tahun, Tiada"
                           class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                    <datalist :id="'dl-'+j+'-ken'">
                        <template x-for="opt in (plan.opts?.kenaikan || [])" :key="opt"><option :value="opt"></option></template>
                    </datalist>
                </div>
                <div>
                    <label class="text-xs text-gray-400 mb-1 block">Plan</label>
                    <input x-model="plan.plan_type" type="text" :list="'dl-'+j+'-pt'" placeholder="e.g. 10 tahun / 20 tahun"
                           class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                    <datalist :id="'dl-'+j+'-pt'">
                        <template x-for="opt in (plan.opts?.plan_type || [])" :key="opt"><option :value="opt"></option></template>
                    </datalist>
                </div>
                <div>
                    <label class="text-xs text-gray-400 mb-1 block">Waiver</label>
                    <select x-model="plan.waiver"
                            class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                        <option value="yes">✓ Yes</option>
                        <option value="no">✗ No</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-400 mb-1 block">Umur Matang</label>
                    <input x-model="plan.umur_matang" type="text" :list="'dl-'+j+'-um'" placeholder="e.g. 60, 70, 85"
                           class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                    <datalist :id="'dl-'+j+'-um'">
                        <template x-for="opt in (plan.opts?.umur_matang || [])" :key="opt"><option :value="opt"></option></template>
                    </datalist>
                </div>
                <div>
                    <label class="text-xs text-gray-400 mb-1 block">Pampasan Matang</label>
                    <input x-model="plan.pampasan_matang" type="text" :list="'dl-'+j+'-pm'" placeholder="e.g. RM30k, nilai akaun"
                           class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                    <datalist :id="'dl-'+j+'-pm'">
                        <template x-for="opt in (plan.opts?.pampasan_matang || [])" :key="opt"><option :value="opt"></option></template>
                    </datalist>
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <label class="text-xs text-gray-400 mb-1 block">Privilege</label>
                    <input x-model="plan.privilege" type="text" :list="'dl-'+j+'-priv'" placeholder="e.g. ICU RM500/hari, GIO"
                           class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                    <datalist :id="'dl-'+j+'-priv'">
                        <template x-for="opt in (plan.opts?.privilege || [])" :key="opt"><option :value="opt"></option></template>
                    </datalist>
                </div>
            </div>

            {{-- Additional Details — dynamic, auto-seeded from any Plan Catalog attribute not covered above (e.g. Deduktibel, Health Wallet, Hibah) --}}
            <div class="border-t border-gray-100 pt-4">
                <div class="flex items-center justify-between mb-2">
                    <label class="text-xs text-gray-400">Additional Details <span class="text-gray-300">(e.g. Deduktibel, Health Wallet, Hibah)</span></label>
                    <button type="button" @click="plan.attributes.push({ key: '', value: '' })"
                            class="text-xs text-matcha-600 hover:text-matcha-800 font-medium transition">+ Add detail</button>
                </div>
                <div class="space-y-2">
                    <template x-for="(row, k) in plan.attributes" :key="k">
                        <div class="flex items-center gap-2">
                            <input x-model="row.key" type="text" placeholder="Label (e.g. Deduktibel)"
                                   class="w-40 flex-shrink-0 text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                            <input x-model="row.value" type="text" placeholder="Value"
                                   class="flex-1 text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                            <button type="button" @click="plan.attributes.splice(k, 1)"
                                    class="text-gray-300 hover:text-strawberry-400 transition text-lg leading-none px-1 flex-shrink-0">×</button>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Notes --}}
            <div>
                <label class="text-xs text-gray-400 mb-1 block">Notes <span class="text-gray-300">(optional)</span></label>
                <input x-model="plan.notes" type="text" placeholder="Any extra info for this plan"
                       class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
            </div>

        </div>
    </template>
</div>
