<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\Constants;
use App\Helpers\Tool;

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
        $this->call(
            !empty($one_drive_id  = $this->option('one_drive_id')) 
                ? 'od:refresh --one_drive_id=' . $one_drive_id
                : 'od:refresh'
        );
        $headers = array_keys(is_array(Tool::getOneDriveInfo())
            ? Tool::getOneDriveInfo() : []);
        if (!$headers) {
            $this->warn('Please try again later!');
            exit;
        }
        $quota[] = Tool::getOneDriveInfo();
        $this->info(Constants::LOGO);
        $this->info('Account ['.Tool::getBindAccount().']');
        $this->info('App Version  ['.Tool::config('app_version').']');
        $this->table($headers, $quota, 'default');
    }
}
