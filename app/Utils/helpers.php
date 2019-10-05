<?php

use App\Http\Controllers\OauthController;
use App\Models\Setting;
use App\Service\OneDrive;
use App\Utils\Tool;

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
        $setting = \Cache::remember('setting', 60 * 60, static function () {

            try {
                $setting = Setting::all()->toArray();
            } catch (Exception $e) {
                return [];
            }
            $data = [];
            foreach ($setting as $detail) {
                $data[$detail['name']] = $detail['value'];
            }
            return $data;
        });
        $setting = collect($setting);
        return $key ? $setting->get($key, $default) : $setting;
    }
}


if (!function_exists('one_account')) {

    /**
     * 获取绑定OneDrive用户信息
     *
     * @param string $key
     * @return \Illuminate\Support\Collection|mixed
     */
    function one_account($key = '')
    {
        $account = collect([
            'account_type' => setting('account_type'),
            'access_token' => setting('access_token'),
            'account_email' => setting('account_email'),
        ]);
        return $key ? $account->get($key, '') : $account->toArray();
    }
}


if (!function_exists('one_info')) {

    /**
     * 获取绑定OneDrive信息
     * @param string $key
     * @return array|\Illuminate\Support\Collection|mixed
     * @throws ErrorException
     */
    function one_info($key = '')
    {
        if (refresh_token()) {
            $quota = Cache::remember(
                'one:quota',
                setting('expires'),
                static function () {
                    $response = OneDrive::getInstance(one_account())->getDriveInfo();
                    if ($response['errno'] === 0) {
                        $quota = $response['data']['quota'];
                        foreach ($quota as $k => $item) {
                            if (!is_string($item)) {
                                $quota[$k] = Tool::convertSize($item);
                            }
                        }
                        return $quota;
                    }
                    return [];
                }
            );
            $info = collect($quota);
            return $key ? $info->get($key, '') : $info;
        }
        return [];
    }
}

if (!function_exists('refresh_token')) {

    /**
     * 刷新token
     * @return bool
     * @throws ErrorException
     */
    function refresh_token()
    {
        $expires = setting('access_token_expires', 0);
        $expires = strtotime($expires);
        $hasExpired = $expires - time() <= 0;
        if ($hasExpired) {
            $oauth = new OauthController();
            $res = json_decode($oauth->refreshToken(false), true);

            return $res['code'] === 200;
        }
        return true;
    }
}

