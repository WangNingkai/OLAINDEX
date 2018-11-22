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
        $this->warn('开始更新...');
        if (file_exists(database_path('database.sqlite'))) {
            $this->warn('如果您您已升级3.0，建议删除数据库文件再进行操作！');
            $this->warn('检测到数据库文件，即将从 database.sqlite 读取版本...');
            if ($this->confirm('继续从数据库获取版本吗？')) {
                $version = \Illuminate\Support\Facades\DB::table('parameters')
                    ->where('name', 'app_version')->value('value');
            } else {
                $version = Tool::config('app_version', 'dev');
            }
        } else {
            $version = Tool::config('app_version', 'dev');
        }
        if ($version == Constants::LATEST_VERSION) {
            $this->info('已是最新版本, 无需更新！');
            return;
        }
        switch ($version) {
            case 'dev':
                $this->v_1_0();
                $this->v_1_1();
                $this->v_1_2();
                $this->v_2_0();
                $this->v_3_0();
                $this->v_3_1();
                $result = $this->v_3_1_1();
                break;
            case 'v1.0':
                $this->v_1_1();
                $this->v_1_2();
                $this->v_2_0();
                $this->v_3_0();
                $this->v_3_1();
                $result = $this->v_3_1_1();
                break;
            case 'v1.1':
                $this->v_1_2();
                $this->v_2_0();
                $this->v_3_0();
                $this->v_3_1();
                $result = $this->v_3_1_1();
                break;
            case 'v1.2':
                $this->v_2_0();
                $this->v_3_0();
                $this->v_3_1();
                $result = $this->v_3_1_1();
                break;
            case 'v2.0':
                $this->v_3_0();
                $this->v_3_1();
                $result = $this->v_3_1_1();
                break;
            case 'v3.0':
                $this->v_3_1();
                $result = $this->v_3_1_1();
                break;
            case 'v3.1':
                $result = $this->v_3_1_1();
                break;
            default:
                $this->v_1_0();
                $this->v_1_1();
                $this->v_1_2();
                $this->v_2_0();
                $this->v_3_0();
                $this->v_3_1();
                $result = $this->v_3_1_1();
        }
        $this->call('cache:clear');
        $this->info($result['status'] . ':' . $result['msg']);
        clearstatcache(); // 清理文件缓存
        exit;
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
        if (!file_exists(storage_path('app/config.json'))) {
            $this->warn('未检测到配置文件！正在创建配置文件...');
            copy(storage_path('app/example.config.json'), storage_path('app/config.json'));
            $this->info('创建完成！');
        };
        $saved = Tool::saveConfig($data);
        return $saved ? $this->returnStatus('更新成功，version=v3.0，请手动执行chmod 777 storage/app/config.json ' . PHP_EOL . ' 并移除原数据库 rm -f database/database.sqlite') : $this->returnStatus('更新失败，数据迁移失败，请手动迁移', false);
    }

    /**
     * @return array
     */
    public function v_3_1()
    {
        if (!file_exists(storage_path('app/config.json'))) {
            $this->warn('未检测到配置文件！正在创建配置文件...');
            copy(storage_path('app/example.config.json'), storage_path('app/config.json'));
            $this->info('创建完成！');
            $config = Tool::config();
        } else {
            $config = Tool::config();
            $config = array_merge($config, ['app_version' => 'v3.1']);
        }
        $saved = Tool::saveConfig($config);
        return $saved ? $this->returnStatus('更新成功，version=v3.1') : $this->returnStatus('更新失败，数据迁移失败，请手动迁移', false);
    }

    /**
     * @return array
     */
    public function v_3_1_1()
    {
        if (!file_exists(storage_path('app/config.json'))) {
            $this->warn('未检测到配置文件！正在创建配置文件...');
            copy(storage_path('app/example.config.json'), storage_path('app/config.json'));
            $this->info('创建完成！');
            $config = Tool::config();
        } else {
            $config = Tool::config();
            $config = array_merge($config, ['app_version' => 'v3.1.1', 'app_type' => 'com']);
        }
        $saved = Tool::saveConfig($config);
        return $saved ? $this->returnStatus('更新成功，version=v3.1.1') : $this->returnStatus('更新失败，数据迁移失败，请手动迁移', false);
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
