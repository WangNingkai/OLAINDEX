<?php

namespace App\Console\Commands;

use App\Helpers\Constants;
use App\Helpers\Tool;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class UpdateInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Install';

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
        // 获取当前版本,默认开发版
        $version = Tool::config('app_version','dev');
        if ($version == Constants::LATEST_VERSION) {
            $this->info('已是最新版本，无需更新');
            return;
        }
        switch ($version)
        {
            case 'dev':
                $result = $this->v_1_0();
            break;
            default:
                $result = $this->v_1_0();
        }
        Artisan::call('cache:clear');
        $this->info($result['status'] . ':' . $result['msg']);
        return;
    }

    /**
     * v1.0迭代
     * @return array
     */
    public function v_1_0()
    {
        $client_id = config('graph.clientId');
        $client_secret = config('graph.clientSecret');
        $redirect_uri = config('graph.redirectUri');
        $result = \Illuminate\Support\Facades\DB::table('parameters')->insert([
            [
                'name' => 'app_version',
                'value' => 'v1.0',
            ],
            [
                'name' => 'client_id',
                'value' => $client_id,
            ],
            [
                'name' => 'client_secret',
                'value' => $client_secret,
            ],
            [
                'name' => 'redirect_uri',
                'value' => $redirect_uri,
            ]
        ]);
        return $result ? $this->returnStatus('更新成功') : $this->returnStatus('更新失败，数据迁移失败，请手动迁移',false);
    }

    /**
     * 返回状态
     * @param $msg
     * @param bool $status
     * @return array
     */
    public function returnStatus($msg,$status = true)
    {
        return [
            'status' => $status,
            'msg' => $msg
        ];
    }

}
