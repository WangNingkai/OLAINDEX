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
        $accounts = Account::query()
            ->select(['id', 'accountType', 'remark', 'status', 'updated_at'])
            ->simplePaginate();
        return view(config('olaindex.theme') . '.admin.account', compact('accounts'));
    }

    public function accountConfig($id, Request $request)
    {
        $account = Account::find($id);
        if (!$account) {
            $this->showMessage('账号不存在！', true);
            return redirect()->route('message');
        }
        $remark = $account->remark;
        if ($request->isMethod('get')) {
            $config = setting($remark, []);
            return view(config('olaindex.theme') . 'admin.account-config', compact('config'));
        }
        $data = $request->except('_token');
        setting_set($remark, $data);
        $this->showMessage('保存成功！');
        return redirect()->back();

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
        $data = array_get($info, 'quota', []);
        return response()->json($data);
    }

    public function driveDetail($id)
    {
        $key = 'dr:id:' . $id;
        if (!Cache::has($key)) {
            $data = (new OneDrive($id))->fetchMe();
            $id = array_get($data, 'id', '');
            if (!$id) {
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
