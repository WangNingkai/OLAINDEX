<?php

namespace App\Jobs;

class OneDriveUpload extends Job
{
    public $task = null;
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
    public $timeout = 7200;

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
        if ($this->task->type == 'padding') {
            if ($this->task->type == 'file') {
            }
        }
    }
}
