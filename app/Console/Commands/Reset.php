<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Reset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:app';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '重置应用';

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
        if ($this->confirm('重置应用将会清空全部数据库数据，继续吗？')) {
            $this->warn('========== 开始重置 ==========');
            $this->call('cache:clear');
            $this->warn('========== 开始重建数据 ==========');
            copy(storage_path('app/example.config.json'), storage_path('app/config.json'));
            $this->warn('========== 重建完成 ==========');
        } else
            return false;
    }
}
