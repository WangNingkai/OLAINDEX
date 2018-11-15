<?php

namespace App\Console\Commands\OneDrive;

use Illuminate\Console\Command;

class InitInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Init Install';

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
     * @return bool
     */
    public function handle()
    {
        $this->call('cache:clear');
        $this->warn('确保已经手动执行以下目录读写权限命令！');
        $this->info('chmod -R 755 storage/* && chown -R www:www *');
        if (!file_exists(storage_path('app/config.json'))) {
            $this->warn('未检测到配置文件！正在创建配置文件！');
            copy(storage_path('app/example.config.json'), storage_path('app/config.json'));
            $this->info('创建完成！');
        };
        $this->warn('开始初始化配置 ...');
        if (!file_exists(base_path('.env.example'))) {
            $this->warn('目录不存在 .env.example 文件，请确保拉取仓库完整！');
            return false;
        }
        $app_url = $this->ask('请输入应用域名');
        $envExample = file_get_contents(base_path('.env.example'));
        $search_db = [
            'APP_KEY=',
            'APP_URL=http://localhost:8000',
        ];
        $replace_db = [
            'APP_KEY=' . str_random(32),
            'APP_URL=' . $app_url,
        ];
        $env = str_replace($search_db, $replace_db, $envExample);
        if (file_exists(base_path('.env'))) {
            if ($this->confirm('目录存在 .env 文件，即将覆盖，继续吗？')) {
                @unlink(base_path('.env'));
                file_put_contents(base_path('.env'), $env);
            }
        } else {
            file_put_contents(base_path('.env'), $env);
        }
        $this->info('应用回调地址请填写：' . trim($app_url, '/') . '/oauth ');
        $this->call('config:cache'); // 生成配置缓存否则报错
        $this->warn('后台登录原始密码：12345678');
        $this->info('请手动执行 chmod 777 storage/app/config.json 确保配置文件权限，否则会出现403错误');
        $this->warn('========== 预安装完成，请继续下面的操作 ==========');
    }
}
