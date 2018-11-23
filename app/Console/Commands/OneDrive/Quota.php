<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\Constants;
use App\Helpers\Tool;
use Illuminate\Console\Command;

class Quota extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'OneDrive Info';

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
        $this->call('od:refresh');
        $headers = array_keys(is_array(quota()) ? quota() : []);
        if (!$headers) {
            $this->warn('请稍后重试...');
            exit;
        }
        $quota[] = quota();
        $this->info(Constants::LOGO);
        $this->info('Account [' . bind_account() . ']');
        $this->info('App Version  [' . Tool::config('app_version') . ']');
        $this->table($headers, $quota, 'default');
    }
}
