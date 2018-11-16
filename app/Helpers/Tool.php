<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class Tool
{
    /**
     * 操作成功或者失败的提示
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
     *文件大小转换
     * @param string $size 原始大小
     * @return string 转换大小
     */
    public static function convertSize($size)
    {
        $units = array(' B', ' KB', ' MB', ' GB', ' TB');
        for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
        return @round($size, 2) . $units[$i];
    }

    /**
     * markdown转html
     *
     * @param string $markdown
     * @return string
     */
    public static function markdown2Html($markdown)
    {
        $parser = new \Parsedown();
        $html = $parser->text($markdown);
        $html = str_replace('<code class="', '<code class="lang-', $html);
        return $html;
    }

    /**
     * 数组分页
     * @param $items
     * @param $perPage
     * @return LengthAwarePaginator
     */
    public static function paginate($items, $perPage)
    {
        $pageStart = request()->get('page', 1);
        // Start displaying items from this number;
        $offSet = ($pageStart * $perPage) - $perPage;

        // Get only the items you need using array_slice
        $itemsForCurrentPage = array_slice($items, $offSet, $perPage, true);

        return new LengthAwarePaginator($itemsForCurrentPage, count($items), $perPage, Paginator::resolveCurrentPage(), ['path' => Paginator::resolveCurrentPath()]);
    }

    /**
     * 获取包屑导航url
     * @param $key
     * @param $pathArr
     * @return string
     */
    public static function getBreadcrumbUrl($key, $pathArr)
    {
        $pathArr = array_slice($pathArr, 0, $key);
        $url = '';
        foreach ($pathArr as $param) {
            $url .= '/' . $param;
        }
        return trim($url, '/');
    }

    /**
     * 获取父级url
     * @param $pathArr
     * @return string
     */
    public static function getParentUrl($pathArr)
    {
        array_pop($pathArr);
        if (count($pathArr) == 0) {
            return '';
        }
        $url = '';
        foreach ($pathArr as $param) {
            $url .= '/' . $param;
        }
        return trim($url, '/');
    }

    /**
     * 处理url
     * @param $path
     * @return string
     */
    public static function handleUrl($path)
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
     * 获取文件图标
     * @param $ext
     * @return string
     */
    public static function getExtIcon($ext = '')
    {
        $patterns = Constants::FILE_ICON;
        $icon = '';
        foreach ($patterns as $key => $suffix) {
            if (in_array($ext, $suffix[1])) {
                $icon = $suffix[0];
                break;
            } else {
                $icon = 'fa-file-text-o';
            }
        }
        return $icon;
    }

    /**
     * 文件是否可编辑
     * @param $file
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
     * @param $config
     * @return bool
     */
    public static function saveConfig($config)
    {
        $file = storage_path('app/config.json');
        if (!is_writable($file)) {
            self::showMessage('权限不足，无法写入配置文件', false);
            abort(403, '权限不足，无法写入配置文件');
        };
        $saved = file_put_contents($file, json_encode($config));
        if ($saved) {
            Cache::forget('config');
            return true;
        } else {
            return false;
        }
    }

    /**
     * 更新配置
     * @param $data
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
     * @param string $key
     * @param string $default
     * @return mixed|string
     */
    public static function config($key = '', $default = '')
    {
        $config = Cache::remember('config', 1440, function () {
            $file = storage_path('app/config.json');
            if (!file_exists($file)) {
                copy(storage_path('app/example.config.json'), storage_path('app/config.json'));
            };
            if (!is_readable($file)) {
                self::showMessage('权限不足，无法预取配置文件', false);
                abort(403, '权限不足，无法预取配置文件');
            };
            $config = file_get_contents($file);
            return json_decode($config, true);
        });
        return $key ? (array_key_exists($key, $config) ? ($config[$key] ?: $default) : $default) : $config;
    }

    /**
     * 解析路径
     * @param $path
     * @param bool $isQuery
     * @param bool $isFile
     * @return string
     */
    public static function convertPath($path, $isQuery = true, $isFile = false)
    {
        $origin_path = trim($path, '/');
        $path_array = explode('/', $origin_path);
        $base = ['home', 'view', 'show', 'download'];
        if (in_array($path_array[0], $base)) {
            unset($path_array[0]);
            $query_path = implode('/', $path_array);
        } else $query_path = $origin_path;
        if (!$isQuery) return $query_path;
        $query_path = Tool::handleUrl(rawurldecode($query_path));
        $root = trim(self::handleUrl(self::config('root')), '/');
        if ($query_path)
            $request_path = empty($root) ? ":/{$query_path}:/" : ":/{$root}/{$query_path}:/";
        else
            $request_path = empty($root) ? '/' : ":/{$root}:/";
        if ($isFile)
            return rtrim($request_path, ':/');
        return $request_path;
    }

    /**
     * 绝对路径转换
     * @param $path
     * @return mixed
     */
    public static function getAbsolutePath($path)
    {
        $path = str_replace(['/', '\\', '//'], '/', $path);

        $parts = array_filter(explode('/', $path), 'strlen');
        $absolutes = [];
        foreach ($parts as $part) {
            if ('.' == $part) continue;
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        return str_replace('//', '/', '/' . implode('/', $absolutes) . '/');
    }

    /**
     * 判断列表是否含有图片
     * @param $items
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
     * 获取远程文件内容
     * @param $url
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getFileContent($url)
    {
        return self::getFileContentByUrl($url);
    }

    /**
     * 获取url文件内容
     * @param $url
     * @param bool $cache
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getFileContentByUrl($url, $cache = true)
    {
        if ($cache) {
            return Cache::remember('one:content:' . $url, self::config('expires'), function () use ($url) {
                try {
                    $client = new Client();
                    $response = $client->request('get', $url);
                    $response = $response->getBody()->getContents();
                } catch (ClientException $e) {
                    $response = response()->json(['code' => $e->getCode(), 'msg' => $e->getMessage()]);
                }
                return $response ?? '';
            });
        } else {
            return self::getFileContent($url);
        }
    }

    /**
     * 读取文件大小
     * @param $path
     * @return bool|int|string
     */
    public static function readFileSize($path)
    {
        if (!file_exists($path))
            return false;
        $size = filesize($path);
        if (!($file = fopen($path, 'rb')))
            return false;
        if ($size >= 0) { //Check if it really is a small file (< 2 GB)
            if (fseek($file, 0, SEEK_END) === 0) { //It really is a small file
                fclose($file);
                return $size;
            }
        }
        //Quickly jump the first 2 GB with fseek. After that fseek is not working on 32 bit php (it uses int internally)
        $size = PHP_INT_MAX - 1;
        if (fseek($file, PHP_INT_MAX - 1) !== 0) {
            fclose($file);
            return false;
        }
        $length = 1024 * 1024;
        $read = '';
        while (!feof($file)) { //Read the file until end
            $read = fread($file, $length);
            $size = bcadd($size, $length);
        }
        $size = bcsub($size, $length);
        $size = bcadd($size, strlen($read));
        fclose($file);
        return $size;
    }

    /**
     * 读取文件内容
     * @param $file
     * @param $offset
     * @param $length
     * @return bool|string
     */
    public static function readFileContent($file, $offset, $length)
    {
        $handler = fopen($file, "rb") ?? die('获取文件内容失败');
        fseek($handler, $offset);
        return fread($handler, $length);
    }

    /**
     * 处理格式化响应
     * @param $response JsonResponse
     * @param bool $origin
     * @return array
     */
    public static function handleResponse($response, $origin = true)
    {
        $data = json_encode($response->getData());
        if ($origin) {
            return json_decode($data, true);
        } else {
            return json_decode($data, true)['data'];
        }
    }

    /**
     * 获取指定目录下全部子目录和文件
     * @param $path
     * @return array
     */
    public static function fetchDir($path)
    {
        $arr = [];
        $arr[] = $path;
        if (!is_file($path)) {
            if (is_dir($path)) {
                $data = scandir($path);
                if (!empty($data)) {
                    foreach ($data as $value) {
                        if ($value != '.' && $value != '..') {
                            $sub_path = $path . "/" . $value;
                            $temp = self::fetchDir($sub_path);
                            $arr = array_merge($temp, $arr);
                        }
                    }
                }
            }
        }
        return $arr;
    }

}
