<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InitInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '初始化安装';

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
        if (!file_exists(database_path('database.sqlite'))) {
            $this->alert(' 未检测到数据库文件！请确认已在应用数据库目录创建 database.sqlite！');
            $this->warn('创建命令 [ touch database/database.sqlite ]');
            return false;
        };
        $this->warn('========== 初始化配置 ==========');
        $app_url = $this->ask('请输入应用域名');
        $envExample = file_get_contents(base_path('.env.example'));
        $search_db = [
            'APP_URL=http://localhost',
        ];
        $replace_db = [
            'APP_URL='.$app_url,
        ];
        $env = str_replace($search_db, $replace_db, $envExample);
        if (file_exists(base_path('.env'))) {
            if ($this->confirm('目录存在 .env 文件，即将覆盖，继续吗？')) {
                @unlink(base_path('.env'));
                file_put_contents(base_path('.env'), $env);
            } else
                return false;
        }
        file_put_contents(base_path('.env'), $env);
        $this->alert(' 应用回调地址请填写：'.trim($app_url,'/') .'/oauth ');
        $this->call('key:generate');
        $this->warn('========== 正在执行数据库操作 ==========');
        $this->call('migrate');
        $this->call('db:seed');
        $this->alert('手动执行以下命令确保目录读写权限');
        $this->warn('chmod -R 755 storage/* && chown -R www:www *');
        $this->warn('========== 预安装完成，请继续下面的操作 ==========');
    }
}
