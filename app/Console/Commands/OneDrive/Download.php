<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\OneDrive;
use App\Helpers\Tool;
use Illuminate\Console\Command;

class Download extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:download
                            {remote : 文件地址}';

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
        $this->call('od:refresh');
        $target = $this->argument('remote');
        $target_path = trim(Tool::handleUrl($target), '/');
        $path = empty($target_path) ? '/' : ":/{$target_path}:/";
        $result = OneDrive::getItemByPath($path);
        $response = OneDrive::responseToArray($result);
        $response['code'] === 200 ? $this->info("下载地址：{$response['data']['@microsoft.graph.downloadUrl']}") : $this->warn("获取文件失败!\n{$response['msg']} ");
    }
}
