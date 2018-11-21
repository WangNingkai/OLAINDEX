<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\Tool;
use App\Http\Controllers\OneDriveController;
use Illuminate\Console\Command;

class CopyFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:cp
                            {source : 源地址}
                            {target : 目标地址}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy File';

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
        $this->info('开始复制...');
        if (!refresh_token()) {
            $this->warn('请稍后重试...');
            return;
        }
        $source = $this->argument('source');
        $target = $this->argument('target');
        $od = new OneDriveController();
        $source_path = trim(Tool::handleUrl($source), '/');
        $item_id_request = Tool::handleResponse($od->pathToItemId(empty($source_path) ? '/' : ":/{$source_path}:/"));
        if ($item_id_request['code'] == 200)
            $item_id = $item_id_request['data']['id'];
        else {
            $this->warn('源路径异常!');
            return;
        }
        $target_path = trim(Tool::handleUrl($target), '/');
        $parent_id_request = Tool::handleResponse($od->pathToItemId(empty($target_path) ? '/' : ":/{$target_path}:/"));
        if ($parent_id_request['code'] == 200)
            $parent_id = $parent_id_request['data']['id'];
        else {
            $this->warn('源路径异常!');
            return;
        }
        /* @var $result \Illuminate\Http\JsonResponse */
        $result = $od->copy($item_id, $parent_id);
        $response = Tool::handleResponse($result);
        $response['code'] == 200 ? $this->info("复制路径查看进度\n{$response['data']['redirect']}") : $this->warn("复制失败\n{$response['msg']} ");
    }
}
