<?php

namespace App\Http\Controllers\Index\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/onedrive';

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
        if (!empty(Auth::guard('web')->user())) {
            return redirect()->route('onedrive.list');
        }

        return view('auth.login');
    }

    protected function guard()
    {
        return Auth::guard('web');
    }
}
