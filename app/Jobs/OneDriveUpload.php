<?php

namespace App\Jobs;

use Exception;
use App\Models\Task;
use Illuminate\Support\Facades\Artisan;

class OneDriveUpload extends Job
{
    protected $task = null;
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 3600;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->task->status == 'pending') {
            $parameters = [
                '--one_drive_id' => $this->task->onedrive_id,
                'local'          => $this->task->source,
                'remote'         => $this->task->target
            ];

            if ($this->task->type == 'folder') {
                $parameters = array_merge($parameters, [
                    '--folder'
                ]);
            }

            try {
                Artisan::call('od:upload', $parameters);
                $this->task->status = 'completed';
            } catch (Exception $e) {
                $this->task->status = 'failed';
                if (app()->bound('sentry')) {
                    app('sentry')->captureException($e);
                }
            }

            $this->task->save();
        }
    }
}
