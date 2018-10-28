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
    protected $description = '更新安装';

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
        $this->warn('========== 开始更新 ==========');
        $version = Tool::config('app_version', 'dev');
        if ($version == Constants::LATEST_VERSION) {
            $this->info('已是最新版本，无需更新');
            return;
        }
        switch ($version) {
            case 'dev':
                $this->v_1_0();
                $this->v_1_1();
                $this->v_1_2();
                $this->v_2_0();
                $result = $this->v_3_0();
                break;
            case 'v1.0':
                $this->v_1_1();
                $this->v_1_2();
                $this->v_2_0();
                $result = $this->v_3_0();
                break;
            case 'v1.1':
                $this->v_1_2();
                $this->v_2_0();
                $result = $this->v_3_0();
                break;
            case 'v1.2':
                $this->v_2_0();
                $result = $this->v_3_0();
                break;
            case 'v2.0':
                $result = $this->v_3_0();
                break;
            default:
                $this->v_1_0();
                $this->v_1_1();
                $this->v_1_2();
                $this->v_2_0();
                $result = $this->v_3_0();
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
        return $result ? $this->returnStatus('更新成功，version=v1.0') : $this->returnStatus('更新失败，数据迁移失败，请手动迁移', false);
    }

    /**
     * @return array
     */
    public function v_1_1()
    {
        $insert = \Illuminate\Support\Facades\DB::table('parameters')->insert([
            [
                'name' => 'hotlink_protection',
                'value' => '',
            ]
        ]);
        $update = false;
        if ($insert) {
            $update = \Illuminate\Support\Facades\DB::table('parameters')
                ->where('name', 'app_version')
                ->update(['value' => 'v1.1']);
        }
        return $update ? $this->returnStatus('更新成功，version=v1.1') : $this->returnStatus('更新失败，数据迁移失败，请手动迁移', false);
    }

    /**
     * @return array
     */
    public function v_1_2()
    {
        $insert = \Illuminate\Support\Facades\DB::table('parameters')->insert([
            [
                'name' => 'dash',
                'value' => 'avi mpg mpeg rm rmvb mov wmv asf ts flv',
            ]
        ]);
        $update = false;
        if ($insert) {
            $update = \Illuminate\Support\Facades\DB::table('parameters')
                ->where('name', 'video')
                ->update(['value' => 'mkv mp4 webm']);
            if ($update) {
                $update = \Illuminate\Support\Facades\DB::table('parameters')
                    ->where('name', 'app_version')
                    ->update(['value' => 'v1.2']);
            }
        }
        return $update ? $this->returnStatus('更新成功，version=v1.2') : $this->returnStatus('更新失败，数据迁移失败，请手动迁移', false);
    }

    /**
     * @return array
     */
    public function v_2_0()
    {
        $update = \Illuminate\Support\Facades\DB::table('parameters')
            ->where('name', 'app_version')
            ->update(['value' => 'v2.0']);
        return $update ? $this->returnStatus('更新成功，version=v2.0') : $this->returnStatus('更新失败，数据迁移失败，请手动迁移', false);
    }

    /**
     * @return array
     */
    public function v_3_0()
    {
        $data = \Illuminate\Support\Facades\DB::table('parameters')->pluck('value', 'name')->toArray();
        $data['app_version'] = 'v3.0';
        $saved = Tool::saveConfig($data);
        return $saved ? $this->returnStatus('更新成功，version=v3.0，请手动执行chmod 777 storage/app/config.json') : $this->returnStatus('更新失败，数据迁移失败，请手动迁移', false);
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
