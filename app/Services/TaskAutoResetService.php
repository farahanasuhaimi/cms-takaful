<?php

namespace App\Services;

use App\Models\Task;

class TaskAutoResetService
{
    /**
     * Anything still sitting in Today or Doing from a previous calendar day
     * slides back to Backlog, so Today always starts the day empty.
     */
    public static function resetStaleTodayDoing(): void
    {
        $todayStart = now()->startOfDay();

        $stale = Task::whereIn('status', ['today', 'doing'])
            ->where('status_changed_at', '<', $todayStart)
            ->get();

        foreach ($stale as $task) {
            $nextPosition = Task::where('status', 'backlog')->max('position') + 1;

            $task->update([
                'status'            => 'backlog',
                'position'          => $nextPosition,
                'status_changed_at' => now(),
            ]);
        }
    }
}
