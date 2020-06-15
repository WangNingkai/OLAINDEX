<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Helpers;

use App\Models\ShortUrl;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Parsedown;
use Log;

class Tool
{
    /**
     * 链接动态添加参数
     * @param $url
     * @param $key
     * @param $value
     * @return string
     */
    public static function buildQueryParams($url, $key, $value)
    {
        $url = preg_replace('/(.*)(?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
        $url = substr($url, 0, -1);
        if (strpos($url, '?') === false) {
            return ($url . '?' . $key . '=' . $value);
        }
        return ($url . '&' . $key . '=' . $value);
    }

    /**
     *文件大小转换
     *
     * @param string $size 原始大小
     *
     * @return string 转换大小
     */
    public static function convertSize($size): string
    {
        $units = array(' B', ' KB', ' MB', ' GB', ' TB');
        for ($i = 0; $size >= 1024 && $i < 4; $i++) {
            $size /= 1024;
        }

        return @round($size, 2) . $units[$i];
    }

    /**
     * markdown转html
     *
     * @param      $markdown
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
     * 数组分页
     *
     * @param $items
     * @param $perPage
     *
     * @return LengthAwarePaginator
     */
    public static function paginate($items, $perPage): LengthAwarePaginator
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
     * 短网址生成
     * @param $url
     * @return mixed
     */
    public static function shortenUrl($url)
    {
        $code = shorten_url($url);
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
     * @param $code
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
     * @param $key
     * @param $path
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
     * @param $path
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
}
