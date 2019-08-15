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
    public $tries = 2;

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
        info('handle-attempts():' . $this->attempts());
  
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
            info('parameters', $parameters);
            Artisan::call('od:uploads', $parameters);
            $this->task->status = 'completed';
            $this->task->save();
        }
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        if (app()->bound('sentry')) {
            app('sentry')->captureException($exception);
        }

        $this->task->update([
            'status' => 'failed'
        ]);
    }
}
