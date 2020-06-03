<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Setting;
use App\Service\OneDrive;
use Illuminate\Http\Request;
use Cache;

class AdminController extends BaseController
{
    public function config(Request $request)
    {
        if ($request->isMethod('get')) {
            return view(config('olaindex.theme') . 'admin.config');
        }
        $data = $request->except('_token');
        Setting::batchUpdate($data);
        $this->showMessage('保存成功！');
        return redirect()->back();
    }

    public function account()
    {
        $accounts = Account::all(['id', 'accountType', 'remark', 'status']);
        return view(config('olaindex.theme') . '.admin.account', compact('accounts'));
    }

    public function accountDetail($id)
    {
        $key = 'ac:id:' . $id;
        if (!Cache::has($key)) {
            $data = (new OneDrive($id))->fetchInfo();
            $quota = array_get($data, 'quota', '');
            if (!$quota) {
                return response()->json($data);
            }
            Cache::add($key, $data, 300);
            $info = Cache::get($key);
        } else {
            $info = Cache::get($key);
        }
        return response()->json($info);
    }
}
