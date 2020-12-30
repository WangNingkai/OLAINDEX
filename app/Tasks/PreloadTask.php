<?php
/**
 * This file is part of the wangningkai/OLAINDEX.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Tasks;

use App\Helpers\HashidsHelper;
use App\Models\Account;
use App\Service\GraphErrorEnum;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use Cache;
use Log;

class PreloadTask extends Task
{
    private $hash;
    private $query;

    public function __construct($hash, $query)
    {
        $this->hash = $hash;
        $this->query = $query;
    }

    public function handle()
    {
        if (!$this->hash) {
            $account_id = setting('primary_account', 0);
        } else {
            $account_id = HashidsHelper::decode($this->hash);
        }
        if (!$account_id) {
            Log::error('preload.账号不存在！');
            return;
        }
        $account = Account::find($account_id);
        if (!$account) {
            Log::error('preload.账号不存在！');
            return;
        }
        // 资源处理
        $root = $account->config['root'] ?? '/';
        $query = trim($this->query, '/');
        $path = explode('/', $query);
        $path = array_filter($path);
        $query = trans_absolute_path(trim("{$root}/$query", '/'));

        $service = $account->getOneDriveService();

        $item = Cache::remember("d:item:{$account_id}:{$query}", setting('cache_expires'), function () use ($service, $query) {
            return $service->fetchItem($query);
        });
        if (array_key_exists('code', $item)) {
            $msg = array_get($item, 'message', '404NotFound');
            $msg = GraphErrorEnum::get($item['code']) ?? $msg;
            Cache::forget("d:item:{$account_id}:{$query}");
            Log::error('preload.' . $msg);
            return;
        }
        Cache::add("d:item:{$account_id}:{$item['id']}", $item, setting('cache_expires'));
        $_cache[] = "d:item:{$account_id}:{$query}";
        if (array_key_exists('file', $item)) {
            return;
        }

        $list = Cache::remember("d:list:{$account_id}:{$query}", setting('cache_expires'), function () use ($service, $query) {
            return $service->fetchList($query);
        });
        if (array_key_exists('code', $list)) {
            $msg = array_get($list, 'message', '404NotFound');
            $msg = GraphErrorEnum::get($list['code']) ?? $msg;
            Cache::forget("d:list:{$account_id}:{$query}");
            Log::error('preload.' . $msg);
            return;
        }
        $_cache[] = "d:list:{$account_id}:{$query}";

        foreach ($list as $list_item) {
            $query = implode('/', array_add($path, key(array_slice($path, -1, 1, true)) + 1, $list_item['name']));
            $query = trim($query, '/');
            $query = trans_absolute_path(trim("{$root}/$query", '/'));
            Cache::add("d:item:{$account_id}:{$query}", $list_item, setting('cache_expires'));
            $_cache[] = "d:item:{$account_id}:{$query}";
            Cache::add("d:item:{$account_id}:{$list_item['id']}", $list_item, setting('cache_expires'));
            if (array_key_exists('file', $list_item)) {
                continue;
            }
            $child_list = Cache::remember("d:list:{$account_id}:{$query}", setting('cache_expires'), function () use ($service, $query) {
                return $service->fetchList($query);
            });
            $_cache[] = "d:list:{$account_id}:{$query}";
            if (array_key_exists('code', $child_list)) {
                Cache::forget("d:list:{$account_id}:{$query}");
                continue;
            }
        }
        Cache::add('tmp_cache', $_cache, 60);
    }

}
