<?php

namespace App\Console\Commands\OneDrive;

use Illuminate\Console\Command;

class Reset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:reset {--f|force  : Force Reset}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset App';

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
        if ($this->option('force')) {
            return $this->reset();
        } else {
            if ($this->confirm('Reset will erase all data, continue?')) {
                return $this->reset();
            }
        }
    }

    /**
     * Execute the console command.
     */
    public function reset()
    {
        $this->call('cache:clear');
        copy(
            storage_path('app/example.config.json'),
            storage_path('app/config.json')
        );
        $this->info('Reset Completed！');
    }
}
