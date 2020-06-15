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
        // 缓存处理
        $item = Cache::remember('d:item:' . $query, setting('cache_expires'), static function () use ($service, $query) {
            return $service->fetchItem($query);
        });
        $list = Cache::remember('d:list:' . $query, setting('cache_expires'), static function () use ($service, $query) {
            return $service->fetchList($query);
        });
        // 读取预设资源
        $doc = $this->filterDoc($list);
        // 资源过滤
        $list = $this->filter($list);
        return view(config('olaindex.theme') . 'one', compact('accounts', 'hash', 'item', 'list', 'doc'));
    }

    public function filter($list)
    {
        // 过滤微软内置无法读取的文件
        $list = array_where($list, static function ($value) {
            return !array_has($value, 'package.type');
        });
        // 过滤预留文件
        $list = array_where($list, static function ($value) {
            return !in_array($value['name'], ['README.md', 'HEAD.md', '.password', '.deny'], false);
        });
        // todo:过滤隐藏文件
        return $list;
    }

    public function filterDoc($list)
    {
        $readme = array_where($list, static function ($value) {
            return $value['name'] === 'README.md';
        });
        $head = array_where($list, static function ($value) {
            return $value['name'] === 'HEAD.md';
        });

        if (!empty($readme)) {
            $readme = array_first($readme);
            $readme = Cache::remember('d:content:' . $readme['id'], setting('cache_expires'), static function () use ($readme) {
                return file_get_contents($readme['@microsoft.graph.downloadUrl']);
            });
        } else {
            $readme = '';
        }
        if (!empty($head)) {
            $head = array_first($head);
            $head = Cache::remember('d:content:' . $head['id'], setting('cache_expires'), static function () use ($head) {
                return file_get_contents($head['@microsoft.graph.downloadUrl']);
            });
        } else {
            $head = '';
        }


        return compact('head', 'readme');
    }
}
