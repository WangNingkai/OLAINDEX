<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\Constants;
use App\Helpers\Tool;
use Illuminate\Console\Command;

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
            if ($version == Constants::LATEST_VERSION) {
                $this->info('已是最新版本, 无需更新');

                return;
            }
            switch ($version) {
                case 'v3.2':
                    $result = $this->upTo321();
                    break;
                default:
                    return;
            }
            $this->info($result['status'] . ':' . $result['msg']);
        }
        $this->call('cache:clear');
        $this->call('config:cache');
        exit;
    }

    public function upTo321()
    {
        if (!file_exists(base_path('.env'))) {
            $this->warn('出错了未检测到 .env 文件');
            exit();
        } else {
            $env_origin = file_get_contents(base_path('.env'));
            $search_db = [
                'LOG_CHANNEL=stack',
            ];
            $replace_db = [
                'LOG_CHANNEL=daily',
            ];
            $env = str_replace($search_db, $replace_db, $env_origin);
            file_put_contents(base_path('.env'), $env);
            $config = Tool::config();
            $config = array_merge($config, ['app_version' => 'v3.2.1']);
        }
        $saved = Tool::saveConfig($config);

        return $saved ? $this->returnStatus('更新成功，version=v3.2.1')
            : $this->returnStatus('更新失败，数据迁移失败，请手动迁移', false);
    }

    /**
     * 返回状态
     *
     * @param      $msg
     * @param bool $status
     *
     * @return array
     */
    public function returnStatus($msg, $status = true)
    {
        return [
            'status' => $status,
            'msg'    => $msg,
        ];
    }
}
