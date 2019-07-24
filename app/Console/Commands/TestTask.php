<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;

class TestTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $task = Task::create([
            'gid'  => 'test',
            'path' => '/',
        ]);

        $task->update([
            'status' => 'completed'
        ]);
    }
}
