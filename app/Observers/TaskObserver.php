<?php

namespace App\Observers;

use App\Models\Task;
use Illuminate\Support\Arr;

class TaskObserver
{
    /**
     * Handle the task "saving" event.
     *
     * @param  \App\Models\Task  $task
     * @return void
     */
    public function saving(Task $task)
    {
        $newData = $task->getDirty();

        if (!empty($status = Arr::get($newData, 'status')) && in_array($status . '_at', $task->getColumns())) {
            $task->setAttribute($status . '_at', now());
        }
    }
}
