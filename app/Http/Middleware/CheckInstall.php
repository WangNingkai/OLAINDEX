<?php

namespace App\Http\Middleware;

use App\Helpers\Tool;
use Closure;

class CheckInstall
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
        // 检测是否配置client_id等信息
        $client_id = Tool::config('client_id');
        $client_secret = Tool::config('client_secret');
        $redirect_uri = Tool::config('redirect_uri');
        if ($client_id == '' || $client_secret == '' || $redirect_uri == '') {
            return redirect()->route('_1stInstall');
        }
        return $next($request);
    }
}
