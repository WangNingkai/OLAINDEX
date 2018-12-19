<?php

namespace App\Helpers;

use App\Http\Controllers\OauthController;
use Curl\Curl;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
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
        if (!empty(self::config('client_id'))
            && !empty(self::config('client_secret'))
            && !empty(self::config('redirect_uri'))
        ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 判断账号绑定
     *
     * @return bool
     */
    public static function hasBind()
    {
        if (!empty(self::config('access_token'))
            && !empty(self::config('refresh_token'))
            && !empty(self::config('access_token_expires'))
        ) {
            return true;
        } else {
            return false;
        }
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
     * @param bool   $success
     */
    public static function showMessage($message = '成功', $success = true)
    {
        $alertType = $success ? 'success' : 'danger';
        Session::put('alertMessage', $message);
        Session::put('alertType', $alertType);
    }

    /**
     *文件大小转换
     *
     * @param string $size 原始大小
     *
     * @return string 转换大小
     */
    public static function convertSize($size)
    {
        $units = array(' B', ' KB', ' MB', ' GB', ' TB');
        for ($i = 0; $size >= 1024 && $i < 4; $i++) {
            $size /= 1024;
        }

        return @round($size, 2).$units[$i];
    }

    /**
     * markdown转html
     *
     * @param      $markdown
     * @param bool $line
     *
     * @return mixed|string
     */
    public static function markdown2Html($markdown, $line = false)
    {
        $parser = new \Parsedown();
        if (!$line) {
            $html = $parser->text($markdown);
        } else {
            $html = $parser->line($markdown);
        }

        return $html;
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
     * 获取包屑导航url
     *
     * @param $key
     * @param $pathArr
     *
     * @return string
     */
    public static function getBreadcrumbUrl($key, $pathArr)
    {
        $pathArr = array_slice($pathArr, 0, $key);
        $url = '';
        foreach ($pathArr as $param) {
            $url .= '/'.$param;
        }

        return trim($url, '/');
    }

    /**
     * 获取父级url
     *
     * @param $pathArr
     *
     * @return string
     */
    public static function getParentUrl($pathArr)
    {
        array_pop($pathArr);
        if (count($pathArr) === 0) {
            return '';
        }
        $url = '';
        foreach ($pathArr as $param) {
            $url .= '/'.$param;
        }

        return trim($url, '/');
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
     * @param string $ext
     * @param bool   $img
     *
     * @return string
     */
    public static function getExtIcon($ext = '', $img = false)
    {
        $patterns = Constants::FILE_ICON;
        $icon = '';
        foreach ($patterns as $key => $suffix) {
            if (in_array($ext, $suffix[2])) {
                $icon = $img ? $suffix[1] : $suffix[0];
                break;
            } else {
                $icon = $img ? 'file' : 'fa-file-text-o';
            }
        }

        return $icon;
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
        $code = explode(' ', self::config('code'));
        $stream = explode(' ', self::config('stream'));
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
        $config = Cache::remember('config', 1440, function () {
            $file = storage_path('app/config.json');
            if (!file_exists($file)) {
                copy(
                    storage_path('app/example.config.json'),
                    storage_path('app/config.json')
                );
            };

            return self::readJson($file);
        });

        return $key ? (array_has($config, $key) ? (array_get($config, $key)
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
     * @param array  $array
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
    public static function getRequestPath(
        $path,
        $isQuery = true,
        $isFile = false
    ) {
        $path = self::getAbsolutePath($path);
        $query_path = trim($path, '/');
        if (!$isQuery) {
            return $query_path;
        }
        $query_path = self::getEncodeUrl(rawurldecode($query_path));
        $root = trim(self::getEncodeUrl(self::config('root')), '/');
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
    public static function getOriginPath(
        $path,
        $isQuery = true
    ) {
        $path = self::getAbsolutePath($path);
        $query_path = trim($path, '/');
        if (!$isQuery) {
            return $query_path;
        }
        $query_path = self::getEncodeUrl(rawurldecode($query_path));
        $root = trim(self::getEncodeUrl(self::config('root')), '/');
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

        return str_replace('//', '/', '/'.implode('/', $absolutes).'/');
    }

    /**
     * @param      $url
     * @param bool $cache
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \ErrorException
     */
    public static function getFileContent($url, $cache = true)
    {
        $key = 'one:content:'.$url;
        if ($cache && Cache::has($key)) {
            $content = Cache::get($key);
            if ($content) {
                return $content;
            }
        }
        $curl = new Curl();
        $curl->setConnectTimeout(5);
        $curl->setTimeout(120);
        $curl->setRetry(3);
        $curl->setOpts([
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_FAILONERROR    => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING       => 'gzip,deflate',
        ]);
        $curl->get($url);
        $curl->close();
        if ($curl->error) {
            Log::error(
                'Get OneDrive file content error.',
                [
                    'code' => $curl->errorCode,
                    'msg'  => $curl->errorMessage,
                ]
            );
            Tool::showMessage('Error: '.$curl->errorCode.': '
                .$curl->errorMessage, false);

            return '远程获取内容失败，请刷新重试';
        } else {
            $content = $curl->rawResponse;
            if ($cache) {
                Cache::put(
                    $key,
                    $content,
                    self::config('expires')
                );
            }

            return $content;
        }
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
                'one:quota',
                self::config('expires'),
                function () {
                    $response = OneDrive::getDrive();
                    if ($response['errno'] === 0) {
                        $quota = $response['data']['quota'];
                        foreach ($quota as $k => $item) {
                            if (!is_string($item)) {
                                $quota[$k] = Tool::convertSize($item);
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
        $expires = Tool::config('access_token_expires', 0);
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
                'one:account',
                Tool::config('expires'),
                function () {
                    $response = OneDrive::getMe();
                    if ($response['errno'] == 0) {
                        return array_get($response, 'data.userPrincipalName');
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

    /**
     * @param $ext
     *
     * @return string
     */
    public static function fileIcon($ext)
    {
        if (in_array($ext, ['ogg', 'mp3', 'wav'])) {
            return "audiotrack";
        }
        if (in_array($ext, ['apk'])) {
            return 'android';
        }
        if (in_array($ext, ['pdf'])) {
            return 'picture_as_pdf';
        }
        if (in_array($ext, [
            'bmp',
            'jpg',
            'jpeg',
            'png',
            'gif',
            'ico',
            'jpe',
        ])
        ) {
            return "image";
        }
        if (in_array($ext, [
            'mp4',
            'mkv',
            'webm',
            'avi',
            'mpg',
            'mpeg',
            'rm',
            'rmvb',
            'mov',
            'wmv',
            'mkv',
            'asf',
        ])
        ) {
            return "ondemand_video";
        }
        if (in_array($ext, [
            'html',
            'htm',
            'css',
            'go',
            'java',
            'js',
            'json',
            'txt',
            'sh',
            'md',
            'php',
        ])
        ) {
            return 'code';
        }

        return "insert_drive_file";
    }
}
