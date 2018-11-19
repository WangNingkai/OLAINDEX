<?php

namespace App\Http\Middleware;

use App\Helpers\Tool;
use Closure;
use Illuminate\Support\Facades\Artisan;

class CheckAccessToken
{
    /**
     * 处理access_token
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Tool::config('refresh_token') == '' || Tool::config('access_token_expires') == '' || Tool::config('access_token') == '') {
            Tool::showMessage('请绑定帐号！', false);
            return redirect()->route('bind');
        }
        if (!refresh_token()) {
            $current = url()->current();
            Artisan::call('od:refresh');
            return redirect()->away($current);
        }
        return $next($request);
    }
}
