<?php

use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\OauthController;
use App\Helpers\Tool;

if (!function_exists('quota')) {
    /**
     * 获取磁盘信息
     * @param string $key
     * @return array|mixed
     */
    function quota($key = '')
    {
        if (refresh_token()) {
            $quota = Cache::remember('quota', Tool::config('expires'), function () {
                $od = new \App\Http\Controllers\OneDriveController();
                $res = $od->getDrive();
                $quota = $res['quota'];
                foreach ($quota as $k => $item) {
                    if (!is_string($item)) {
                        $quota[$k] = Tool::convertSize($item);
                    }
                }
                return $quota;
            });
            return $key ? $quota[$key] : $quota;
        } else {
            return [];
        }
    }
}


if (!function_exists('refresh_token')) {
    /**
     * @return bool
     */
    function refresh_token()
    {
        $expires = Tool::config('access_token_expires');
        $hasExpired = $expires - time() < 0 ? true : false;
        if ($hasExpired) {
            $oauth = new OauthController();
            $res = json_decode($oauth->refreshToken(false), true);
            return $res['code'] === 200;
        } else {
            return true;
        }
    }
}

