<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\Constants;
use App\Helpers\Tool;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class Update extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update App';

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
        $this->info(Constants::LOGO);
        $this->info('Current Version  [' . Tool::config('app_version') . ']');
        // 获取当前版本,默认开发版
        $this->warn('Start updating...');
        $version = Tool::config('app_version', 'v3.2');
        if (version_compare($version, 'v3.2') < 0) {
            $this->warn('Version less [v3.2] ,Failed！Please delete the config.json and try again later');
        } else {
            $this->warn('No Updates');
        }
        $this->call('cache:clear');
        exit;
    }


    /**
     * 返回状态
     * @param $msg
     * @param bool $status
     * @return array
     */
    public function returnStatus($msg, $status = true)
    {
        return [
            'status' => $status,
            'msg' => $msg
        ];
    }
}
