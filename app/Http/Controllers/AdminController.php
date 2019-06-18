<?php

namespace App\Http\Controllers;

use App\Utils\Tool;
use App\Jobs\RefreshCache;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Artisan;
use Auth;
use Hash;

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
        $this->middleware(['auth','verify.installation']);
    }

    /**
     * 基础设置
     *
     * @param Request $request
     * @return Factory|RedirectResponse|View
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
     * @return Factory|RedirectResponse|View
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
     * @return Factory|RedirectResponse|View
     */
    public function profile(Request $request)
    {
        if (!$request->isMethod('post')) {
            return view(config('olaindex.theme') . 'admin.profile');
        }
        /* @var $user User */
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
     * @return RedirectResponse
     */
    public function clear(): RedirectResponse
    {
        Artisan::call('cache:clear');
        Tool::showMessage('清理成功');

        return redirect()->route('admin.basic');
    }

    /**
     * 刷新缓存
     *
     * @return RedirectResponse
     */
    public function refresh(): RedirectResponse
    {
        if (setting('queue_refresh', 0)) {
            RefreshCache::dispatch()
                ->delay(Carbon::now()->addSeconds(5))
                ->onQueue('olaindex')
                ->onConnection('database');
            Tool::showMessage('后台正在刷新，请继续其它任务...');
        } else {
            Artisan::call('od:cache');
            Tool::showMessage('刷新成功');
        }
        return redirect()->route('admin.basic');
    }

    /**
     * 账号绑定
     *
     * @param Request $request
     *
     * @return Factory|RedirectResponse|View
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
            'access_token' => '',
            'refresh_token' => '',
            'access_token_expires' => 0,
            'root' => '/',
            'image_hosting' => 0,
            'image_hosting_path' => '',
            'account_email' => '',
            'account_state' => '暂时无法使用',
            'account_extend' => ''
        ];
        Setting::batchUpdate($data);
        Tool::showMessage('保存成功！');

        return redirect()->route('bind');
    }
}
