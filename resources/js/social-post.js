// Social post maker for a quotation. Tailwind doesn't scan this folder, so no
// class names live here — the Blade view owns all styling.

const MAX_ROWS = { hero: 1, table: 4, family: 5 };

function money(n) {
    if (n === null || n === undefined) return '—';
    const opts = Number.isInteger(n) ? {} : { minimumFractionDigits: 2, maximumFractionDigits: 2 };
    return 'RM' + n.toLocaleString('en-MY', opts);
}

export function socialPost({ quotationId, people, plans, selectedPlanId }) {
    const storageKey = `social-post:${quotationId}`;

    let saved = {};
    try { saved = JSON.parse(localStorage.getItem(storageKey) || '{}'); } catch (e) {}

    const planId = plans.some(p => p.id === saved.planId) ? saved.planId
        : (plans.some(p => p.id === selectedPlanId) ? selectedPlanId : plans[0]?.id);

    return {
        people,
        plans,
        planId,
        layout: saved.layout || 'table',
        headline: saved.headline ?? '',
        showPlanName: saved.showPlanName ?? true,
        // Per person: included? + label shown on the post (never the name).
        rows: people.map(p => {
            const prev = (saved.rows || []).find(r => r.id === p.id);
            return {
                id: p.id,
                name: p.name,
                age: p.age,
                on: prev ? prev.on : true,
                label: prev ? prev.label : (p.age ? `Umur ${p.age}` : 'Peserta'),
            };
        }),
        heroId: saved.heroId ?? people[0]?.id ?? null,
        highlightsByPlan: saved.highlightsByPlan || {},
        customHighlight: '',

        init() {
            if (!this.headline) this.headline = this.defaultHeadline();
            this.ensureHighlights();
            this.$watch('$data', () => this.remember());
        },

        remember() {
            try {
                localStorage.setItem(storageKey, JSON.stringify({
                    planId: this.planId,
                    layout: this.layout,
                    headline: this.headline,
                    showPlanName: this.showPlanName,
                    rows: this.rows.map(({ id, on, label }) => ({ id, on, label })),
                    heroId: this.heroId,
                    highlightsByPlan: this.highlightsByPlan,
                }));
            } catch (e) {}
        },

        get plan() {
            return this.plans.find(p => p.id === this.planId) || null;
        },

        defaultHeadline() {
            const p = this.plan;
            return p ? (p.category || p.name) : 'Sebut Harga Takaful';
        },

        premium(personId) {
            const v = this.plan?.premiums?.[personId];
            return v === undefined ? null : v;
        },

        money,

        // ---- Rows ----------------------------------------------------------

        maxRows() {
            return MAX_ROWS[this.layout];
        },

        // Included rows, capped to what the layout fits.
        shownRows() {
            if (this.layout === 'hero') {
                const r = this.rows.find(r => r.id === this.heroId) || this.rows[0];
                return r ? [r] : [];
            }
            return this.rows.filter(r => r.on).slice(0, this.maxRows());
        },

        overflowCount() {
            if (this.layout === 'hero') return 0;
            return Math.max(0, this.rows.filter(r => r.on).length - this.maxRows());
        },

        familyTotal() {
            const amounts = this.shownRows().map(r => this.premium(r.id));
            const known = amounts.filter(a => a !== null);
            return {
                total: known.reduce((a, b) => a + b, 0),
                missing: amounts.length - known.length,
            };
        },

        hero() {
            const r = this.shownRows()[0];
            return r ? { label: r.label, amount: this.premium(r.id) } : null;
        },

        // ---- Highlights ----------------------------------------------------

        // Each plan keeps its own picked/edited highlights.
        ensureHighlights() {
            const p = this.plan;
            if (p && !this.highlightsByPlan[p.id]) {
                this.highlightsByPlan[p.id] = p.highlights.map(h => ({ ...h }));
            }
        },

        highlights() {
            return (this.plan && this.highlightsByPlan[this.plan.id]) || [];
        },

        shownHighlights() {
            return this.highlights().filter(h => h.on && h.text.trim()).slice(0, 3);
        },

        addHighlight() {
            const text = this.customHighlight.trim();
            if (!text) return;
            const on = this.highlights().filter(h => h.on).length < 3;
            this.highlights().push({ text, on });
            this.customHighlight = '';
        },

        removeHighlight(i) {
            this.highlights().splice(i, 1);
        },

        selectPlan(id) {
            const wasDefault = this.headline === this.defaultHeadline();
            this.planId = Number(id);
            this.ensureHighlights();
            if (wasDefault) this.headline = this.defaultHeadline();
        },

        resetPost() {
            try { localStorage.removeItem(storageKey); } catch (e) {}
            this.layout = 'table';
            this.showPlanName = true;
            this.highlightsByPlan = {};
            this.rows.forEach(r => { r.on = true; r.label = r.age ? `Umur ${r.age}` : 'Peserta'; });
            this.heroId = this.rows[0]?.id ?? null;
            this.headline = this.defaultHeadline();
            this.ensureHighlights();
        },
    };
}
