<?php

namespace App\Http\Middleware;

use Closure;

class CheckOneDrive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!app('onedrive')->is_binded) {
            abort(403, '请先绑定OneDrive账号!');
        }

        return $next($request);
    }
}
