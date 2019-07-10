<?php

namespace App\Http\Middleware;

use App\Http\Controllers\OauthController;
use Closure;
use Illuminate\Support\Facades\Session;

class CheckAccessToken
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
        $expires = app('onedrive')->access_token_expires;
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
