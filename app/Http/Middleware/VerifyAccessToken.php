<?php

namespace App\Http\Middleware;

use App\Utils\Tool;
use App\Http\Controllers\OauthController;
use Closure;
use Session;

class VerifyAccessToken
{
    /**
     * @param         $request
     * @param Closure $next
     *
     * @return false|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|mixed|string
     * @throws \ErrorException
     */
    public function handle($request, Closure $next)
    {
        if (!Tool::hasBind()) {
            Tool::showMessage('请绑定帐号！', false);

            return redirect()->route('bind');
        }
        $expires = setting('access_token_expires', 0);
        $expires = strtotime($expires);
        $hasExpired = $expires - time() <= 0;
        if ($hasExpired) {
            $current = url()->current();
            Session::put('refresh_redirect', $current);
            $oauth = new OauthController();

            return $oauth->refreshToken();
        }

        return $next($request);
    }
}
