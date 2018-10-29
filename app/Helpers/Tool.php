<?php

namespace App\Helpers;

use App\Models\Parameter;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
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
        $parser = new \Parsedown();
        $html = $parser->text($markdown);
        $html = str_replace('<code class="', '<code class="lang-', $html);
        // 将临时字符串替换为 i_frame
        if (!empty($iframe[0])) {
            $html = str_replace($tmp, $replace, $html);
        }
        return $html;
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
     * 是否可编辑
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

    /**
     * 保存配置到json文件
     * @param $config
     * @return bool
     */
    public static function saveConfig($config)
    {
        $file = storage_path('app/config.json');
        if (!is_writable($file)) {
            self::showMessage('权限不足，无法写入配置文件');
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
                self::showMessage('权限不足，无法预取配置文件');
                abort(403, '权限不足，无法预取配置文件');
            };
            $config = file_get_contents($file);
            return json_decode($config, true);
        });
        return $key ? (array_key_exists($key, $config) ? ($config[$key] ?: $default) : $default) : $config;
    }

}
