<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Services\TaskAutoBacklogService;
use App\Services\TaskAutoResetService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // Done cards older than this stay in the DB but drop off the board.
    private const DONE_VISIBLE_DAYS = 7;

    public function index()
    {
        TaskAutoResetService::resetStaleTodayDoing();
        TaskAutoBacklogService::sync(auth()->id());

        $doneSince = now()->subDays(self::DONE_VISIBLE_DAYS)->startOfDay();

        $tasks = Task::where(fn ($q) => $q
                ->where('status', '!=', 'done')
                ->orWhere('status_changed_at', '>=', $doneSince))
            ->orderBy('position')
            ->get()
            ->map->toBoard()
            ->values();

        $archivedDone = Task::where('status', 'done')
            ->where('status_changed_at', '<', $doneSince)
            ->count();

        return view('tasks.index', [
            'tasks'           => $tasks,
            'archivedDone'    => $archivedDone,
            'doneVisibleDays' => self::DONE_VISIBLE_DAYS,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'    => ['required', 'string', 'max:255'],
            'status'   => ['nullable', 'in:' . implode(',', Task::STATUSES)],
            'due_date' => ['nullable', 'date'],
        ]);

        $status = $validated['status'] ?? 'backlog';

        $task = Task::create([
            'user_id'           => auth()->id(),
            'title'             => $validated['title'],
            'status'            => $status,
            'position'          => $this->nextPosition($status),
            'status_changed_at' => now(),
            'due_date'          => $validated['due_date'] ?? null,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['task' => $task->fresh()->toBoard()], 201);
        }

        return back()->with('success', 'Task added.');
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title'       => ['sometimes', 'required', 'string', 'max:255'],
            'notes'       => ['sometimes', 'nullable', 'string', 'max:5000'],
            'due_date'    => ['sometimes', 'nullable', 'date'],
            'is_priority' => ['sometimes', 'boolean'],
            'status'      => ['sometimes', 'in:' . implode(',', Task::STATUSES)],
        ]);

        // Auto-card title and due date are owned by their CRM signal and
        // refreshed on every sync, so an edit here would just be overwritten.
        if ($task->source_type) {
            unset($validated['title'], $validated['due_date']);
        }

        if (isset($validated['status']) && $validated['status'] !== $task->status) {
            $validated['position']          = $this->nextPosition($validated['status']);
            $validated['status_changed_at'] = now();
        }

        $task->update($validated);

        if ($request->expectsJson()) {
            return response()->json(['task' => $task->fresh()->toBoard()]);
        }

        return back()->with('success', 'Task updated.');
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'columns'     => ['required', 'array'],
            'columns.*'   => ['array'],
            'columns.*.*' => ['integer'],
        ]);

        $currentStatuses = Task::pluck('status', 'id');

        foreach ($validated['columns'] as $status => $ids) {
            if (! in_array($status, Task::STATUSES, true)) {
                continue;
            }

            foreach (array_values($ids) as $position => $id) {
                $update = ['status' => $status, 'position' => $position];

                if (($currentStatuses[$id] ?? null) !== $status) {
                    $update['status_changed_at'] = now();
                }

                Task::where('id', $id)->update($update);
            }
        }

        return response()->json(['ok' => true]);
    }

    public function destroy(Request $request, Task $task)
    {
        $task->delete();

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Task removed.');
    }

    private function nextPosition(string $status): int
    {
        return (int) Task::where('status', $status)->max('position') + 1;
    }
}
