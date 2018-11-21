<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\Tool;
use Illuminate\Console\Command;

class SwitchType extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:switch {--type=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Switch Type';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        if ($this->option('type')) {
            $this->call('od:reset', ['--y' => true]);
            $type = $this->option('type');
        } else {
            if ($this->confirm('切换版本将会删除全部数据确定吗？')) {
                $this->call('od:reset', ['--y' => true]);
                $type = $this->choice('请选择切换的版本(com:国际通用 cn:世纪互联)', ['com', 'cn'], 'com');
            } else return;
        }
        $data = ['app_type' => $type];
        $saved = Tool::updateConfig($data);
        $saved ? $this->info('切换成功！') : $this->warn('切换失败！');

    }
}
