<?php

namespace App\Jobs;

use Exception;
use App\Models\Task;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

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
        Log::info('start job');
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

            Artisan::call('od:upload', $parameters);
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
        Log::info('upload error');
        if (app()->bound('sentry')) {
            app('sentry')->captureException($exception);
        }

        Log::info("attempts:{$this->attempts()}");
        Log::info("tries:{$this->tries}");
        if ($this->attempts() == $this->tries) {

            $this->task->update([
                'status' => 'failed'
            ]);
        }
    }
        
    /**
     * Determine the time at which the job should timeout.
     *
     * @return \DateTime
     */
    public function retryUntil()
    {
        return now()->addSeconds(3);
    }
}
