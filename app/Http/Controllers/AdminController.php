<?php

namespace App\Http\Controllers;

use App\Helpers\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

/**
 * 后台管理操作
 * Class AdminController
 * @package App\Http\Controllers
 */
class AdminController extends Controller
{
    /**
     * ManageController constructor.
     */
    public function __construct()
    {
        $this->middleware('checkAuth')->except('login');
        $this->middleware('checkToken');
    }

    /**
     * 登录
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function login(Request $request)
    {
        if (Session::has('LogInfo')) return redirect()->route('admin.basic');
        if (!$request->isMethod('post')) return view('admin.login');
        $password = $request->get('password');
        if (md5($password) == Tool::config('password')) {
            $logInfo = [
                'LastLoginTime' => time(),
                'LastLoginIP' => $request->getClientIp(),
                'LastActivityTime' => time(),
            ];
            Session::put('LogInfo', $logInfo);
            $request->session()->regenerate();
            return redirect()->route('admin.basic');
        } else {
            Tool::showMessage('密码错误', false);
            return redirect()->back();
        }
    }

    /**
     * 退出
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $request->session()->invalidate();
        Tool::showMessage('已退出');
        return redirect()->route('home');
    }

    /**
     * 基础设置
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function basic(Request $request)
    {
        if (!$request->isMethod('post')) return view('admin.basic');
        $data = $request->except('_token');
        $this->update($data);
        return redirect()->back();
    }

    /**
     * 显示设置
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        if (!request()->isMethod('post')) return view('admin.show');
        $data = $request->except('_token');
        $this->update($data);
        return redirect()->back();
    }

    /**
     * 密码设置
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function profile(Request $request)
    {
        if (!$request->isMethod('post')) return view('admin.profile');
        $old_password = $request->get('old_password');
        $password = $request->get('password');
        $password_confirm = $request->get('password_confirm');
        if (md5($old_password) != Tool::config('password') || $old_password == '') {
            Tool::showMessage('请确保原密码的准确性！', false);
            return redirect()->back();
        }
        if ($password != $password_confirm || $old_password == '' || $old_password == '') {
            Tool::showMessage('两次密码不一致', false);
            return redirect()->back();
        }
        $data = ['password' => md5($password)];
        $this->update($data);
        return redirect()->back();
    }

    /**
     * 缓存清理
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clear()
    {
        Artisan::call('cache:clear');
        Tool::showMessage('清理成功');
        return redirect()->route('admin.basic');
    }

    /**
     * 设置更新
     * @param $data
     * @return bool|int
     */
    public function update($data)
    {
        $config = Tool::config();
        $config = array_merge($config, $data);
        $saved = Tool::saveConfig($config);
        Cache::forget('config');
        Tool::showMessage('更新成功');
        return $saved;
    }
}
