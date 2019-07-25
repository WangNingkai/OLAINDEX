<?php

use Curl\Curl;
use App\Helpers\Tool;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Helpers\Constants;
use App\Models\OneDrive;
use Illuminate\Support\Arr;
use App\Models\Admin;

if (!function_exists('convertSize')) {
    /**
     *文件大小转换
     *
     * @param string $size 原始大小
     *
     * @return string 转换大小
     */
    function convertSize($size)
    {
        $units = [' B', ' KB', ' MB', ' GB', ' TB'];
        for ($i = 0; $size >= 1024 && $i < 4; $i++) {
            $size /= 1024;
        }

        return @round($size, 2) . $units[$i];
    }
}

if (!function_exists('getBreadcrumbUrl')) {
    /**
     * 获取包屑导航url
     *
     * @param $key
     * @param $pathArr
     *
     * @return string
     */
    function getBreadcrumbUrl($key, $pathArr)
    {
        $pathArr = array_slice($pathArr, 0, $key);
        $url = '';
        foreach ($pathArr as $param) {
            $url .= '/' . $param;
        }

        return trim($url, '/');
    }
}

if (!function_exists('getParentUrl')) {
    /**
     * 获取父级url
     *
     * @param $pathArr
     *
     * @return string
     */
    function getParentUrl($pathArr)
    {
        array_pop($pathArr);
        if (count($pathArr) === 0) {
            return '';
        }
        $url = '';
        foreach ($pathArr as $param) {
            $url .= '/' . $param;
        }

        return trim($url, '/');
    }
}

if (!function_exists('markdown2Html')) {
    /**
     * markdown转html
     *
     * @param      $markdown
     * @param bool $line
     *
     * @return mixed|string
     */
    function markdown2Html($markdown, $line = false)
    {
        $parser = new \Parsedown();
        if (!$line) {
            $html = $parser->text($markdown);
        } else {
            $html = $parser->line($markdown);
        }

        return $html;
    }
}

if (!function_exists('getFileContent')) {
    /**
     * @param      $url
     * @param bool $cache
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \ErrorException
     */
    function getFileContent($url, $cache = true)
    {
        $key = 'one_' . app('onedrive')->id . ':content:' . $url;
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
            Tool::showMessage('Error: ' . $curl->errorCode . ': '
                . $curl->errorMessage, false);

            return '远程获取内容失败，请刷新重试';
        } else {
            $content = $curl->rawResponse;
            if ($cache) {
                Cache::put(
                    $key,
                    $content,
                    app('onedrive')->expires
                );
            }

            return $content;
        }
    }
}

if (!function_exists('getExtIcon')) {
    /**
     * @param string $ext
     * @param bool $img
     *
     * @return string
     */
    function getExtIcon($ext = '', $img = false)
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
}

if (!function_exists('fileIcon')) {
    /**
     * @param $ext
     *
     * @return string
     */
    function fileIcon($ext)
    {
        if (in_array($ext, ['ogg', 'mp3', 'wav'])) {
            return 'audiotrack';
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
            return 'image';
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
            return 'ondemand_video';
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

        return 'insert_drive_file';
    }
}

if (!function_exists('getOrderByStatus')) {
    function getOrderByStatus($field)
    {
        $search_field = request()->get('by');
        $sort = request()->get('sort');

        if ($field !== $search_field) {
            return true;
        } else {
            if (strtolower($sort) !== 'desc') {
                return false;
            } else {
                return true;
            }
        }

        return true;
    }
}

/**
 * 选择默认的OneDrive
 */
if (!function_exists('getDefaultOneDriveAccount')) {
    function getDefaultOneDriveAccount($one_drive_id = null)
    {
        if (app()->bound('onedrive')) {
            if (!empty($one_drive_id) && app('onedrive')->id == $one_drive_id) {
                return;
            }

            if (empty($one_drive_id) && app('onedrive')->is_default) {
                return;
            }
        }

        $key = 'instance:onedrive_' . $one_drive_id ?? 0;
        if (Cache::has($key)) {
            app()->instance('onedrive', Cache::get($key));
            return;
        }

        if (empty($one_drive_id)) {
            $oneDrive = OneDrive::where('is_default', true)->firstOrFail();
        } else {
            $oneDrive = OneDrive::findOrFail($one_drive_id);
        }

        $oneDrive->authorize_url = $oneDrive->account_type == 'com'
            ? Constants::AUTHORITY_URL . Constants::AUTHORIZE_ENDPOINT
            : Constants::AUTHORITY_URL_21V . Constants::AUTHORIZE_ENDPOINT_21V;
        $oneDrive->access_token_url = $oneDrive->account_type == 'com'
            ? Constants::AUTHORITY_URL . Constants::TOKEN_ENDPOINT
            : Constants::AUTHORITY_URL_21V . Constants::TOKEN_ENDPOINT_21V;
        $oneDrive->scopes = Constants::SCOPES;

        Cache::put($key, $oneDrive, now()->addDay());

        app()->instance('onedrive', $oneDrive);
    }
}

if (!function_exists('success')) {
    function success($message = '操作成功', $status = 302, $headers = [], $fallback = false)
    {
        return app('redirect')->back($status, $headers, $fallback)->with('message', $message);
    }
}

if (!function_exists('redirectSuccess')) {
    function redirectSuccess($route, $parameters = [], $status = 302, $headers = [], $message = '操作成功')
    {
        return app('redirect')->route($route, $parameters, $status, $headers)->with('message', $message);
    }
}

/**
 * 返回主题的view路径
 */
if (!function_exists('themeView')) {
    function themeView($view, $data = [])
    {
        return view(config('olaindex.theme') . '.' . $view, $data);
    }
}

if (!function_exists('getAdminConfig')) {
    function getAdminConfig($key = '')
    {
        $admin = Cache::rememberForever('admin_settings', function () {
            return Admin::firstOrFail();
        });
        
        if (!empty($admin)) {
            return $admin->$key;
        } else {
            return Arr::get(config('olaindex.admin'), $key, '暂无数据');
        }
    }
}

if (!function_exists('route_parameter')) {
    /**
     * Get a given parameter from the route.
     *
     * @param $name
     * @param null $default
     * @return mixed
     */
    function route_parameter($name, $default = null)
    {
        $routeInfo = app('request')->route();

        return Arr::get($routeInfo->parameters, $name, $default);
    }
}
