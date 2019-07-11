<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/admin';

    protected $redirectToLogin = '/admin/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view(config('olaindex.theme') . 'admin.login');
    }

    public function authenticated(Request $request, $user)
    {
        Auth::guard('web')->login($user);
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();
        Auth::guard('web')->logout();

        return $this->loggedOut($request) ?: redirect($this->redirectToLogin);
    } 

    protected function guard()
    {
        return Auth::guard('admin');
    }
}
