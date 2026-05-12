<x-app-layout>
    <x-slot name="title">Edit Quotation · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">Edit Quotation</x-slot>
    <x-slot name="actions">
        <a href="{{ route('quotations.show', $quotation) }}"
           class="text-xs bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 px-3 py-1.5 rounded-lg transition">
            ← Back
        </a>
    </x-slot>

    <form method="POST" action="{{ route('quotations.update', $quotation) }}" id="qform">
        @csrf
        @method('PUT')
        <input type="hidden" name="data" id="q-data">
    </form>

    <div x-data="quotationBuilder({{ json_encode($initial) }}, {{ json_encode($planCatalog) }})" class="space-y-6">

        {{-- Title --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-2">Quotation Title</label>
            <input x-model="title" type="text" placeholder="e.g. Quotation for Amir & Benny"
                   class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
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

                    <div class="border-t border-gray-100 pt-4 grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="text-xs text-gray-400 mb-1 block">Type</label>
                            <input x-model="plan.type" type="text" placeholder="e.g. Hibah, Personal Accident"
                                   class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                        </div>
                        <div>
                            <label class="text-xs text-gray-400 mb-1 block">Coverage</label>
                            <input x-model="plan.coverage" type="text" placeholder="e.g. RM350k"
                                   class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                        </div>
                        <div>
                            <label class="text-xs text-gray-400 mb-1 block">Umur Matang</label>
                            <input x-model="plan.umur_matang" type="text" placeholder="e.g. 60, 70, 85"
                                   class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                        </div>
                        <div>
                            <label class="text-xs text-gray-400 mb-1 block">Pampasan Matang</label>
                            <input x-model="plan.pampasan_matang" type="text" placeholder="e.g. RM30k, &lt;RM1000"
                                   class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                        </div>
                        <div>
                            <label class="text-xs text-gray-400 mb-1 block">Kenaikan</label>
                            <input x-model="plan.kenaikan" type="text" placeholder="e.g. Max: RM45/bln, or leave empty"
                                   class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                        </div>
                        <div>
                            <label class="text-xs text-gray-400 mb-1 block">Plan Type</label>
                            <select x-model="plan.plan_type"
                                    class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                                <option value="no_investment">No Investment</option>
                                <option value="investment">Investment</option>
                            </select>
                        </div>
                        <div class="col-span-2 sm:col-span-2">
                            <label class="text-xs text-gray-400 mb-1 block">Privilege</label>
                            <input x-model="plan.privilege" type="text" placeholder="e.g. Conversion, ICU RM100/hari"
                                   class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                        </div>
                        <div>
                            <label class="text-xs text-gray-400 mb-1 block">Waiver</label>
                            <select x-model="plan.waiver"
                                    class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                                <option value="yes">✓ Yes</option>
                                <option value="no">✗ No</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs text-gray-400 mb-1 block">Notes <span class="text-gray-300">(optional)</span></label>
                        <input x-model="plan.notes" type="text" placeholder="Any extra info for this plan"
                               class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                    </div>

                </div>
            </template>
        </div>

        {{-- Submit --}}
        <div class="flex items-center gap-3">
            <button type="button" @click="submit()"
                    class="bg-matcha-600 hover:bg-matcha-800 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition">
                Save Changes
            </button>
            <a href="{{ route('quotations.show', $quotation) }}" class="text-sm text-gray-400 hover:text-gray-600 transition">Cancel</a>
        </div>

    </div>

    <script>
    function quotationBuilder(initial, planCatalog) {
        const defaults = initial || {
            title: '',
            people: [{ name: '', age: '' }, { name: '', age: '' }],
            plans: [{
                category: '', plan_name: '', type: '', coverage: '',
                umur_matang: '', pampasan_matang: '', kenaikan: '',
                plan_type: 'no_investment', privilege: '', waiver: 'yes',
                notes: '', premiums: ['', '']
            }]
        };

        return {
            ...defaults,
            planCatalog: planCatalog || [],

            loadFromCatalog(j, id) {
                if (!id) return;
                const c = this.planCatalog.find(p => p.id == id);
                if (!c) return;
                const plan = this.plans[j];
                const a = c.attributes || {};
                plan.plan_name       = c.name;
                plan.category        = c.category;
                plan.type            = a['Type'] || a['type'] || '';
                plan.coverage        = a['Coverage'] || a['coverage'] || '';
                plan.umur_matang     = a['Umur Matang'] || a['umur_matang'] || '';
                plan.pampasan_matang = a['Pampasan Matang'] || a['pampasan_matang'] || '';
                plan.kenaikan        = a['Kenaikan'] || a['kenaikan'] || '';
                plan.privilege       = a['Privilege'] || a['privilege'] || '';
                const w = (a['Waiver'] || a['waiver'] || '').toLowerCase();
                plan.waiver = (w === 'yes' || w === 'true') ? 'yes' : 'no';
                const pt = (a['Plan'] || a['plan_type'] || '').toLowerCase();
                plan.plan_type = pt.includes('invest') && !pt.includes('no') ? 'investment' : 'no_investment';
            },

            addPerson() {
                this.people.push({ name: '', age: '' });
                this.plans.forEach(p => p.premiums.push(''));
            },

            removePerson(i) {
                if (this.people.length <= 1) return;
                this.people.splice(i, 1);
                this.plans.forEach(p => p.premiums.splice(i, 1));
            },

            addPlan() {
                this.plans.push({
                    category: '', plan_name: '', type: '', coverage: '',
                    umur_matang: '', pampasan_matang: '', kenaikan: '',
                    plan_type: 'no_investment', privilege: '', waiver: 'yes',
                    notes: '', premiums: this.people.map(() => '')
                });
            },

            removePlan(i) {
                if (this.plans.length <= 1) return;
                this.plans.splice(i, 1);
            },

            submit() {
                if (!this.title.trim()) { alert('Please enter a quotation title.'); return; }
                document.getElementById('q-data').value = JSON.stringify({
                    title: this.title,
                    people: this.people,
                    plans: this.plans,
                });
                document.getElementById('qform').submit();
            }
        };
    }
    </script>

</x-app-layout>
