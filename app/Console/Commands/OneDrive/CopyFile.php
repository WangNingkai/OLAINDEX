<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\OneDrive;
use App\Helpers\Tool;
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
        $this->call('od:refresh');
        $source = $this->argument('source');
        $target = $this->argument('target');
        $source_path = trim(Tool::handleUrl($source), '/');
        $item_id_request = OneDrive::responseToArray(OneDrive::pathToItemId(empty($source_path) ? '/' : ":/{$source_path}:/"));
        if ($item_id_request['code'] === 200)
            $item_id = $item_id_request['data']['id'];
        else {
            $this->warn('源路径异常!');
            exit;
        }
        $target_path = trim(Tool::handleUrl($target), '/');
        $parent_id_request = OneDrive::responseToArray(OneDrive::pathToItemId(empty($target_path) ? '/' : ":/{$target_path}:/"));
        if ($parent_id_request['code'] === 200)
            $parent_id = $parent_id_request['data']['id'];
        else {
            $this->warn('目标路径异常!');
            exit;
        }
        /* @var $result \Illuminate\Http\JsonResponse */
        $result = OneDrive::copy($item_id, $parent_id);
        $response = OneDrive::responseToArray($result);
        $response['code'] == 200 ? $this->info("复制路径查看进度\n{$response['data']['redirect']}") : $this->warn("复制失败\n{$response['msg']} ");
    }
}
