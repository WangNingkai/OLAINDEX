<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\Tool;
use App\Http\Controllers\OneDriveController;
use Illuminate\Console\Command;

class Share extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:share
                            {remote : 文件地址}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ShareLink For File';

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
            exit;
        }
        $target = $this->argument('remote');
        $od = new OneDriveController();
        $target_path = trim(Tool::handleUrl($target), '/');
        $id_request = Tool::handleResponse($od->pathToItemId(empty($target_path) ? '/' : ":/{$target_path}:/"));
        if ($id_request['code'] == 200)
            $_id = $id_request['data']['id'];
        else {
            $this->warn('路径异常！');
            exit;
        }
        /* @var $result \Illuminate\Http\JsonResponse */
        $result = $od->createShareLink($_id);
        $response = Tool::handleResponse($result);
        if ($response['code'] == 200) {
            $direct = str_replace('15/download.aspx', '15/guestaccess.aspx', $response['data']['redirect']);
            $this->info("创建成功！\n分享链接： {$direct}");
        } else
            $this->warn("创建失败！\n{$response['msg']} ");
    }
}
