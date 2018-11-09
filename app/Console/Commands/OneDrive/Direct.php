<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\Tool;
use App\Http\Controllers\OneDriveController;
use Illuminate\Console\Command;

class Direct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:direct
                            {path : 文件地址}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'DirectDownloadLink For File';

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
            $this->error('refresh token error');
        }
        $target = $this->argument('path');
        $od = new OneDriveController();
        $target_path = trim(Tool::handleUrl($target), '/');
        $id_request = Tool::handleResponse($od->pathToItemId(empty($target_path) ? '/' : ":/{$target_path}:/"));
        if ($id_request['code'] == 200)
            $_id = $id_request['data']['id'];
        else {
            $this->error('路径异常');
            return;
        }
        /* @var $result \Illuminate\Http\JsonResponse */
        $result = $od->createShareLink($_id);
        /* @var $result \Illuminate\Http\JsonResponse */
        $result = $od->createShareLink($_id);
        $response = Tool::handleResponse($result);
        $response['code'] == 200 ? $this->info("创建成功\n永久直链地址： {$response['data']['redirect']}") : $this->error("创建失败\n{$response['msg']} ");
    }
}
