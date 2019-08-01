<?php

namespace App\Observers;

use App\Models\Task;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TaskObserver
{
    /**
     * Handle the one drive "creating" event.
     *
     * @param  \App\Models\Task  $task
     * @return void
     */
    public function creating(Task $task)
    {
    }

    /**
     * Handle the task "saving" event.
     *
     * @param  \App\Models\Task  $task
     * @return void
     */
    public function saving(Task $task)
    {
        $newData = $task->getDirty();

        if (!empty($status = Arr::get($newData, 'status'))) {
            if (in_array($status . '_at', $task->getColumns())) {
                $task->setAttribute($status . '_at', now());
            }

            if ($status == 'completed') {
                clearOnedriveCache($task->onedrive_id);
            }
        }
    }
}
