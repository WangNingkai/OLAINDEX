<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Helpers;

use App\Models\ShortUrl;
use App\Models\Account;
use Curl\Curl;
use Parsedown;
use Log;
use Cache;

class Tool
{
    /**
     * 链接动态添加参数
     * @param string $url
     * @param string $key
     * @param string $value
     * @return string
     */
    public static function buildQueryParams(string $url, $key, $value): string
    {
        $url = urldecode($url);
        $parseArr = parse_url($url);
        $queryArr = [];
        if (isset($parseArr['query'])) {
            $queryArr = parse_query($parseArr['query']);
        }
        $queryArr[$key] = $value;
        $query = '?' . build_query($queryArr, false);
        if (strpos($url, '?') === false) {
            return $url . $query;
        }
        $base = str_before($url, '?');
        return $base . $query;

    }

    /**
     * markdown转html
     *
     * @param string $markdown
     * @param bool $line
     *
     * @return string
     */
    public static function markdown2Html($markdown, $line = false): string
    {
        $parser = new Parsedown();
        if (!$line) {
            $html = $parser->text($markdown);
        } else {
            $html = $parser->line($markdown);
        }

        return $html;
    }

    /**
     * 短网址生成
     * @param string $url
     * @return mixed
     */
    public static function shortenUrl($url)
    {
        $code = shorten_str($url);
        $data = ShortUrl::query()->select('id', 'original_url', 'short_code')->where(['short_code' => $code])->first();
        if (!$data) {
            $new = new ShortUrl();
            $new->short_code = $code;
            $new->original_url = $url;
            $new->save();
        }
        return route('short', ['code' => $code]);
    }

    /**
     * 短网址解析
     * @param string $code
     * @return \Illuminate\Database\Eloquent\HigherOrderBuilderProxy|mixed|string
     */
    public static function decodeShortUrl($code)
    {
        $url = ShortUrl::query()->select('id', 'original_url', 'short_code')->where(['short_code' => $code])->first();
        if (!$url) {
            return '';
        }
        return $url->original_url;
    }

    /**
     * 面包屑导航
     * @param string $key
     * @param string $path
     * @return string
     */
    public static function combineBreadcrumb($key, $path): string
    {
        $path = array_slice($path, 0, $key);
        $url = '';
        foreach ($path as $param) {
            $url .= '/' . $param;
        }

        return trim($url, '/');
    }

    /**
     * 面包屑返回上一级
     * @param string $path
     * @return string
     */
    public static function fetchGoBack($path): string
    {
        array_pop($path);
        if (count($path) === 0) {
            return '';
        }
        $url = '';
        foreach ($path as $param) {
            $url .= '/' . $param;
        }

        return trim($url, '/');
    }

    /**
     * 获取图标
     * @param string $ext
     * @return mixed|string
     */
    public static function fetchExtIco($ext)
    {
        $patterns = [
            'stream' => ['file-text', ['txt', 'log']],
            'image' => ['image', ['bmp', 'jpg', 'jpeg', 'png', 'gif', 'ico', 'jpe']],
            'video' => ['video', ['mkv', 'mp4', 'webm', 'avi', 'mpg', 'mpeg', 'rm', 'rmvb', 'mov', 'wmv', 'asf', 'ts', 'flv',]],
            'audio' => ['file-music', ['ogg', 'mp3', 'wav']],
            'code' => ['file-code', ['html', 'htm', 'css', 'go', 'java', 'js', 'json', 'txt', 'sh', 'md', 'php',]],
            'doc' => ['file-word', ['csv', 'doc', 'docx', 'odp', 'ods', 'odt', 'pot', 'potm', 'potx', 'pps', 'ppsx', 'ppsxm', 'ppt', 'pptm', 'pptx', 'rtf', 'xls', 'xlsx',]],
            'pdf' => ['pdf', ['pdf']],
            'zip' => ['file-zip', ['zip', '7z', 'rar', 'bz', 'gz']],
            'android' => ['android', ['apk']],
            'exe' => ['apps', ['exe', 'msi']],
            'folder' => ['folder', ['folder']],
        ];
        $icon = 'file';
        foreach ($patterns as $key => $suffix) {
            if (in_array($ext, $suffix[1], false)) {
                $icon = $suffix[0];
                break;
            }
        }

        return $icon;
    }

