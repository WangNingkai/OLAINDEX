<?php

namespace App\Helpers;

use App\Http\Controllers\OauthController;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class Tool
{
    /**
     * 判断密钥配置
     *
     * @return bool
     */
    public static function hasConfig()
    {
        return app('onedrive')->is_configuraed;
    }

    /**
     * 判断账号绑定
     *
     * @return bool
     */
    public static function hasBind()
    {
        return app('onedrive')->is_binded;
    }

    /**
     * 判断列表是否含有图片
     *
     * @param $items
     *
     * @return bool
     */
    public static function hasImages($items)
    {
        $hasImage = false;
        foreach ($items as $item) {
            if (isset($item['image'])) {
                $hasImage = true;
                break;
            }
        }

        return $hasImage;
    }

    /**
     * 操作成功或者失败的提示
     *
     * @param string $message
     * @param bool $success
     */
    public static function showMessage($message = '成功', $success = true)
    {
        $alertType = $success ? 'success' : 'danger';
        Session::put('alertMessage', $message);
        Session::put('alertType', $alertType);
    }

    /**
     * 数组分页
     *
     * @param $items
     * @param $perPage
     *
     * @return LengthAwarePaginator
     */
    public static function paginate($items, $perPage)
    {
        $pageStart = request()->get('page', 1);
        // Start displaying items from this number;
        $offSet = ($pageStart * $perPage) - $perPage;
        // Get only the items you need using array_slice
        $itemsForCurrentPage = array_slice($items, $offSet, $perPage, true);

        return new LengthAwarePaginator(
            $itemsForCurrentPage,
            count($items),
            $perPage,
            Paginator::resolveCurrentPage(),
            ['path' => Paginator::resolveCurrentPath()]
        );
    }

    /**
     * 处理url
     *
     * @param $path
     *
     * @return string
     */
    public static function getEncodeUrl($path)
    {
        $url = [];
        foreach (explode('/', $path) as $key => $value) {
            if (empty(!$value)) {
                $url[] = rawurlencode($value);
            }
        }

        return @implode('/', $url);
    }

    /**
     * 文件是否可编辑
     *
     * @param $file
     *
     * @return bool
     */
    public static function canEdit($file)
    {
        $code = explode(' ', Arr::get(app('onedrive')->settings, 'code'));
        $stream = explode(' ', Arr::get(app('onedrive')->settings, 'stream'));
        $canEditExt = array_merge($code, $stream);
        $isText = in_array($file['ext'], $canEditExt);
        $isBigFile = $file['size'] > 5 * 1024 * 1024 ?: false;

        if (!$isBigFile && $isText) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 保存配置到json文件
     *
     * @param $config
     *
     * @return bool
     */
    public static function saveConfig($config)
    {
        $file = storage_path('app/config.json');

        return self::writeJson($file, $config);
    }

    /**
     * 更新配置
     *
     * @param $data
     *
     * @return bool
     */
    public static function updateConfig($data)
    {
        $config = self::config();
        $config = array_merge($config, $data);
        $saved = self::saveConfig($config);
        Cache::forget('config');

        return $saved;
    }

    /**
     * 从json文件读取配置
     *
     * @param string $key
     * @param string $default
     *
     * @return string|array
     */
    public static function config($key = '', $default = '')
    {
        $config = Cache::remember('config', 1440 * 60, function () {
            $file = storage_path('app/config.json');
            if (!file_exists($file)) {
                copy(
                    storage_path('app/example.config.json'),
                    storage_path('app/config.json')
                );
            };

            return self::readJson($file);
        });

        return $key ? (Arr::has($config, $key) ? (Arr::get($config, $key)
            ?: $default) : $default) : $config;
    }

    /**
     * @param string $file
     *
     * @return array|bool
     */
    public static function readJson(string $file)
    {
        try {
            $config = file_get_contents($file);

            return json_decode($config, true);
        } catch (\Exception $e) {
            return abort(403, $e->getMessage());
        }
    }

    /**
     * @param string $file
     * @param array $array
     *
     * @return bool|int
     */
    public static function writeJson(string $file, array $array)
    {
        try {
            return file_put_contents($file, json_encode($array));
        } catch (\Exception $e) {
            return abort(403, $e->getMessage());
        }
    }

    /**
     * 解析路径
     *
     * @param      $path
     * @param bool $isQuery
     * @param bool $isFile
     *
     * @return string
     */
    public static function getRequestPath($path, $isQuery = true, $isFile = false)
    {
        $path = self::getAbsolutePath($path);
        $query_path = trim($path, '/');
        if (!$isQuery) {
            return $query_path;
        }
        $query_path = self::getEncodeUrl(rawurldecode($query_path));
        $root = trim(self::getEncodeUrl(app('onedrive')->root), '/');
        if ($query_path) {
            $request_path = empty($root) ? ":/{$query_path}:/"
                : ":/{$root}/{$query_path}:/";
        } else {
            $request_path = empty($root) ? '/' : ":/{$root}:/";
        }
        if ($isFile) {
            return rtrim($request_path, ':/');
        }

        return $request_path;
    }

    /**
     * @param      $path
     * @param bool $isQuery
     *
     * @return string
     */
    public static function getOriginPath($path, $isQuery = true)
    {
        $path = self::getAbsolutePath($path);
        $query_path = trim($path, '/');
        if (!$isQuery) {
            return $query_path;
        }
        $query_path = self::getEncodeUrl(rawurldecode($query_path));
        $root = trim(self::getEncodeUrl(app('onedrive')->root), '/');
        if ($query_path) {
            $request_path = empty($root) ?
                $query_path
                : "{$root}/{$query_path}";
        } else {
            $request_path = empty($root) ? '/' : $root;
        }

        return self::getAbsolutePath($request_path);
    }

    /**
     * 绝对路径转换
     *
     * @param $path
     *
     * @return mixed
     */
    public static function getAbsolutePath($path)
    {
        $path = str_replace(['/', '\\', '//'], '/', $path);
        $parts = array_filter(explode('/', $path), 'strlen');
        $absolutes = [];
        foreach ($parts as $part) {
            if ('.' === $part) {
                continue;
            }
            if ('..' === $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }

        return str_replace('//', '/', '/' . implode('/', $absolutes) . '/');
    }

    /**
     * @param string $key
     *
     * @return mixed|string
     * @throws \ErrorException
     */
    public static function getOneDriveInfo($key = '')
    {
        if (self::refreshToken()) {
            $quota = Cache::remember(
                'one_' . app('onedrive')->id . ':quota',
                app('onedrive')->expires,
                function () {
                    $response = OneDrive::getDrive();
                    if ($response['errno'] === 0) {
                        $quota = $response['data']['quota'];
                        foreach ($quota as $k => $item) {
                            if (!is_string($item)) {
                                $quota[$k] = convertSize($item);
                                $quota['raw_' . $k] = $item;
                            }
                        }

                        return $quota;
                    } else {
                        return [];
                    }
                }
            );

            return $key ? $quota[$key] ?? '' : $quota ?? '';
        } else {
            return '';
        }
    }

    /**
     * @return bool
     * @throws \ErrorException
     */
    public static function refreshToken()
    {
        $expires = app('onedrive')->access_token_expires;
        $hasExpired = $expires - time() <= 0 ? true : false;
        if ($hasExpired) {
            $oauth = new OauthController();
            $res = json_decode($oauth->refreshToken(false), true);

            return $res['code'] === 200;
        } else {
            return true;
        }
    }

    /**
     * @return mixed|string
     * @throws \ErrorException
     */
    public static function getBindAccount()
    {
        if (self::refreshToken()) {
            $account = Cache::remember(
                'one_' . app('onedrive')->id . ':account',
                app('onedrive')->expires,
                function () {
                    $response = OneDrive::getMe();
                    if ($response['errno'] == 0) {
                        return Arr::get($response, 'data.userPrincipalName');
                    } else {
                        return '';
                    }
                }
            );

            return $account;
        } else {
            return '';
        }
    }

    /**
     * 解析加密目录
     *
     * @param $str
     *
     * @return array
     */
    public static function handleEncryptDir($str)
    {
        $str = str_replace(PHP_EOL, '', $str);
        $str = trim($str, ',');
        $encryptPathList = explode(',', $str);
        $all = [];
        foreach ($encryptPathList as $encryptPathDir) {
            $pathItem = explode(' ', $encryptPathDir);
            $password = array_pop($pathItem);
            $pa = array_fill_keys($pathItem, $password);
            $all = array_merge($pa, $all);
        }
        uksort($all, [Tool::class, 'lenSort']);

        return $all;
    }

    /**
     * @param $a
     * @param $b
     *
     * @return int
     */
    public static function lenSort($a, $b)
    {
        $countA = count(explode('/', self::getAbsolutePath($a)));
        $countB = count(explode('/', self::getAbsolutePath($b)));

        return $countB - $countA;
    }
}
