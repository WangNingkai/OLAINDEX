<?php


namespace App\Utils;

use App\Models\Setting;
use App\Service\CoreConstants;
use App\Service\OneDrive;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Curl\Curl;
use Session;
use Cache;
use Parsedown;
use Log;
use ErrorException;

class Tool
{
    /**
     * 操作成功或者失败的提示
     *
     * @param string $message
     * @param bool $success
     */
    public static function showMessage($message = '成功', $success = true): void
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
     * 判断密钥配置
     *
     * @return bool
     */
    public static function hasConfig(): bool
    {
        return setting('client_id') && setting('client_secret') && setting('redirect_uri');
    }

    /**
     * 判断账号绑定
     *
     * @return bool
     */
    public static function hasBind(): bool
    {
        return setting('access_token') && setting('refresh_token') && setting('access_token_expires');
    }

    /**
     * 判断资源列表是否含有图片
     *
     * @param $items
     *
     * @return bool
     */
    public static function hasImages($items): bool
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
     * 获取排序状态
     *
     * @param $field
     * @return bool
     */
    public static function getOrderByStatus($field): bool
    {
        $order = request()->get('orderBy');
        @list($search_field, $sortBy) = explode(',', $order);
        if ($field !== $search_field) {
            return true;
        }
        return strtolower($sortBy) === 'desc';
    }

    /**
     * 获取包屑导航url
     *
     * @param $key
     * @param $pathArr
     *
     * @return string
     */
    public static function getBreadcrumbUrl($key, $pathArr): string
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
     *
     * @param $pathArr
     *
     * @return string
     */
    public static function getParentUrl($pathArr): string
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

    /**
     * 获取扩展图标
     * @param string $ext
     * @param bool $img
     *
     * @return string
     */
    public static function getExtIcon($ext = '', $img = false): string
    {
        $patterns = Extension::FILE_ICON;
        $icon = '';
        foreach ($patterns as $key => $suffix) {
            if (in_array($ext, $suffix[2], false)) {
                $icon = $img ? $suffix[1] : $suffix[0];
                break;
            }
            $icon = $img ? 'file' : 'fa-file-text-o';
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
    public static function canEdit($file): bool
    {
        $code = explode(' ', setting('code'));
        $stream = explode(' ', setting('stream'));
        $canEditExt = array_merge($code, $stream);
        if (!isset($file['ext'])) {
            return false;
        }
        $isText = in_array($file['ext'], $canEditExt, false);
        $isBigFile = $file['size'] > 5 * 1024 * 1024 ?: false;

        return !$isBigFile && $isText;
    }

    /**
     * 处理url
     *
     * @param $path
     *
     * @return string
     */
    public static function encodeUrl($path): string
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
     * 获取初始文件路径
     *
     * @param      $path
     * @param bool $isQuery
     *
     * @return string
     */
    public static function getOriginPath($path, $isQuery = true): string
    {
        $path = self::getAbsolutePath($path);
        $query_path = trim($path, '/');
        if (!$isQuery) {
            return $query_path;
        }
        $query_path = self::encodeUrl(rawurldecode($query_path));
        $root = trim(self::encodeUrl(setting('root')), '/');
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
     * @param $path
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

        return str_replace('//', '/', '/' . implode('/', $absolutes) . '/');
    }

    /**
     * 刷新账户信息
     * @param $account
     * @throws ErrorException
     */
    public static function refreshAccount($account): void
    {
        $response = OneDrive::getInstance($account)->getAccountInfo();
        if ($response['errno'] === 0) {
            $extend = Arr::get($response, 'data');
            $account_email = $response['errno'] === 0 ? Arr::get($extend, 'userPrincipalName') : '';
            $data = [
                'account_email' => $account_email,
                'account_state' => '正常',
            ];
            $resp = OneDrive::getInstance($account)->getDriveInfo();
            if ($resp['errno'] === 0) {
                $extend = Arr::get($resp, 'data');
                $data['account_extend'] = $extend;
            }
        } else {
            $data = [
                'account_state' => '账号异常',
            ];
        }
        Setting::batchUpdate($data);
    }

    /**
     * 获取远程文件内容
     *
     * @param $url
     * @param string|bool $cache
     * @return mixed|string|null
     * @throws ErrorException
     */
    public static function getFileContent($url, $cache = '')
    {
        if ($cache) {
            $key = 'one:content:' . $cache;
            $content = Cache::get($key, '');
            if ($content) {
                return $content;
            }
        }
        $curl = new Curl();
        $curl->setConnectTimeout(CoreConstants::DEFAULT_CONNECT_TIMEOUT);
        $curl->setTimeout(CoreConstants::DEFAULT_TIMEOUT);
        $curl->setRetry(CoreConstants::DEFAULT_RETRY);
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
                'Get Remote file content error.',
                [
                    'code' => $curl->errorCode,
                    'msg' => $curl->errorMessage,
                ]
            );
            self::showMessage('Error: ' . $curl->errorCode . ': ' . $curl->errorMessage, false);

            return '远程获取内容失败，请刷新重试';
        }
        $content = $curl->rawResponse;
        if ($cache) {
            Cache::put($key, $content, setting('expires'));
        }

        return $content;
    }

    /**
     * 解析加密目录
     *
     * @param $str
     *
     * @return array
     */
    public static function handleEncryptItem($str): array
    {
        $str = str_replace(PHP_EOL, '', $str);
        $str = trim($str, '|');
        $encryptPathArr = explode('|', $str);
        $all = [];
        foreach ($encryptPathArr as $encryptPathDir) {
            @list($pathItem, $password) = explode(':', $encryptPathDir);
            $pathItem = explode(',', $pathItem);
            $pathItem = array_map(static function ($value) {
                return 'p>' . $value;
            }, $pathItem);
            $pathArray = array_fill_keys($pathItem, $password);
            $all = collect($all)->merge($pathArray)->toArray();
        }
        uksort($all, [self::class, 'lenSort']);
        return $all;
    }

    /**
     * 解析隐藏目录
     *
     * @param $str
     * @return array
     */
    public static function handleHideItem($str): array
    {
        $str = str_replace(PHP_EOL, '', $str);
        $str = trim($str, '|');
        $hidePathArr = explode('|', $str);
        $hidePathArr = array_map(static function ($value) {
            return trim($value, '/');
        }, $hidePathArr);
        uksort($hidePathArr, [self::class, 'lenSort']);
        return $hidePathArr;
    }

    /**
     * @param $a
     * @param $b
     *
     * @return int
     */
    public static function lenSort($a, $b): int
    {
        $countA = count(explode('/', self::getAbsolutePath($a)));
        $countB = count(explode('/', self::getAbsolutePath($b)));
        return $countB - $countA;
    }
}