    /**
     * 获取文件流类型
     * @param string $ext
     * @return mixed|string
     */
    public static function fetchFileType($ext)
    {
        $map = [
            'file' => 'application/octet-stream',
            'chm' => 'application/octet-stream',
            'ppt' => 'application/vnd.ms-powerpoint',
            'xls' => 'application/vnd.ms-excel',
            'doc' => 'application/msword',
            'exe' => 'application/octet-stream',
            'rar' => 'application/octet-stream',
            'js' => 'javascript/js',
            'css' => 'text/css',
            'hqx' => 'application/mac-binhex40',
            'bin' => 'application/octet-stream',
            'oda' => 'application/oda',
            'pdf' => 'application/pdf',
            'ai' => 'application/postsrcipt',
            'eps' => 'application/postsrcipt',
            'es' => 'application/postsrcipt',
            'rtf' => 'application/rtf',
            'mif' => 'application/x-mif',
            'csh' => 'application/x-csh',
            'dvi' => 'application/x-dvi',
            'hdf' => 'application/x-hdf',
            'nc' => 'application/x-netcdf',
            'cdf' => 'application/x-netcdf',
            'latex' => 'application/x-latex',
            'ts' => 'application/x-troll-ts',
            'src' => 'application/x-wais-source',
            'zip' => 'application/zip',
            'bcpio' => 'application/x-bcpio',
            'cpio' => 'application/x-cpio',
            'gtar' => 'application/x-gtar',
            'shar' => 'application/x-shar',
            'sv4cpio' => 'application/x-sv4cpio',
            'sv4crc' => 'application/x-sv4crc',
            'tar' => 'application/x-tar',
            'ustar' => 'application/x-ustar',
            'man' => 'application/x-troff-man',
            'sh' => 'application/x-sh',
            'tcl' => 'application/x-tcl',
            'tex' => 'application/x-tex',
            'texi' => 'application/x-texinfo',
            'texinfo' => 'application/x-texinfo',
            't' => 'application/x-troff',
            'tr' => 'application/x-troff',
            'roff' => 'application/x-troff',
            'shar' => 'application/x-shar',
            'me' => 'application/x-troll-me',
            'ts' => 'application/x-troll-ts',
            'gif' => 'image/gif',
            'jpeg' => 'image/pjpeg',
            'jpg' => 'image/pjpeg',
            'jpe' => 'image/pjpeg',
            'ras' => 'image/x-cmu-raster',
            'pbm' => 'image/x-portable-bitmap',
            'ppm' => 'image/x-portable-pixmap',
            'xbm' => 'image/x-xbitmap',
            'xwd' => 'image/x-xwindowdump',
            'ief' => 'image/ief',
            'tif' => 'image/tiff',
            'tiff' => 'image/tiff',
            'pnm' => 'image/x-portable-anymap',
            'pgm' => 'image/x-portable-graymap',
            'rgb' => 'image/x-rgb',
            'xpm' => 'image/x-xpixmap',
            'txt' => 'text/plain',
            'c' => 'text/plain',
            'cc' => 'text/plain',
            'h' => 'text/plain',
            'html' => 'text/html',
            'htm' => 'text/html',
            'htl' => 'text/html',
            'txt' => 'text/html',
            'php' => 'text/html',
            'rtx' => 'text/richtext',
            'etx' => 'text/x-setext',
            'tsv' => 'text/tab-separated-values',
            'mpeg' => 'video/mpeg',
            'mpg' => 'video/mpeg',
            'mpe' => 'video/mpeg',
            'avi' => 'video/x-msvideo',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
            'moov' => 'video/quicktime',
            'movie' => 'video/x-sgi-movie',
            'au' => 'audio/basic',
            'snd' => 'audio/basic',
            'wav' => 'audio/x-wav',
            'aif' => 'audio/x-aiff',
            'aiff' => 'audio/x-aiff',
            'aifc' => 'audio/x-aiff',
            'swf' => 'application/x-shockwave-flash',
            'myz' => 'application/myz',
        ];
        return array_get($map, $ext, 'application/octet-stream');
    }

    /**
     * 获取远程文件内容
     * @param string $url
     * @return string|null
     * @throws \Exception
     */
    public static function fetchContent($url)
    {
        $curl = new Curl();
        $curl->setConnectTimeout(5);
        $curl->setTimeout(3);
        $curl->setRetry(3);
        $curl->setOpts([
            CURLOPT_AUTOREFERER => true,
            CURLOPT_FAILONERROR => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => 'gzip,deflate',
        ]);
        $curl->get($url);
        $curl->close();
        if ($curl->error) {
            Log::error(
                '获取远程文件内容失败',
                [
                    'code' => $curl->errorCode,
                    'msg' => $curl->errorMessage,
                ]
            );
            throw new \Exception($curl->errorMessage, $curl->errorCode);
        }
        return $curl->rawResponse;
    }

    /**
     * 获取排序
     * @param $field
     * @return bool
     */
    public static function getOrderByStatus($field): bool
    {
        $order = request()->get('sortBy');
        if ($order) {
            [$column, $sortBy] = explode(',', $order);
            if ($field !== $column) {
                return true;
            }
            return strtolower($sortBy) === 'desc';
        }
        return false;

    }
}
