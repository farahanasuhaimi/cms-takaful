export function kanbanBoard(initialColumns, reorderUrl, csrfToken) {
    return {
        columns: initialColumns,
        dragging: null, // { id, from }

        onDragStart(task, status) {
            this.dragging = { id: task.id, from: status };
        },

        onDrop(toStatus, toIndex) {
            if (!this.dragging) return;

            const { id, from } = this.dragging;
            this.dragging = null;

            const fromList = this.columns[from];
            const idx = fromList.findIndex(t => t.id === id);
            if (idx === -1) return;

            const [task] = fromList.splice(idx, 1);
            if (from !== toStatus) {
                task.status_changed_at = new Date().toISOString();
            }
            const toList = this.columns[toStatus];
            const insertAt = toIndex === null ? toList.length : toIndex;
            toList.splice(insertAt, 0, task);

            this.persist();
        },

        persist() {
            const payload = {};
            for (const status in this.columns) {
                payload[status] = this.columns[status].map(t => t.id);
            }

            fetch(reorderUrl, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ columns: payload }),
            });
        },

        ageDays(task) {
            if (!task.status_changed_at) return 0;
            const changed = new Date(task.status_changed_at);
            return Math.max(0, Math.floor((Date.now() - changed.getTime()) / 86400000));
        },

        ageLabel(task) {
            const days = this.ageDays(task);
            return days === 0 ? 'today' : `${days}d`;
        },

        ageClass(task) {
            const days = this.ageDays(task);
            if (days >= 14) return 'text-strawberry-500';
            if (days >= 7) return 'text-amber-500';
            return 'text-gray-300';
        },
    };
}
