<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\OneDrive;
use App\Helpers\Tool;
use Illuminate\Console\Command;

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
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        $path = $this->argument('path');
        $this->getRecursive($path ?? '/');
    }

    /**
     * @param $path
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getChildren($path)
    {
        \Illuminate\Support\Facades\Artisan::call('od:refresh');
        $result = OneDrive::getChildrenByPath($path, '?select=id,name,size,lastModifiedDateTime,file,image,folder,@microsoft.graph.downloadUrl');
        $response = OneDrive::responseToArray($result);
        return $response['code'] === 200 ? $response['data'] : null;
    }

    /**
     * @param $path
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getRecursive($path)
    {
        set_time_limit(0);
        $this->info($path);
        $graphPath = OneDrive::getRequestPath($path);
        $data = $this->getChildren($graphPath);
        if (is_array($data)) {
            \Illuminate\Support\Facades\Cache::put('one:list:' . $graphPath, $data, Tool::config('expires'));
        } else {
            exit('Cache Error!');
        }
        foreach ((array)$data as $item) {
            if (array_has($item, 'folder')) {
                $this->getRecursive($path . $item['name'] . '/');
            }
        }
    }
}
