<?php

use App\Http\Controllers\FetchController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\OauthController;
use App\Helpers\Tool;

if (!function_exists('id2path')) {
    /**
     * @param $id
     * @return string
     */
    function id2path($id)
    {
        $fetch = new FetchController();
        $file = $fetch->getFileById($id);
        $path = $file['parentReference']['path'];
        $root = Tool::config('root', '/');
        if ($root == '/') {
            $key = mb_strpos($path, ':');
            $path = mb_substr($path, $key + 1);
            $pathArr = explode('/', $path);
            unset($pathArr[0]);
        } else {
            $path = mb_strstr($path, $root, false, 'utf8');
            $start = mb_strlen($root, 'utf8');
            $rest = mb_substr($path, $start, null, 'utf8');
            $pathArr = explode('/', $rest);
        }
        array_push($pathArr, $file['name']);
        return trim(implode('/', $pathArr), '/');
    }
}

if (!function_exists('path2id')) {
    /**
     * @param $path
     * @param bool $root
     * @return mixed
     */
    function path2id($path, $root = false)
    {
        if ($root) {
            $path = Tool::config('root') . '/' . trim($path, '/');
        }
        $fetch = new FetchController();
        $item = $fetch->requestGraph('/me/drive/root:/' . trim($path, '/'));
        return $item['id'];
    }
}

if (!function_exists('quota')) {
    /**
     * 获取磁盘信息
     * @param string $key
     * @return bool
     */
    function quota($key = '')
    {
        if (refresh_token()) {
            $request = new RequestController();
            $res = $request->requestGraph('get', '/me/drive');
            $quota = $res['quota'];
            foreach ($quota as $k => $item) {
                $quota[$k] = Tool::convertSize($item);
            }
            return $key ? $quota[$key] : $quota;
        } else {
            return false;
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
