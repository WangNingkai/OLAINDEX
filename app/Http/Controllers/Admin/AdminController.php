<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Tool;
use App\Helpers\Constants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;

/**
 * 后台管理操作
 * Class AdminController
 *
 * @package App\Http\Controllers
 */
class AdminController extends Controller
{
    public function showBasic()
    {
        $admin = $this->user();

        return view(config('olaindex.theme') . 'admin.new_basic', compact('admin'));
    }

    /**
     * 基础设置
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function basic(Request $request)
    {
        $data = $request->validate([
            'name'               => 'sometimes|nullable|string',
            'theme'              => 'sometimes|nullable|string|in:' . implode(',', Constants::SITE_THEME),
            'hotlink_protection' => 'sometimes|nullable|string',
            'copyright'          => 'sometimes|nullable|string',
            'statistics'         => 'sometimes|nullable|string',
        ]);

        $user = $this->user();
        $data = array_map(function (&$item) {
            return is_null($item) ? $item = '' : $item;
        }, $data);

        $user->update($data);
        // Tool::updateConfig($data);
        // Tool::showMessage('保存成功！');

        return success();
    }

    /**
     * 显示设置
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function settings(Request $request)
    {
        $data = $request->validate([
            'video'  => 'sometimes|nullable|string',
            'audio'  => 'sometimes|nullable|string',
            'image'  => 'sometimes|nullable|string',
            'dash'   => 'sometimes|nullable|string',
            'code'   => 'sometimes|nullable|string',
            'doc'    => 'sometimes|nullable|string',
            'stream' => 'sometimes|nullable|string'
        ]);

        $data = array_filter($data);
        Tool::updateConfig($data);
        Tool::showMessage('保存成功！');

        return redirect()->back();
    }

    /**
     * 密码设置
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function profile(Request $request)
    {
        $data = $request->validate([
            'old_password'     => 'required|string',
            'password'         => 'required|string|different:old_password',
            'password_confirm' => 'required|string|same:password',
        ]);

        $this->user()->update(Arr::only($data, 'password'));
        // Tool::updateConfig($data);
        // Tool::showMessage('保存成功！');

        return redirect()->back();
    }

    /**
     * 缓存清理
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clear()
    {
        Artisan::call('cache:clear');
        Tool::showMessage('清理成功');

        return redirect()->route('admin.basic');
    }

    /**
     * 刷新缓存
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function refresh()
    {
        Artisan::call('od:cache');
        Tool::showMessage('刷新成功');

        return redirect()->route('admin.basic');
    }

    /**
     * 账号绑定
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function bind(Request $request)
    {
        if (!$request->isMethod('post')) {
            return view(config('olaindex.theme') . 'admin.bind');
        }

        if (!Tool::hasBind()) {
            return redirect()->route('bind');
        }
        $data = [
            'access_token'         => '',
            'refresh_token'        => '',
            'access_token_expires' => 0,
            'root'                 => '/',
            'image_hosting'        => 0,
            'image_hosting_path'   => '',
        ];
        Tool::updateConfig($data);
        Cache::forget('one:account');
        Tool::showMessage('保存成功！');

        return redirect()->route('bind');
    }
}
