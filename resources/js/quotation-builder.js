const KNOWN_ATTRIBUTE_KEYS = new Set([
    'type', 'room & board', 'room_board', 'coverage',
    'umur matang', 'umur_matang', 'pampasan matang', 'pampasan_matang',
    'kenaikan', 'privilege', 'waiver', 'plan', 'plan_type',
]);

const emptyPlan = () => ({
    category: '', plan_name: '', type: '', coverage: '', room_board: '',
    umur_matang: '', pampasan_matang: '', kenaikan: '',
    plan_type: '', privilege: '', waiver: 'yes',
    notes: '', premiums: ['', ''], opts: {}, attributes: [],
});

export function quotationBuilder(initial, planCatalog) {
    const defaults = initial || {
        title: '',
        linked_person: '',
        prospect_name: '', prospect_phone: '', prospect_notes: '',
        people: [{ name: '', age: '' }, { name: '', age: '' }],
        plans: [emptyPlan()],
    };

    // Existing plans loaded from the DB (edit form) may predate the
    // opts/attributes fields — make sure they're always present.
    if (defaults.plans) {
        defaults.plans.forEach(p => {
            if (!p.opts) p.opts = {};
            if (!p.attributes) p.attributes = [];
        });
    }

    return {
        ...defaults,
        planCatalog: planCatalog || [],

        loadFromCatalog(j, id) {
            if (!id) return;
            const c = this.planCatalog.find(p => p.id == id);
            if (!c) return;
            const plan = this.plans[j];
            const a = c.attributes || {};
            const o = c.attribute_options || {};
            const t1 = val => val ? val.split('|')[0].trim() : '';
            plan.plan_name       = c.name;
            plan.category        = c.category;
            plan.type            = a['Type'] || a['type'] || '';
            plan.room_board      = t1(a['Room & Board'] || a['room_board'] || '');
            plan.coverage        = t1(a['Coverage'] || a['coverage'] || '');
            plan.umur_matang     = t1(a['Umur Matang'] || a['umur_matang'] || '');
            plan.pampasan_matang = t1(a['Pampasan Matang'] || a['pampasan_matang'] || '');
            plan.kenaikan        = t1(a['Kenaikan'] || a['kenaikan'] || '');
            plan.privilege       = t1(a['Privilege'] || a['privilege'] || '');
            const w = (a['Waiver'] || a['waiver'] || '').toLowerCase();
            plan.waiver    = (w === 'yes' || w === 'true') ? 'yes' : 'no';
            plan.plan_type = a['Plan'] || a['plan_type'] || '';
            plan.opts = {
                type:            o['Type'] || [],
                room_board:      o['Room & Board'] || [],
                coverage:        o['Coverage'] || [],
                kenaikan:        o['Kenaikan'] || [],
                plan_type:       o['Plan'] || [],
                umur_matang:     o['Umur Matang'] || [],
                pampasan_matang: o['Pampasan Matang'] || [],
                privilege:       o['Privilege'] || [],
            };

            // Anything in the catalog's attributes not mapped to a field above
            // (e.g. Deduktibel, Health Wallet, Hibah) becomes an editable extra row.
            plan.attributes = Object.entries(a)
                .filter(([key]) => !KNOWN_ATTRIBUTE_KEYS.has(key.toLowerCase()))
                .map(([key, value]) => ({ key, value: t1(value) }));
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
            this.plans.push({ ...emptyPlan(), premiums: this.people.map(() => '') });
        },

        removePlan(i) {
            if (this.plans.length <= 1) return;
            this.plans.splice(i, 1);
        },

        submit() {
            if (!this.title.trim()) { alert('Please enter a quotation title.'); return; }
            const hasName = this.people.some(p => p.name.trim());
            if (!hasName) { alert('Please add at least one person.'); return; }
            const hasPlan = this.plans.some(p => p.plan_name.trim());
            if (!hasPlan) { alert('Please add at least one plan.'); return; }

            document.getElementById('q-data').value = JSON.stringify({
                title: this.title,
                linked_person: this.linked_person || '',
                prospect_name: this.prospect_name,
                prospect_phone: this.prospect_phone,
                prospect_notes: this.prospect_notes,
                people: this.people,
                plans: this.plans,
            });
            document.getElementById('qform').submit();
        },
    };
}
