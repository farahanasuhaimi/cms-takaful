<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Services\TaskAutoBacklogService;
use App\Services\TaskAutoResetService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        TaskAutoResetService::resetStaleTodayDoing();
        TaskAutoBacklogService::sync(auth()->id());

        $tasks = Task::orderBy('position')->get()->groupBy('status');

        $columns = collect(Task::STATUSES)->mapWithKeys(fn ($status) => [
            $status => $tasks->get($status, collect()),
        ]);

        return view('tasks.index', compact('columns'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'  => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'in:' . implode(',', Task::STATUSES)],
        ]);

        $status = $validated['status'] ?? 'backlog';

        $nextPosition = Task::where('status', $status)->max('position') + 1;

        Task::create([
            'user_id'           => auth()->id(),
            'title'             => $validated['title'],
            'status'            => $status,
            'position'          => $nextPosition,
            'status_changed_at' => now(),
        ]);

        return back()->with('success', 'Task added.');
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $task->update($validated);

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

    public function destroy(Task $task)
    {
        $task->delete();

        return back()->with('success', 'Task removed.');
    }
}
