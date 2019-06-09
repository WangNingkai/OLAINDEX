<?php

namespace App\Http\Controllers;

use App\Helpers\Tool;
use App\Jobs\RefreshCache;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

/**
 * 后台管理操作
 * Class AdminController
 *
 * @package App\Http\Controllers
 */
class AdminController extends Controller
{
    /**
     * ManageController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 基础设置
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function basic(Request $request)
    {
        if (!$request->isMethod('post')) {
            return view(config('olaindex.theme') . 'admin.basic');
        }
        $data = $request->except('_token');

        Setting::batchUpdate($data);

        Tool::showMessage('保存成功！');

        return redirect()->back();
    }

    /**
     * 显示设置
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        if (!$request->isMethod('post')) {
            return view(config('olaindex.theme') . 'admin.show');
        }
        $data = $request->except('_token');
        Setting::batchUpdate($data);
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
        if (!$request->isMethod('post')) {
            return view(config('olaindex.theme') . 'admin.profile');
        }
        $user = Auth::user();
        $oldPassword = $request->get('old_password');
        $password = $request->get('password');
        $passwordConfirm = $request->get('password_confirm');

        if (!Hash::check($oldPassword, $user->password)) {
            Tool::showMessage('请确保原密码的准确性！', false);

            return redirect()->back();
        }
        if ($password !== $passwordConfirm) {
            Tool::showMessage('两次密码不一致', false);

            return redirect()->back();
        }

        $saved = User::query()->update([
            'id' => $user->id,
            'password' => bcrypt($password),
        ]);

        $msg = $saved ? '密码修改成功' : '请稍后重试';
        Tool::showMessage($msg, $saved);
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
        // todo:后台异步任务
//        Artisan::call('od:cache');

        RefreshCache::dispatch()->delay(Carbon::now()->addSeconds(5))->onQueue('olaindex');

        Tool::showMessage('后台正在刷新，请继续其它任务...');

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
        } else {
            if (!Tool::hasBind()) {
                return redirect()->route('bind');
            }
            $data = [
                'access_token' => '',
                'refresh_token' => '',
                'access_token_expires' => 0,
                'root' => '/',
                'image_hosting' => 0,
                'image_hosting_path' => '',
            ];
            Tool::updateConfig($data);
            Cache::forget('one:account');
            Tool::showMessage('保存成功！');

            return redirect()->route('bind');
        }
    }
}
