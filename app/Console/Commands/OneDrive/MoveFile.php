<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\Tool;
use App\Helpers\OneDrive;
use Illuminate\Console\Command;

class MoveFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:mv
                            {source : 源地址}
                            {target : 目标地址}
                            {rename? : 重命名}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move File';

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
        $this->info('开始移动...');
        $this->call('od:refresh');
        $source = $this->argument('source');
        $target = $this->argument('target');
        $rename = $this->argument('rename');
        $source_path = trim(Tool::handleUrl($source), '/');
        $item_id_request = OneDrive::responseToArray(OneDrive::pathToItemId(empty($source_path) ? '/' : ":/{$source_path}:/"));
        if ($item_id_request['code'] == 200)
            $item_id = $item_id_request['data']['id'];
        else {
            $this->warn('源路径异常！');
            exit;
        }
        $target_path = trim(Tool::handleUrl($target), '/');
        $parent_id_request = OneDrive::responseToArray(OneDrive::pathToItemId(empty($target_path) ? '/' : ":/{$target_path}:/"));
        if ($parent_id_request['code'] == 200)
            $parent_id = $parent_id_request['data']['id'];
        else {
            $this->warn('源路径异常！');
            exit;
        }
        /* @var $result \Illuminate\Http\JsonResponse */
        $result = OneDrive::move($item_id, $parent_id, $rename);
        $response = OneDrive::responseToArray($result);
        $response['code'] == 200 ? $this->info("移动成功！") : $this->warn("移动失败！\n{$response['msg']} ");
    }
}
