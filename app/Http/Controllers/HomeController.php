<?php
/**
 * This file is part of the wangningkai/OLAINDEX.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Http\Controllers;


use App\Models\Account;

class HomeController extends BaseController
{
    public function __invoke()
    {
        $accounts = Account::query()
            ->select(['id', 'accountType', 'remark', 'status', 'updated_at'])
            ->where('status', 1)->get();
        return view(config('olaindex.theme') . 'one', compact('accounts'));
    }

}
