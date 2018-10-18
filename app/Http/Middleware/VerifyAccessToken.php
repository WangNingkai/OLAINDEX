<?php

namespace App\Http\Middleware;

use App\Helpers\Tool;
use Closure;

class VerifyAccessToken
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
        if (Tool::config('refresh_token') == '' || Tool::config('access_token_expires') == '' || Tool::config('access_token') == '') {
            return redirect()->route('oauth');
        }
        $now = time();
        $expires = Tool::config('access_token_expires');
        $hasExpired = $expires - $now < 0 ? true : false;
        if ($hasExpired) {
            $current = url()->current();
            return redirect()->route('refresh')->with('refresh_redirect', $current);
        }
        return $next($request);
    }
}
