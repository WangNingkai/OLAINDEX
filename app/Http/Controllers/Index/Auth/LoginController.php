<?php

namespace App\Http\Controllers\Index\Auth;

use App\Helpers\Tool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        if (Session::has('index_log_info')) {
            return redirect()->route($this->getRedirect());
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        array_walk($data, function (&$value, $key) {
            return trim($value);
        });

        $redirect = $this->getRedirect();

        if (Session::has('index_log_info')) {
            return redirect()->route($redirect);
        }

        if ($this->attemptLogin($data)) {
            $logInfo = [
                'LastLoginTime'    => time(),
                'LastLoginIP'      => $request->getClientIp(),
                'LastActivityTime' => time(),
            ];
            Session::put('index_log_info', $logInfo);

            return redirect()->route($redirect);
        } else {
            Tool::showMessage('密码错误', false);

            return redirect()->back();
        }
    }

    public function logout(Request $request)
    {
        $request->session()->forget('index_log_info');
        Tool::showMessage('用户已退出');

        return redirect()->route('login');
    }

    public function getRedirect()
    {
        return Tool::config('image_home', 0) ? 'image' : 'home';
    }

    protected function attemptLogin($data)
    {
        $users = Tool::config('users');

        foreach ($users as $user) {
            if ($user['email'] === $data['email'] && $user['password'] === md5($data['password'])) {
                return true;
            }
        }

        return false;
    }
}
