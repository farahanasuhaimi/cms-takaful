// Task Board. Tailwind doesn't scan this folder, so nothing here returns
// class names — it returns tone keys and the Blade view maps them to classes.

const DAY = 86400000;

function localToday() {
    const d = new Date();
    return new Date(d.getFullYear(), d.getMonth(), d.getDate());
}

// 'YYYY-MM-DD' as a local date (new Date('2026-10-08') would be UTC midnight).
function parseDate(ymd) {
    if (!ymd) return null;
    const [y, m, d] = ymd.split('-').map(Number);
    return new Date(y, m - 1, d);
}

export function kanbanBoard({ tasks, urls, csrf, archivedDone }) {
    return {
        tasks,
        archivedDone,
        tab: tasks.some(t => t.status === 'today' || t.status === 'doing') ? 'today' : 'backlog',
        filter: 'all',
        showEarlierDone: false,
        dragging: null, // { id, from }
        dropTarget: null,
        newTitle: { backlog: '', today: '', doing: '', done: '' },
        sheet: null, // editable copy of the open task
        confirmDelete: false,
        saving: false,
        error: null,

        // ---- Reading the board -------------------------------------------

        column(status) {
            const list = this.tasks.filter(t => t.status === status);

            if (status === 'backlog') {
                return list
                    .filter(t => this.matchesFilter(t))
                    .sort((a, b) => this.urgencyCompare(a, b));
            }

            if (status === 'done') {
                // Most recently finished first.
                return list.sort((a, b) => (b.status_changed_at || '').localeCompare(a.status_changed_at || ''));
            }

            return list.sort((a, b) => a.position - b.position);
        },

        count(status) {
            return this.tasks.filter(t => t.status === status).length;
        },

        doneToday() {
            return this.column('done').filter(t => this.ageDays(t) === 0);
        },

        doneEarlier() {
            return this.column('done').filter(t => this.ageDays(t) > 0);
        },

        matchesFilter(task) {
            if (this.filter === 'all') return true;
            if (this.filter === 'mine') return !task.source_type;
            return task.source_type === this.filter;
        },

        filterCount(key) {
            const backlog = this.tasks.filter(t => t.status === 'backlog');
            if (key === 'all') return backlog.length;
            if (key === 'mine') return backlog.filter(t => !t.source_type).length;
            return backlog.filter(t => t.source_type === key).length;
        },

        // Priority first, then whatever is most overdue / due soonest,
        // then cards with no date (CRM cards before your own), oldest first.
        urgencyCompare(a, b) {
            if (a.is_priority !== b.is_priority) return a.is_priority ? -1 : 1;

            const da = parseDate(a.due_date), db = parseDate(b.due_date);
            if (da && db && da - db !== 0) return da - db;
            if (da && !db) return -1;
            if (!da && db) return 1;

            if (!!a.source_type !== !!b.source_type) return a.source_type ? -1 : 1;

            return a.position - b.position;
        },

        overdueCount() {
            return this.tasks.filter(t => t.status !== 'done' && this.dueDays(t) !== null && this.dueDays(t) < 0).length;
        },

        // ---- Per-card labels -----------------------------------------------

        dueDays(task) {
            const due = parseDate(task.due_date);
            if (!due) return null;
            return Math.round((due - localToday()) / DAY);
        },

        // { tone: overdue|today|soon|later, label } or null
        due(task) {
            if (task.status === 'done') return null;
            const days = this.dueDays(task);
            if (days === null) return null;
            if (days < 0) return { tone: 'overdue', label: `Overdue ${-days}d` };
            if (days === 0) return { tone: 'today', label: 'Due today' };
            if (days === 1) return { tone: 'soon', label: 'Due tomorrow' };
            const label = parseDate(task.due_date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short' });
            return { tone: days <= 7 ? 'soon' : 'later', label: `Due ${label}` };
        },

        ageDays(task) {
            if (!task.status_changed_at) return 0;
            const changed = new Date(task.status_changed_at);
            const changedDay = new Date(changed.getFullYear(), changed.getMonth(), changed.getDate());
            return Math.max(0, Math.round((localToday() - changedDay) / DAY));
        },

        ageLabel(task) {
            const days = this.ageDays(task);
            if (task.status === 'done') return days === 0 ? 'done today' : `done ${days}d ago`;
            return days === 0 ? 'today' : `${days}d`;
        },

        // How long a card has sat in this column: fresh|aging|stale
        ageTone(task) {
            if (task.status === 'done') return 'fresh';
            const days = this.ageDays(task);
            if (days >= 14) return 'stale';
            if (days >= 7) return 'aging';
            return 'fresh';
        },

        // ---- Changing the board --------------------------------------------

        async request(method, url, body) {
            this.error = null;
            const res = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: body ? JSON.stringify(body) : undefined,
            });
            if (!res.ok) {
                this.error = res.status === 419
                    ? 'Your session expired. Reload the page and try again.'
                    : 'Could not save that change. Reload the page and try again.';
                throw new Error(`HTTP ${res.status}`);
            }
            return res.json();
        },

        replace(updated) {
            const i = this.tasks.findIndex(t => t.id === updated.id);
            if (i !== -1) this.tasks.splice(i, 1, updated);
        },

        async add(status) {
            const title = this.newTitle[status].trim();
            if (!title) return;
            this.newTitle[status] = '';
            try {
                const { task } = await this.request('POST', urls.store, { title, status });
                this.tasks.push(task);
            } catch (e) {
                this.newTitle[status] = title;
            }
        },

        async move(task, status) {
            if (task.status === status) return;
            const before = { ...task };
            // Optimistic: shows at the end of the new column straight away.
            task.status = status;
            task.status_changed_at = new Date().toISOString();
            task.position = Number.MAX_SAFE_INTEGER;
            try {
                const { task: saved } = await this.request('PUT', urls.task(task.id), { status });
                this.replace(saved);
            } catch (e) {
                this.replace(before);
            }
        },

        async togglePriority(task) {
            task.is_priority = !task.is_priority;
            try {
                await this.request('PUT', urls.task(task.id), { is_priority: task.is_priority });
            } catch (e) {
                task.is_priority = !task.is_priority;
            }
        },

        // ---- Drag and drop (desktop) ---------------------------------------

        onDragStart(task) {
            this.dragging = { id: task.id, from: task.status };
        },

        onDragEnd() {
            this.dragging = null;
            this.dropTarget = null;
        },

        // beforeId: drop above that card; null: drop at the end of the column.
        onDrop(toStatus, beforeId) {
            if (!this.dragging) return;
            const { id, from } = this.dragging;
            this.onDragEnd();

            const task = this.tasks.find(t => t.id === id);
            if (!task) return;

            // Backlog is sorted by urgency, so there's no slot to drop into.
            if (toStatus === 'backlog') {
                this.move(task, 'backlog');
                return;
            }

            const list = this.column(toStatus).filter(t => t.id !== id);
            const at = beforeId === null ? list.length : Math.max(0, list.findIndex(t => t.id === beforeId));
            list.splice(at, 0, task);

            if (from !== toStatus) {
                task.status = toStatus;
                task.status_changed_at = new Date().toISOString();
            }
            list.forEach((t, i) => { t.position = i; });

            this.request('PATCH', urls.reorder, { columns: { [toStatus]: list.map(t => t.id) } })
                .catch(() => {});
        },

        // ---- Detail sheet --------------------------------------------------

        open(task) {
            this.sheet = { ...task, notes: task.notes || '', due_date: task.due_date || '' };
            this.confirmDelete = false;
        },

        close() {
            this.sheet = null;
            this.confirmDelete = false;
        },

        async saveSheet() {
            if (!this.sheet || this.saving) return;
            const s = this.sheet;
            const body = { status: s.status, is_priority: s.is_priority, notes: s.notes || null };
            if (!s.source_type) {
                if (!s.title.trim()) return;
                body.title = s.title.trim();
                body.due_date = s.due_date || null;
            }
            this.saving = true;
            try {
                const { task } = await this.request('PUT', urls.task(s.id), body);
                this.replace(task);
                this.close();
            } catch (e) {
                // error banner is already showing
            } finally {
                this.saving = false;
            }
        },

        async deleteSheet() {
            if (!this.confirmDelete) {
                this.confirmDelete = true;
                return;
            }
            const id = this.sheet.id;
            try {
                await this.request('DELETE', urls.task(id));
                this.tasks = this.tasks.filter(t => t.id !== id);
                this.close();
            } catch (e) {
                this.confirmDelete = false;
            }
        },
    };
}
