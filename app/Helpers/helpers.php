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
            $quota = Cache::remember('one:quota', Tool::config('expires'), function () {
                $od = new \App\Http\Controllers\OneDriveController();
                $drive = $od->getDrive();
                $res = Tool::handleResponse($drive);
                if ($res['code'] == 200) {
                    $quota = $res['data']['quota'];
                    foreach ($quota as $k => $item) {
                        if (!is_string($item)) {
                            $quota[$k] = Tool::convertSize($item);
                        }
                    }
                    return $quota;
                } else {
                    return [];
                }
            });
            return $key ? $quota[$key] ?? '' : $quota ?? '';
        } else {
            return '';
        }
    }
}


if (!function_exists('refresh_token')) {
    /**
     * 刷新refresh_token
     * @return bool
     */
    function refresh_token()
    {
        $expires = Tool::config('access_token_expires', 0);
        $hasExpired = $expires - time() <= 0 ? true : false;
        if ($hasExpired) {
            $oauth = new OauthController();
            $res = json_decode($oauth->refreshToken(), true);
            return $res['code'] === 200;
        } else {
            return true;
        }
    }
}

if (!function_exists('bind_account')) {
    /**
     * 绑定账户
     * @return mixed|string
     */
    function bind_account()
    {
        if (refresh_token()) {
            $account = Cache::remember('one:account', Tool::config('expires'), function () {
                $od = new \App\Http\Controllers\OneDriveController();
                $drive = $od->getMe();
                $res = Tool::handleResponse($drive);
                if ($res['code'] == 200) {
                    return array_get($res, 'data.userPrincipalName');
                } else {
                    return '';
                }
            });
            return $account;
        } else {
            return '';
        }
    }
}

if (!function_exists('has_bind')) {

    /**
     * 判断账号绑定
     * @return bool
     */
    function has_bind()
    {
        if (Tool::config('access_token') != '' && Tool::config('refresh_token') != '' && Tool::config('access_token_expires') != '') {
            return true;
        } else return false;
    }
}
