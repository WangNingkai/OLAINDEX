<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\Tool;
use Illuminate\Console\Command;

class SwitchType extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:switch {--type= : Switch Type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Switch Type';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        if ($this->option('type')) {
            $this->call('od:reset', ['--force' => true]);
            $type = $this->option('type');
        } else {
            if ($this->confirm('Switch type will erase all data,continue?')) {
                $this->call('od:reset', ['--yes' => true]);
                $type = $this->choice('Please choose a version (com:World cn:21Vianet)', ['com', 'cn'], 'com');
            } else exit;
        }
        $data = ['account_type' => $type];
        $saved = Tool::updateConfig($data);
        $saved ? $this->info('Success!') : $this->warn('Failed!');

    }
}
