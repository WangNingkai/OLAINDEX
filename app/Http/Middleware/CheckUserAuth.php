<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class CheckUserAuth
{
    /**
     * 处理登陆
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Session::has('index_log_info')) {
            return redirect()->route('login');
        }
        Session::put('index_log_info.LastActivityTime', time());

        return $next($request);
    }
}
