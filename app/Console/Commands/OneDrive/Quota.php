<?php

namespace App\Console\Commands\OneDrive;

use App\Service\CoreConstants;
use App\Utils\Tool;
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
    protected $description = 'OneDriveGraph Info';

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
     * @throws \ErrorException
     */
    public function handle()
    {
        $this->call('od:refresh');
        $headers = array_keys(is_array(one_info())
            ? one_info() : []);
        if (!$headers) {
            $this->warn('Please try again later!');
            exit;
        }
        $quota[] = one_info();
        $this->info(CoreConstants::LOGO);
        $this->info('Account [' . setting('account_email') . ']');
        $this->info('App Version  [' . setting('app_version') . ']');
        $this->table($headers, $quota);
    }
}
