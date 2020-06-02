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
     * @param $json
     * @return bool
     */
    function is_json($json)
    {
        json_decode($json, true);
        return (json_last_error() === JSON_ERROR_NONE);
    }
}
if (!function_exists('url_encode')) {
    /**
     * 解析路径
     *
     * @param $path
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
     * @param $path
     * @param bool $query
     * @param bool $isFile
     * @return string
     */
    function trans_request_path($path, $query = true, $isFile = false): string
    {
        $originPath = trans_absolute_path($path);
        $queryPath = trim($originPath, '/');
        $queryPath = url_encode(rawurldecode($queryPath));
        if (!$query) {
            return $queryPath;
        }
        $requestPath = empty($queryPath) ? '/' : ":/{$queryPath}:/";
        if ($isFile) {
            return rtrim($requestPath, ':/');
        }
        return $requestPath;
    }
}
if (!function_exists('trans_absolute_path')) {
    /**
     * 获取绝对路径
     *
     * @param $path
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
if (!function_exists('setting')) {
    /**
     * 获取设置
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
    function setting($key = '', $default = '')
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
        $setting = collect($setting)->all();
        return $key ? array_get($setting, $key, $default) : $setting;
    }
}
if (!function_exists('setting_set')) {
    /**
     * 更新设置
     * @param mixed $key
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
