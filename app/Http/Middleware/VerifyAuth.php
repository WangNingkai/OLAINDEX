<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class VerifyAuth
{
    /**
     * Handle an incoming request.
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
