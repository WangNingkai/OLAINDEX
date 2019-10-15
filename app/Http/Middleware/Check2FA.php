<?php

namespace App\Http\Middleware;

use Closure;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class Check2FA
{
    public function handle($request, Closure $next)
    {
        if ($request->cookie('remember_2fa')) {
            return $next($request);
        }

        $authenticator = app(Authenticator::class)->boot($request);

        if ($authenticator->isAuthenticated()) {
            return $next($request);
        }

        return $authenticator->makeRequestOneTimePasswordResponse();
    }
}
