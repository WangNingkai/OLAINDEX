<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\OneDrive;
use App\Helpers\Tool;
use Illuminate\Console\Command;

class DeleteFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:rm
                            {remote : 文件地址}
                            {--f|force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete File';

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
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        $this->info('请稍等...');
        $this->call('od:refresh');
        if ($this->option('force')) return $this->delete();
        if ($this->confirm('删除后仅能通过OneDrive回收站找回，确认继续吗?')) {
            return $this->delete();
        }
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete()
    {
        $target = $this->argument('remote');
        $target_path = trim(Tool::handleUrl($target), '/');
        $id_request = OneDrive::responseToArray(OneDrive::pathToItemId(empty($target_path) ? '/' : ":/{$target_path}:/"));
        if ($id_request['code'] == 200)
            $_id = $id_request['data']['id'];
        else {
            $this->warn('路径异常!');
            exit;
        }
        /* @var $result \Illuminate\Http\JsonResponse */
        $result = OneDrive::delete($_id);
        $response = OneDrive::responseToArray($result);
        $this->call('cache:clear');
        $response['code'] == 200 ? $this->info("删除成功!") : $this->warn("删除失败!\n{$response['msg']} ");
    }
}
