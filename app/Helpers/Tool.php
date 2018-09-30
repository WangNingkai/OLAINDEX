<?php

namespace App\Helpers;

use App\Models\Parameter;
use HyperDown\Parser;
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
    * @param string $size  原始大小
    * @return string 转换大小
    */
    public static function convertSize($size)
    {
        $units = array(' B', ' KB', ' MB', ' GB', ' TB');
        for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
        return @round($size, 2).$units[$i];
    }

    /**
     * 获取包屑导航栏路径
     * @param $key
     * @param $pathArr
     * @return string
     */
    public static function getUrl($key,$pathArr)
    {
        array_pop($pathArr);
        // 兼容目录下就是文件的情况
        if (count($pathArr) == 1) {
            $key = $key - 1;
        }
        $pathArr = array_slice($pathArr,0,$key);
        $url= '';
        foreach ($pathArr as $param) {
            $url .= '-'.$param;
        }
        return trim($url,'-');
    }

    /**
     * 获取上一级 Url
     * @param $pathArr
     * @return string
     */
    public static function getParentUrl($pathArr)
    {
        array_pop($pathArr);
        if (count($pathArr) == 0)
        {
            return '';
        }
        $url= '';
        foreach ($pathArr as $param) {
            $url .= '-'.$param;
        }
        return trim($url,'-');
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
    public static function config($key = '',$default = '')
    {
        // 读取配置缓存
        $config = Cache::remember('config', 1440, function () {
            return Parameter::query()->pluck('value', 'name')->toArray();
        });
        return $key ? ($config[$key] ?: $default) : $config;
    }

    /**
     * 获取文件图片
     * @param $ext
     * @return string
     */
    public static function getExtIcon($ext)
    {
        $patterns = [
            'stream'=>['fa-file-text-o',['txt','log']],
            'image' => ['fa-file-image-o',['bmp','jpg','jpeg','png','gif']],
            'video' => ['fa-file-video-o',['mkv','mp4']],
            'audio' => ['fa-file-audio-o',['mp3']],
            'code' => ['fa-file-code-o',['html','htm', 'css', 'go','java','js','json','txt','sh','md']],
            'doc' => ['fa-file-word-o',['csv','doc','docx','odp','ods','odt','pot','potm','potx','pps','ppsx','ppsxm','ppt','pptm','pptx','rtf','xls','xlsx']],
            'pdf' => ['fa-file-pdf-o',['pdf']],
            'zip' => ['fa-file-archive-o',['zip','7z','rar','bz','gz']],
            'android' => ['fa-android',['apk']],
            'exe' => ['fa-windows',['exe','msi']],
        ];
        $icon = '';
        foreach ($patterns as $key => $suffix) {
            if(in_array($ext,$suffix[1])){
                $icon = $patterns[$key][0];
                break;
            } else {
                $icon = 'fa-file-text-o';
            }
        }
        return $icon;
    }

    /**
     * 加解密
     * @param $string
     * @param $operation
     * @param string $key
     * @return bool|mixed|string
     */
    public static function  encrypt($string,$operation,$key=''){
        $key = md5($key);
        $key_length = strlen($key);
        $string=$operation == 'D' ? base64_decode($string) : substr(md5($string.$key),0,8).$string;
        $string_length = strlen($string);
        $randKey = [];
        $box = [];
        $result = '';
        for($i=0; $i<=255; $i++){
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
}
