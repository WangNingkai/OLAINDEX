<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\Tool;
use App\Http\Controllers\OneDriveController;
use Illuminate\Console\Command;

class Search extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:search
                            {keyword : 关键词}
                            {--id= : id}
                            {--remote=/ : 查询路径}
                            {--offset=0 : 起始位置}
                            {--limit=10 : 限制数量}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Search Items';

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
        $keyword = $this->argument('keyword');
        $target = $this->option('remote');
        $offset = $this->option('offset');
        $length = $this->option('limit');
        $target_path = trim(Tool::handleUrl($target), '/');
        $od = new OneDriveController();
        if ($id = $this->option('id')) {
            $result = $od->getItem($id);
        } else {
            $result = $od->search($target_path, $keyword);
        }
        /* @var $result \Illuminate\Http\JsonResponse */
        $response = Tool::handleResponse($result);
        $data = $response['code'] == 200 ? $response['data'] : [];
        if (!$data) {
            $this->warn('出错了，请稍后重试...');
            exit;
        }
        if ($id = $this->option('id')) {
            $data = [$data];
        }
        $data = $this->format($data);
        $items = array_slice($data, $offset, $length);
        $headers = [];
        $this->line('total ' . count($items));
        $this->table($headers, $items, 'compact');
    }

    /**
     * @param $data
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function format($data)
    {
        $list = [];
        foreach ($data as $item) {
            $type = array_has($item, 'folder') ? 'd' : '-';
            $size = Tool::convertSize($item['size']);
            $time = date('M m H:i', strtotime($item['lastModifiedDateTime']));
            $folder = array_has($item, 'folder') ? array_get($item, 'folder.childCount') : '1';
            $owner = array_get($item, 'createdBy.user.displayName');
            if ($id = $this->option('id')) {
                $od = new OneDriveController();
                $result = $od->itemIdToPath($item['id']);
                /* @var $result \Illuminate\Http\JsonResponse */
                $response = Tool::handleResponse($result);
                $path = $response['code'] == 200 ? $response['data']['path'] : '获取目录失败';
                $content = [$type, $path, $folder, $owner, $size, $time, $item['name']];
            } else {
                $content = [$type, $item['id'], $folder, $owner, $size, $time, $item['name']];
            }
            $list[] = $content;
        }
        return $list;
    }
}
