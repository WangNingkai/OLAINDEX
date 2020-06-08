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

class HomeController extends BaseController
{
    public function __invoke($hash = '', $query = '/')
    {
        $accounts = \Cache::remember('ac:list', 600, static function () {
            return Account::query()
                ->select(['id', 'remark'])
                ->where('status', 1)->get();
        });
        if ($hash) {
            $account_id = HashidsHelper::decode($hash);
        } else {
            $account_id = 0;
            if ($accounts) {
                $account_id = array_get(array_first($accounts), 'id');
            }
        }
        if (!$account_id) {
            abort(404, '账号不存在');
        }
        $service = (new OneDrive($account_id));
        $item = $service->fetchItem($query);
        $list = $service->fetchList($query);
        $doc = [
            'header' => '',
            'readme' => ''
        ];
        return view(config('olaindex.theme') . 'one', compact('accounts', 'item', 'list', 'doc'));
    }
}
