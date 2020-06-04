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
use App\Models\Setting;
use App\Service\OneDrive;
use Illuminate\Http\Request;
use Cache;

class AdminController extends BaseController
{
    /**
     * 全局设置
     */
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

    /**
     * 账号列表
     */
    public function account()
    {
        $accounts = Account::query()
            ->select(['id', 'accountType', 'remark', 'status', 'updated_at'])
            ->simplePaginate();
        return view(config('olaindex.theme') . '.admin.account', compact('accounts'));
    }

    /**
     * 账号设置
     */
    public function accountConfig($id, Request $request)
    {
        $decodeId = HashidsHelper::decode($id);
        if (!$decodeId) {
            $this->showMessage('账号不存在！', true);
            return redirect()->route('message');
        }
        $account = Account::find($decodeId);
        if (!$account) {
            $this->showMessage('账号不存在！', true);
            return redirect()->route('message');
        }
        if ($request->isMethod('get')) {
            $config = setting($id, []);
            return view(config('olaindex.theme') . 'admin.account-config', compact('config'));
        }
        $data = $request->except('_token');
        setting_set($id, $data);
        $this->showMessage('保存成功！');
        return redirect()->back();

    }

    /**
     * 账号详情
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * 网盘详情
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
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
