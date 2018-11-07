<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class CheckAuth
{
    /**
     * 处理登陆
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Session::has('LogInfo')) {
            return redirect()->route('login');
        }
        Session::put('LogInfo.LastActivityTime', time());
        return $next($request);
    }
}
