<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\Tool;
use App\Http\Controllers\OneDriveController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ListItem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:ls
                            {path? : 文件地址}
                            {--offset=0 : 起始位置}
                            {--limit=10 : 限制数量}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List Items';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
//        $this->info('请稍等...');
        if (!refresh_token()) {
            $this->warn('请稍后重试...');
            return;
        }
        $target = $this->argument('path');
        $offset = $this->option('offset');
        $length = $this->option('limit');
        $target_path = trim(Tool::handleUrl($target), '/');
        $graphPath = empty($target_path) ? '/' : ":/{$target_path}:/";
        $data = Cache::remember('one:list:' . $graphPath, Tool::config('expires'), function () use ($graphPath) {
            $od = new OneDriveController();
            $result = $od->listChildrenByPath($graphPath);
            $response = Tool::handleResponse($result);
            return $response['code'] == 200 ? $response['data'] : [];
        });
        if (!$data) {
            $this->warn('出错了，请稍后重试...');
            return;
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
            $list[] = [$type, $folder, $owner, $size, $time, $item['name']];
        }
        return $list;
    }
}
