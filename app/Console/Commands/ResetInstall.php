<?php

namespace App\Console\Commands;

use App\Models\Parameter;
use Illuminate\Console\Command;

class ResetInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '重置帐号';

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
        $data = [
            'access_token' => '',
            'refresh_token' => '',
            'access_token_expires' => ''
        ];
        $editData = [];
        foreach ($data as $k => $v) {
            $editData[] = [
                'name' => $k,
                'value' => $v
            ];
        }
        $update = new Parameter();
        if ($update->updateBatch($editData)) {
            $this->call('cache:clear');
            $this->warn('========== 重置成功，请重新登录 ==========');
        }
    }
}
