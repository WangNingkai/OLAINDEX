<?php

namespace App\Helpers;

use App\Http\Controllers\FetchController;
use App\Models\Parameter;
use HyperDown\Parser;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
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
     * 获取包屑导航栏路径
     * @param $key
     * @param $pathArr
     * @return string
     */
    public static function getUrl($key, $pathArr)
    {
        $pathArr = array_slice($pathArr, 0, $key);
        $url = '';
        foreach ($pathArr as $param) {
            $url .= '/' . $param;
        }
        return trim($url, '/');
    }

    /**
     * 获取上一级 Url
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

    public static function getOriginPath($url)
    {
        $root = self::config('root');
    }

    /**
     * 字符串截取，支持中文和其他编码
     *
     * @param string $str 需要转换的字符串
     * @param integer $start 开始位置
     * @param string $length 截取长度
     * @param boolean $suffix 截断显示字符
     * @param string $charset 编码格式
     * @return string
     */
    public static function subStr($str, $start, $length, $suffix = true, $charset = "utf-8")
    {
        $slice = mb_substr($str, $start, $length, $charset);
        $omit = mb_strlen($str) >= $length ? '...' : '';
        return $suffix ? $slice . $omit : $slice;
    }

    /**
     * markdown 转 html
     *
     * @param string $markdown
     * @return array
     */
    public static function markdown2Html($markdown)
    {
        preg_match_all('/&lt;iframe.*iframe&gt;/', $markdown, $iframe);
        // 如果有 i_frame 则先替换为临时字符串
        if (!empty($iframe[0])) {
            $tmp = [];
            // 组合临时字符串
            foreach ($iframe[0] as $k => $v) {
                $tmp[] = '【iframe' . $k . '】';
            }
            // 替换临时字符串
            $markdown = str_replace($iframe[0], $tmp, $markdown);
            // 转义 i_frame
            $replace = array_map(function ($v) {
                return htmlspecialchars_decode($v);
            }, $iframe[0]);
        }
        // markdown转html
        $parser = new Parser();
        $html = $parser->makeHtml($markdown);
        $html = str_replace('<code class="', '<code class="lang-', $html);
        // 将临时字符串替换为 i_frame
        if (!empty($iframe[0])) {
            $html = str_replace($tmp, $replace, $html);
        }
        return $html;
    }

    /**
     * 读取配置
     * @param string $key
     * @param string $default
     * @return mixed|string
     */
    public static function config($key = '', $default = '')
    {
        // 读取配置缓存
        $config = Cache::remember('config', 1440, function () {
            return Parameter::query()->pluck('value', 'name')->toArray();
        });
        return $key ? (array_key_exists($key, $config) ? ($config[$key] ?: $default) : $default) : $config;
    }

    /**
     * 获取文件图片
     * @param $ext
     * @return string
     */
    public static function getExtIcon($ext)
    {
        $patterns = Constants::ICON;
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
     * 获取文件后缀
     * @param $mimeType
     * @return int|string
     */
    public static function getExt($mimeType)
    {
        $patterns = Constants::EXT;
        $suffix = '';
        foreach ($patterns as $ext => $mime) {
            if ($mimeType == $mime) {
                $suffix = $ext;
                break;
            } else {
                $suffix = 'unknown';
            }
        }
        return $suffix;
    }

    /**
     * 加解密
     * @param $string
     * @param $operation
     * @param string $key
     * @return bool|mixed|string
     */
    public static function encrypt($string, $operation, $key = '')
    {
        $key = md5($key);
        $key_length = strlen($key);
        $string = $operation == 'D' ? base64_decode($string) : substr(md5($string . $key), 0, 8) . $string;
        $string_length = strlen($string);
        $randKey = [];
        $box = [];
        $result = '';
        for ($i = 0; $i <= 255; $i++) {
            $randKey[$i] = ord($key[$i % $key_length]);
            $box[$i] = $i;
        }

        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $randKey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if ($operation == 'D') {
            if (substr($result, 0, 8) == substr(md5(substr($result, 8) . $key), 0, 8)) {
                return substr($result, 8);
            } else {
                return '';
            }
        } else {
            return str_replace('=', '', base64_encode($result));
        }
    }

    /**
     * 数组分页
     * @param $data
     * @param $path
     * @param int $perPage
     * @return mixed
     */
    public static function arrayPage($data, $path, $perPage = 10)
    {
        //获取当前的分页数
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        //实例化collect方法
        $collection = new Collection($data);
        //定义一下每页显示多少个数据
//        $perPage = 5;
        //获取当前需要显示的数据列表$currentPage * $perPage
        $currentPageDataResults = $collection->slice(($currentPage - 1) * $perPage, $perPage)->all();
        //创建一个新的分页方法
        $paginatedDataResults = new LengthAwarePaginator($currentPageDataResults, count($collection), $perPage);
        //给分页加自定义url
        $paginatedDataResults = $paginatedDataResults->setPath($path);
        return $paginatedDataResults;
    }

    /**
     * @param $file
     * @return bool
     */
    public static function isEdited($file)
    {
        $code = explode(' ', self::config('code'));
        $stream = explode(' ', self::config('stream'));
        $exts = array_merge($code, $stream);
        $isText = in_array($file['ext'], $exts);
        $isBigFile = $file['size'] > 5 * 1024 * 1024 ?: false;
        if (!$isBigFile && $isText) {
            return true;
        } else {
            return false;
        }
    }

    public static function id2Path($id)
    {
        $fetch = new FetchController();
        $file = $fetch->getFileById($id);
        $path = $file['parentReference']['path'];
        $root = self::config('root','/');
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
        return trim(implode('/',$pathArr),'/');
    }
}
