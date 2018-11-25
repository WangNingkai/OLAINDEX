<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\OneDrive;
use App\Helpers\Tool;
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
        $this->call('od:refresh');
        $name = $this->argument('name');
        $target = $this->argument('target');
        $target_path = trim(Tool::handleUrl($target), '/');
        $path = empty($target_path) ? '/' : ":/{$target_path}:/";
        $result = OneDrive::mkdirByPath($name, $path);
        $response = OneDrive::responseToArray($result);
        $response['code'] == 200 ? $this->info("创建目录成功!") : $this->warn("创建目录失败!\n{$response['msg']} ");
    }
}
