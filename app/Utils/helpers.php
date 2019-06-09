<?php

use App\Models\Setting;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

if (!function_exists('word_time')) {
    /**
     * 把日期或者时间戳转为距离现在的时间
     *
     * @param $time
     * @return bool|string
     */
    function word_time($time)
    {
// 如果是日期格式的时间;则先转为时间戳
        if (!is_int($time)) {
            $time = strtotime($time);
        }
        $int = time() - $time;
        if ($int <= 2) {
            $str = sprintf('刚刚', $int);
        } elseif ($int < 60) {
            $str = sprintf('%d秒前', $int);
        } elseif ($int < 3600) {
            $str = sprintf('%d分钟前', floor($int / 60));
        } elseif ($int < 86400) {
            $str = sprintf('%d小时前', floor($int / 3600));
        } elseif ($int < 1728000) {
            $str = sprintf('%d天前', floor($int / 86400));
        } else {
            $str = date('Y-m-d H:i:s', $time);
        }
        return $str;
    }
}
if (!function_exists('is_json')) {
    /**
     * 判断字符串是否是json
     *
     * @param $json
     * @return bool
     */
    function is_json($json)
    {
        json_decode($json, true);
        return (json_last_error() === JSON_ERROR_NONE);
    }
}

if (!function_exists('setting')) {
    /**
     * 获取设置
     * @param $key
     * @param string $default
     * @return mixed
     */
    function setting($key = '', $default = '')
    {
        $setting = Cache::remember('setting', 60 * 60, static function () {
            $setting = Setting::all()->toArray();
            $configData = [];
            foreach ($setting as $detail) {
                $configData[$detail['name']] = $detail['value'];
            }
            return $configData;
        });
        $setting = collect($setting);

        return $key ? $setting->get($key, $default) : $setting;

    }
}