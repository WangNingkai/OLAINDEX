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
use App\Models\User;
use OneDrive;
use Illuminate\Http\Request;
use Cache;

class AdminController extends BaseController
{
    /**
     * 缓存清理
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clear()
    {
        Cache::flush();
        $this->showMessage('清理缓存成功！');
        return redirect()->back();
    }

    /**
     * 全局设置
     * @param Request $request
     * @return mixed
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
     * 账号设置
     * @param Request $request
     * @return mixed
     */
    public function profile(Request $request)
    {
        if ($request->isMethod('get')) {
            return view(config('olaindex.theme') . 'admin.profile');
        }

        $request->validate([
            'name' => 'required|min:5|max:20',
            'old_password' => 'required',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required|min:8',
        ]);

        /* @var $user User */
        $user = $request->user();
        if (!\Hash::check($request->get('old_password'), $user->password)) {
            $this->showMessage('原密码错误！', true);
            return redirect()->back();
        }

        if ($user->fill([
            'name' => $request->get('name'),
            'password' => \Hash::make($request->get('password'))
        ])->save()) {
            $this->showMessage('修改成功！');
        } else {
            $this->showMessage('密码修改失败！', true);
        }


        return redirect()->back();
    }

    /**
     * 账号列表
     * @return mixed
     */
    public function account()
    {
        $accounts = Account::query()
            ->select(['id', 'accountType', 'remark', 'status', 'updated_at'])
            ->simplePaginate();
        return view(config('olaindex.theme') . 'admin.account', compact('accounts'));
    }

    /**
     * 账号设置
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function accountConfig(Request $request, $id)
    {
        $account = Account::find($id);
        if (!$account) {
            $this->showMessage('账号不存在！', true);
            return redirect()->back();
        }
        $uuid = $account->hash_id;
        if ($request->isMethod('get')) {
            $config = setting($uuid, []);
            return view(config('olaindex.theme') . 'admin.account-config', compact('config'));
        }
        $data = $request->except('_token');
        setting_set($uuid, $data);
        $this->showMessage('保存成功！');
        return redirect()->back();
    }

    /**
     * 主账号设置
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function accountSet(Request $request)
    {
        $id = $request->post('id', 0);
        $account = Account::find($id);
        if (!$account) {
            return response()->json([
                'error' => '账号不存在！'
            ]);
        }
        setting_set('primary_account', $id);
        return response()->json([
            'error' => ''
        ]);
    }

    /**
     * 账号备注
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function accountRemark($id, Request $request)
    {
        $account = Account::find($id);
        if (!$account) {
            return response()->json([
                'error' => '账号不存在！'
            ]);
        }
        $remark = $request->get('remark');
        $account->remark = $remark;
        if ($account->save()) {
            Cache::forget('ac:list');
            return response()->json();
        }
        return response()->json([
            'error' => ''
        ]);
    }

    /**
     * 账号删除
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function accountDelete(Request $request)
    {

        $id = $request->post('id', 0);
        $account = Account::find($id);
        if (!$account) {
            return response()->json([
                'error' => '账号不存在！'
            ]);
        }
        if ($account->delete()) {
            Cache::forget('ac:list');
            return response()->json([
                'error' => ''
            ]);
        }
        return response()->json([
            'error' => '删除失败'
        ]);
    }

    /**
     * 账号详情
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function accountDetail($id)
    {
        $data = OneDrive::account($id)->fetchInfo();
        return response()->json($data);
    }

    /**
     * 网盘详情
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function driveDetail($id)
    {
        $data = OneDrive::account($id)->fetchMe();
        return response()->json($data);
    }
}
