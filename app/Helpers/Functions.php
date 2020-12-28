<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
if (!function_exists('is_json')) {
    /**
     * 判断字符串是否是json
     *
     * @param string $json
     * @return bool
     */
    function is_json($json)
    {
        json_decode($json, true);
        return (json_last_error() === JSON_ERROR_NONE);
    }
}
if (!function_exists('convert_size')) {
    /**
     * 转换字节为可读取数值
     * @param int $size
     * @param int $digits
     * @return string
     */
    function convert_size($size, $digits = 2): string
    {
        if ($size <= 0) {
            return '0 B';
        }
        $size = (int)$size;
        $units = [' B', ' KB', ' MB', ' GB', ' TB', 'PB'];
        $i = floor(log($size, 1024));
        return round($size / (1024 ** $i), $digits) . $units[$i];
    }
}
if (!function_exists('url_encode')) {
    /**
     * 解析路径
     *
     * @param string $path
     *
     * @return string
     */
    function url_encode($path): string
    {
        $url = [];
        foreach (explode('/', $path) as $key => $value) {
            if (empty(!$value)) {
                $url[] = rawurlencode($value);
            }
        }
        return @implode('/', $url);
    }
}
if (!function_exists('trans_request_path')) {
    /**
     * 处理请求路径
     *
     * @param string $path
     * @param bool $query
     * @param bool $isItem
     * @return string
     */
    function trans_request_path($path, $query = true, $isItem = false): string
    {
        $originPath = trans_absolute_path($path);
        $queryPath = trim($originPath, '/');
        $queryPath = url_encode(rawurldecode($queryPath));
        if (!$query) {
            return $queryPath;
        }
        $requestPath = empty($queryPath) ? '/' : ":/{$queryPath}:/";
        if ($isItem) {
            return rtrim($requestPath, ':/');
        }
        return $requestPath;
    }
}
if (!function_exists('trans_absolute_path')) {
    /**
     * 获取绝对路径
     *
     * @param string $path
     *
     * @return mixed
     */
    function trans_absolute_path($path)
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
}
if (!function_exists('parse_query')) {
    /**
     * Parse a query string into an associative array.
     *
     * If multiple values are found for the same key, the value of that key
     * value pair will become an array. This function does not parse nested
     * PHP style arrays into an associative array (e.g., foo[a]=1&foo[b]=2 will
     * be parsed into ['foo[a]' => '1', 'foo[b]' => '2']).
     *
     * @param string $str Query string to parse
     * @param int|bool $urlEncoding How the query string is encoded
     *
     * @return array
     */
    function parse_query($str, $urlEncoding = true)
    {
        $result = [];

        if ($str === '') {
            return $result;
        }

        if ($urlEncoding === true) {
            $decoder = function ($value) {
                return rawurldecode(str_replace('+', ' ', $value));
            };
        } elseif ($urlEncoding === PHP_QUERY_RFC3986) {
            $decoder = 'rawurldecode';
        } elseif ($urlEncoding === PHP_QUERY_RFC1738) {
            $decoder = 'urldecode';
        } else {
            $decoder = function ($str) {
                return $str;
            };
        }

        foreach (explode('&', $str) as $kvp) {
            $parts = explode('=', $kvp, 2);
            $key = $decoder($parts[0]);
            $value = isset($parts[1]) ? $decoder($parts[1]) : null;
            if (!isset($result[$key])) {
                $result[$key] = $value;
            } else {
                if (!is_array($result[$key])) {
                    $result[$key] = [$result[$key]];
                }
                $result[$key][] = $value;
            }
        }

        return $result;
    }
}
if (!function_exists('build_query')) {
    /**
     * Build a query string from an array of key value pairs.
     *
     * This function can use the return value of parse_query() to build a query
     * string. This function does not modify the provided keys when an array is
     * encountered (like http_build_query would).
     *
     * @param array $params Query string parameters.
     * @param int|false $encoding Set to false to not encode, PHP_QUERY_RFC3986
     *                            to encode using RFC3986, or PHP_QUERY_RFC1738
     *                            to encode using RFC1738.
     * @return string
     */
    function build_query(array $params, $encoding = PHP_QUERY_RFC3986)
    {
        if (!$params) {
            return '';
        }

        if ($encoding === false) {
            $encoder = function ($str) {
                return $str;
            };
        } elseif ($encoding === PHP_QUERY_RFC3986) {
            $encoder = 'rawurlencode';
        } elseif ($encoding === PHP_QUERY_RFC1738) {
            $encoder = 'urlencode';
        } else {
            throw new \InvalidArgumentException('Invalid type');
        }

        $qs = '';
        foreach ($params as $k => $v) {
            $k = $encoder($k);
            if (!is_array($v)) {
                $qs .= $k;
                if ($v !== null) {
                    $qs .= '=' . $encoder($v);
                }
                $qs .= '&';
            } else {
                foreach ($v as $vv) {
                    $qs .= $k;
                    if ($vv !== null) {
                        $qs .= '=' . $encoder($vv);
                    }
                    $qs .= '&';
                }
            }
        }

        return $qs ? (string)substr($qs, 0, -1) : '';
    }
}


