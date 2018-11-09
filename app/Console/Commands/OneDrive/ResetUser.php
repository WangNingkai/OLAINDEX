<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\Tool;
use Illuminate\Console\Command;

class ResetUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:logout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset User';

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
        $this->warn('========== 开始重置帐号信息 ==========');
        if ($this->confirm('重置账号可能出现无法登录的错误，建议重置应用，确认继续吗')) {
            $config = Tool::config();
            $data = [
                'access_token' => '',
                'refresh_token' => '',
                'access_token_expires' => 0,
                'root' => '/',
                'image_hosting' => 0,
                'image_hosting_path' => ''
            ];
            $config = array_merge($config, $data);
            $saved = Tool::saveConfig($config);
            if ($saved) {
                $this->call('cache:clear');
                $this->warn('========== 重置成功，请重新登录 ==========');
            }
        }
    }
}
