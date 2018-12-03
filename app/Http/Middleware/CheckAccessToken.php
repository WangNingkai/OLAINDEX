<?php

namespace App\Http\Middleware;

use App\Helpers\Tool;
use App\Http\Controllers\OauthController;
use Closure;
use Illuminate\Support\Facades\Session;

class CheckAccessToken
{
    /**
     * 处理access_token
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Tool::hasBind()) {
            Tool::showMessage('请绑定帐号！', false);

            return redirect()->route('bind');
        }
        $expires = Tool::config('access_token_expires', 0);
        $hasExpired = $expires - time() <= 0 ? true : false;
        if ($hasExpired) {
            $current = url()->current();
            Session::put('refresh_redirect', $current);
            $oauth = new OauthController();

            return $oauth->refreshToken();
        }

        return $next($request);
    }
}
