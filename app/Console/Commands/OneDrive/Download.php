<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\Tool;
use App\Http\Controllers\OneDriveController;
use Illuminate\Console\Command;

class Download extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:download
                            {path : 文件地址}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download File';

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
        $this->info('请稍等...');
        if (!refresh_token()) {
            $this->warn('请稍后重试...');
            return;
        }
        $target = $this->argument('path');
        $target_path = trim(Tool::handleUrl($target), '/');
        $path = empty($target_path) ? '/' : ":/{$target_path}:/";
        $od = new OneDriveController();
        $result = $od->getItemByPath($path);
        $response = Tool::handleResponse($result);
        $response['code'] == 200 ? $this->info("下载地址：{$response['data']['@microsoft.graph.downloadUrl']}") : $this->warn("获取文件失败!\n{$response['msg']} ");
    }
}
