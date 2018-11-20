<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\Tool;
use App\Http\Controllers\OneDriveController;
use Illuminate\Console\Command;

class CreateFolder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:mkdir
                            {name : 文件夹名称}
                            {target : 目标地址}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create New Folder';

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
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        if (!refresh_token()) {
            $this->warn('请稍后重试...');
            return;
        }
        $name = $this->argument('name');
        $target = $this->argument('target');
        $od = new OneDriveController();
        $target_path = trim(Tool::handleUrl($target), '/');
        $path = empty($target_path) ? '/' : ":/{$target_path}:/";
        $result = $od->mkdirByPath($name, $path);
        $response = Tool::handleResponse($result);
        $response['code'] == 200 ? $this->info("创建目录成功!") : $this->error("创建目录失败!\n{$response['msg']} ");
    }
}