if (!function_exists('setting')) {
    /**
     * 获取设置
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting($key = '', $default = null)
    {
        $setting = \Cache::remember('settings', 60 * 60 * 2, static function () {
            try {
                $setting = \App\Models\Setting::all();
            } catch (Exception $e) {
                return [];
            }
            $settingData = [];
            foreach ($setting as $detail) {
                $settingData = array_add($settingData, $detail->name, $detail->value);
            }
            return $settingData;
        });
        if ($default === null) {
            $default = \App\Models\Setting::$setting[$key] ?? '';
        }
        $setting = collect($setting)->all();
        return $key ? array_get($setting, $key, $default) : $setting;
    }
}
if (!function_exists('setting_set')) {
    /**
     * 更新设置
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    function setting_set($key = '', $value = '')
    {
        if (!is_array($key)) {
            $value = is_array($value) ? json_encode($value) : $value;
            \App\Models\Setting::query()->updateOrCreate(['name' => $key], ['value' => $value]);
        } else {
            foreach ($key as $k => $v) {
                $v = is_array($v) ? json_encode($v) : $v;
                \App\Models\Setting::query()->updateOrCreate(['name' => $k], ['value' => $v]);
            }
        }

        return refresh_setting();
    }
}
if (!function_exists('refresh_setting')) {
    /**
     * 刷新设置缓存
     * @return array
     */
    function refresh_setting()
    {
        $settingData = [];
        try {
            $settingModel = \App\Models\Setting::all();
        } catch (Exception $e) {
            $settingModel = [];
        }
        foreach ($settingModel->toArray() as $detail) {
            $settingData[$detail['name']] = $detail['value'];
        }

        \Cache::forever('settings', $settingData);

        return collect($settingData)->toArray();
    }
}
if (!function_exists('install_path')) {
    /**
     * 安装路径
     * @param string $path
     * @return string
     */
    function install_path($path = '')
    {
        return storage_path('install' . ($path ? DIRECTORY_SEPARATOR . $path : $path));
    }
}
if (!function_exists('shorten_str')) {
    /**
     * 获取短链
     * @param string $url
     * @return mixed
     */
    function shorten_str($url)
    {
        $shortenList = [];
        $charset = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        $key = "olaindex666";
        $urlHash = md5($key . $url);
        $len = strlen($urlHash);
        #将加密后的串分成4段，每段4字节，对每段进行计算，一共可以生成四组短连接
        for ($i = 0; $i < 4; $i++) {
            $urlHashPiece = substr($urlHash, $i * $len / 4, $len / 4);
            #将分段的位与0x3fffffff做位与，0x3fffffff表示二进制数的30个1，即30位以后的加密串都归零
            $hex = hexdec($urlHashPiece) & 0x3fffffff; #此处需要用到hexdec()将16进制字符串转为10进制数值型，否则运算会不正常
            $shortenUrl = '';
            #生成6位短连接
            for ($j = 0; $j < 6; $j++) {
                #将得到的值与0x0000003d,3d为61，即charset的坐标最大值
                $shortenUrl .= $charset[$hex & 0x0000003d];
                #循环完以后将hex右移5位
                $hex >>= 5;
            }
            $shortenList[] = $shortenUrl;
        }
        return array_first($shortenList);
    }
}
if (!function_exists('shorten_url')) {
    /**
     * 获取短链
     * @param string $url
     * @return mixed
     */
    function shorten_url($url)
    {
        if (!setting('open_short_url', 1)) {
            return $url;
        }
        $code = shorten_str($url);
        $data = \App\Models\ShortUrl::query()->select('id', 'original_url', 'short_code')->where(['short_code' => $code])->first();
        if (!$data) {
            $new = new \App\Models\ShortUrl();
            $new->short_code = $code;
            $new->original_url = $url;
            $new->save();
        }
        return route('short', ['code' => $code]);
    }
}
if (!function_exists('marked')) {
    /**
     * 转换markdown
     * @param string $text
     * @return mixed
     */
    function marked($text)
    {
        return Parsedown::instance()->text($text);
    }
}
