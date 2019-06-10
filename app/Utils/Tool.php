<?php


namespace App\Utils;

use App\Models\Setting;
use App\Service\OneDrive;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Session;
use Parsedown;

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
     * @throws \ErrorException
     */
    public static function refreshAccount($account): void
    {
        $response = OneDrive::getInstance($account)->getDriveInfo();
        if ($response['errno'] === 0) {
            $extend = Arr::get($response, 'data');
            $account_email = Arr::get($extend, 'owner.user.email', '');
            $data = [
                'account_email' => $account_email,
                'account_state' => '正常',
                'account_extend' => $extend
            ];
        } else {
            $response = OneDrive::getInstance($account)->getAccountInfo();
            $extend = Arr::get($response, 'data');
            $account_email = $response['errno'] === 0 ? Arr::get($extend, 'userPrincipalName') : '';
            $data = [
                'account_email' => $account_email,
                'account_state' => '暂时无法使用',
                'account_extend' => $extend
            ];
        }
        Setting::batchUpdate($data);
    }
}
