<?php
/**
 * This file is part of the wangningkai/OLAINDEX.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponseTrait;
use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends BaseController
{
    use ApiResponseTrait;

    /**
     * 账号列表
     * @return mixed
     */
    public function list()
    {
        $accounts = Account::query()
            ->select(['id', 'accountType', 'remark', 'status', 'updated_at'])
            ->simplePaginate();
        return view('admin.account-list', compact('accounts'));
    }

    /**
     * 更新账号设置
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function config(Request $request, $id)
    {
        $account = Account::find($id);
        if (!$account) {
            $this->showMessage('账号不存在！', true);
            return redirect()->back();
        }
        if ($request->isMethod('get')) {
            $config = $account->config;
            return view('admin.account', compact('config', 'account'));
        }

        $config = $request->get('config');
        if (array_has($config, 'open_sp') && !blank(array_get($config, 'open_sp'))
            && array_has($config, 'sp') && !blank(array_get($config, 'sp'))) {
            $service = $account->getOneDriveService(false);
            $resp = $service->fetchSharePoint(array_get($config, 'sp'));
            $sp_id = $resp['id'];
            $config['sp_id'] = $sp_id;
        }
        $data = [
            'config' => $config,
        ];
        $update = $account->update($data);
        if ($update) {
            $this->showMessage('修改成功！');
        }
        $account->refreshOneDriveQuota(true);
        return redirect()->back();
    }

    /**
     * 主账号设置
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setMain(Request $request)
    {
        $id = $request->get('id');
        $account = Account::find($id);
        if (!$account) {
            return $this->fail('账号不存在');
        }
        setting_set('primary_account', $id);
        return $this->success();
    }

    /**
     * 账号备注
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function remark(Request $request, $id)
    {
        $account = Account::find($id);
        if (!$account) {
            return $this->fail('账号不存在');
        }
        $remark = $request->get('remark');
        $account->remark = $remark;
        if ($account->save()) {
            return $this->success();
        }
        return $this->success();
    }

    /**
     * 账号删除
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function delete(Request $request, $id)
    {
        $account = Account::find($id);
        if (!$account) {
            return $this->fail('账号不存在');
        }
        if ($account->delete()) {
            return $this->success();
        }
        return $this->fail('删除失败');
    }

    /**
     * 账号详情
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function quota(Request $request, $id)
    {
        $account = Account::find($id);
        if (!$account) {
            return $this->fail('账号不存在！');
        }
        $data = $account->refreshOneDriveQuota(true);
        return $this->success($data);
    }

    /**
     * 网盘详情
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function drive(Request $request, $id)
    {
        $account = Account::find($id);
        if (!$account) {
            return $this->fail('账号不存在！');
        }
        $data = $account->getOneDriveInfo(true);
        return $this->success($data);
    }
}
