<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponseTrait;
use App\Models\Account;
use App\Models\Setting;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Http\Request;
use Cache;

class AdminController extends BaseController
{
    use ApiResponseTrait;

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
     * 后台首页
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $links_count = ShortUrl::count('id');
        $accounts_count = Account::count('id');
        return view('admin.home', compact('links_count', 'accounts_count'));
    }

    /**
     * 全局设置
     * @param Request $request
     * @return mixed
     */
    public function config(Request $request)
    {
        $accounts = Account::fetchlist();
        if ($request->isMethod('get')) {
            return view('admin.config', compact('accounts'));
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
            return view('admin.profile');
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

}
