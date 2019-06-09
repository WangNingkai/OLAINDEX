<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\Constants;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Install extends Command
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
    protected $description = 'Install App';

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
     * Execute Command
     */
    public function handle()
    {
        $this->info(Constants::LOGO);
        // 初始化操作
        $this->call('cache:clear');
        $this->warn('Please make sure you have rights to configure!');
        $this->info('chmod -R 755 storage/* && chown -R www:www *');
        // sqlite数据库文件检测
        if (!file_exists(base_path('database/database.sqlite'))) {
            $this->warn('Missing the Database File');
            copy(
                base_path('database/database.sample.sqlite'),
                base_path('database/database.sqlite')
            );
            $this->info('Done!');
        }
        // 执行数据迁移
        $this->call('migrate:reset');
        $this->call('migrate');
        if (!file_exists(base_path('.env.example'))) {
            $this->warn('No [.env.example] File,please make sure the project complete!');
            exit;
        }
        $app_url = $this->ask('Bind Domain(For Authorize)');
        $search_db = [
            'APP_KEY=',
            'APP_URL=http://localhost:8000',
        ];
        $replace_db = [
            'APP_KEY=' . Str::random(32),
            'APP_URL=' . $app_url,
        ];
        // 初始化env文件
        $envExample = file_get_contents(base_path('.env.example'));
        $env = str_replace($search_db, $replace_db, $envExample);
        if (file_exists(base_path('.env'))) {
            if ($this->confirm('Already have [.env] ,overwrite?')) {
                @unlink(base_path('.env'));
                file_put_contents(base_path('.env'), $env);
            }
        } else {
            file_put_contents(base_path('.env'), $env);
        }

        // 生成配置缓存否则报错
        $this->call('config:cache');

        // 初始化用户
        DB::table('users')->updateOrInsert([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('12345678'),
        ]);
        $this->warn('username:[ admin ] email:[ admin@admin.com ] password:[ 12345678 ]');

        $this->warn('All Done!');
    }
}
