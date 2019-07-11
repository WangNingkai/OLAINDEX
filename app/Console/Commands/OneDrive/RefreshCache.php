<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\OneDrive;
use App\Helpers\Tool;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class RefreshCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:cache {path? : Target path to cache}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache Dir';

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
     * @throws \ErrorException
     */
    public function handle()
    {
        $path = $this->argument('path');

        $this->getRecursive(Tool::getOriginPath($path));
    }

    /**
     * @param $path
     *
     * @return mixed
     * @throws \ErrorException
     */
    public function getChildren($path)
    {
        $this->call(
            !empty($one_drive_id  = $this->option('one_drive_id')) 
                ? 'od:refresh --one_drive_id=' . $one_drive_id
                : 'od:refresh'
        );
        $response = OneDrive::getChildrenByPath(
            $path,
            '?select=id,eTag,name,size,lastModifiedDateTime,file,image,folder,@microsoft.graph.downloadUrl'
            . '&expand=thumbnails'
        );

        return $response['errno'] === 0 ? $response['data'] : null;
    }

    /**
     * @param $path
     *
     * @throws \ErrorException
     */
    public function getRecursive($path)
    {
        set_time_limit(0);
        $this->info($path);
        $data = $this->getChildren($path);
        if (is_array($data)) {
            Cache::put(
                'one_' . app('onedrive')->id .  ':list:' . $path,
                $data,
                app('onedrive')->expires
            );
        } else {
            exit('Cache Error!');
        }

        foreach ((array)$data as $item) {
            if (Arr::has($item, 'folder')) {
                $this->getRecursive($path . $item['name'] . '/');
            }
        }
    }
}
