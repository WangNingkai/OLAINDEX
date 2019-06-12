<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class VerifyImageHost
{
    /**
     * 处理图床开启
     * @param $request
     * @param Closure $next
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function handle($request, Closure $next)
    {

        $status = setting('image_hosting', 0);

        if ((int)$status === 0 || ((int)$status === 2 && Auth::guest())) {
            return redirect()->route('home');
        }

        return $next($request);
    }
}
