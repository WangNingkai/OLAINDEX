<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Http\Controllers;

use App\Helpers\HashidsHelper;
use App\Models\Account;
use App\Service\OneDrive;
use Cache;

class DiskController extends BaseController
{
    public function __invoke($hash, $query = '/')
    {
        // 账号处理
        $accounts = Cache::remember('ac:list', 600, static function () {
            return Account::query()
                ->select(['id', 'remark'])
                ->where('status', 1)->get();
        });
        $account_id = HashidsHelper::decode($hash);
        if (!$account_id) {
            abort(404, '账号不存在');
        }
        // 资源处理
        $root = array_get(setting($hash), 'root', '/');
        $root = trim($root, '/');
        $query = trim($query, '/');
        $query = "{$root}/$query";
        $service = (new OneDrive($account_id));
        $item = Cache::remember('d:item:' . $query, setting('cache_expires'), static function () use ($service, $query) {
            return $service->fetchItem($query);
        });
        $list = Cache::remember('d:list:' . $query, setting('cache_expires'), static function () use ($service, $query) {
            return $service->fetchList($query);
        });
        $list = $this->filter($list);
        $doc = [
            'head' => '## HEAD',
            'readme' => '## README'
        ];
        return view(config('olaindex.theme') . 'one', compact('accounts', 'hash', 'item', 'list', 'doc'));
    }

    public function filter($list)
    {
        $collect = collect($list);
        // 过滤微软内置无法读取的文件
        $collect->filter(static function ($value) {
            return !array_has($value, 'package.type');
        });
        // 过滤预留文件
        $collect->reject(static function ($value) {
            return in_array($value['name'], ['README.md', 'HEAD.md', '.password', '.deny'], false);
        });
        // 过滤隐藏文件
        return $collect->all();

    }
}
