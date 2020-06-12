<?php
/**
 * This file is part of the wangningkai/OLAINDEX.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Http\Controllers;

use App\Helpers\HashidsHelper;
use App\Models\Account;
use App\Service\OneDrive;
use Illuminate\Http\Request;
use Cache;

class HomeController extends BaseController
{
    public function __invoke(Request $request)
    {
        // 账号处理
        $accounts = \Cache::remember('ac:list', 600, static function () {
            return Account::query()
                ->select(['id', 'remark'])
                ->where('status', 1)->get();
        });
        $account_id = 0;
        if ($accounts) {
            $account_id = array_get(array_first($accounts), 'id');
            $hash = array_get(array_first($accounts), 'hash_id');
        }
        if (!$account_id) {
            abort(404, '账号不存在');
        }

        // 资源处理
        $root = array_get(setting($hash), 'root', '/');
        $query = trim($root, '/');
        $service = (new OneDrive($account_id));
        $item = Cache::remember('d:item:' . $query, setting('cache_expires'), static function () use ($service, $query) {
            return $service->fetchItem($query);
        });
        $list = Cache::remember('d:list:' . $query, setting('cache_expires'), static function () use ($service, $query) {
            return $service->fetchList($query);
        });
        $doc = [
            'head' => '## HEAD',
            'readme' => '## README'
        ];
        return view(config('olaindex.theme') . 'one', compact('accounts', 'hash', 'item', 'list', 'doc'));
    }
}
