<?php

namespace App\Services;

use App\Helpers\OneDrive;
use Illuminate\Support\Facades\Cache;
use App\Helpers\Tool;

class CacheService
{
    public $method = '';

    public $path = '';

    public $onedrive = '';

    public function __construct($method, $path = '')
    {
        $this->method = $method;
        $this->path = $path;
        $this->onedrive = new OneDrive();
    }

    public function get($key = '', ...$params)
    {
        if (Cache::has($key)) {
            return Cache::get($key);
        }

        if (!method_exists($this->onedrive, $this->method)) {
            $this->error('没有该方法: ' . $this->method);
        }

        $response = call_user_func_array([$this->onedrive, $this->method], array_merge([$this->path], $params));

        if ($response['errno'] === 0) {
            $item = $response['data'];
            Cache::put($key, $item, Tool::config('expires', 600));
        } else {
            $this->error($response['msg']);
        }

        return $item;
    }

    public function error($message, $headers = [])
    {
        if (config('app.debug')) {
            abort(503, $message, $headers);
        } else {
            abort(500);
        }
    }
}
