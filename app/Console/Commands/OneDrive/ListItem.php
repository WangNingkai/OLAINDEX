<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\OneDrive;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class ListItem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:ls
                           {remote? : Remote Path}
                            {--a|all : List All Info}
                            {--id= : ID}
                            {--offset=0 : Start}
                            {--limit=20 : Length}';

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
        $this->call(
            !empty($one_drive_id  = $this->option('one_drive_id')) 
                ? 'od:refresh --one_drive_id=' . $one_drive_id
                : 'od:refresh'
        );
        $remote = $this->argument('remote');
        $id = $this->option('id');
        $offset = $this->option('offset');
        $length = $this->option('limit');
        if ($id) {
            $data = Cache::remember(
                'one_' . app('onedrive')->id . ':list:id:' . $id,
                app('onedrive')->expires,
                function () use ($id) {
                    $response = OneDrive::getChildren($id);

                    return $response['errno'] === 0 ? $response['data'] : [];
                }
            );
        } else {
            $data = Cache::remember(
                'one_' . app('onedrive')->id . ':list:path:'.$remote,
                app('onedrive')->expires,
                function () use ($remote) {
                    $response = OneDrive::getChildrenByPath($remote);

                    return $response['errno'] === 0 ? $response['data'] : [];
                }
            );
        }

        if (!$data) {
            $this->error('Please confirm your options and try again later!');
            $this->call('cache:clear');
            exit;
        }

        $data = $this->format($data);
        $items = array_slice($data, $offset, $length);
        $headers = [];
        $this->line('total '.count($items));
        $this->table($headers, $items, 'compact');
    }

    /**
     * @param $data
     *
     * @return array
     */
    public function format($data)
    {
        $list = [];
        foreach ($data as $item) {
            $type = Arr::has($item, 'folder') ? 'd' : '-';
            $size = convertSize($item['size']);
            $time = date('M m H:i', strtotime($item['lastModifiedDateTime']));
            $folder = Arr::has($item, 'folder') ? Arr::get($item, 'folder.childCount'): '1';
            $owner = Arr::get($item, 'createdBy.user.displayName');
            if ($this->option('all')) {
                $content = [
                    $type,
                    $item['id'],
                    $folder,
                    $owner,
                    $size,
                    $time,
                    $item['name'],
                ];
            } else {
                $content = [
                    $type,
                    $folder,
                    $owner,
                    $size,
                    $time,
                    $item['name'],
                ];
            }
            $list[] = $content;
        }

        return $list;
    }
}
