<?php

namespace App\Http\Middleware;

use Closure;

class CustomMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 图床开关
        $openImageHost = setting('open_image_host', 0);
        $publicImageHost = setting('public_image_host', 0);
        if (!$openImageHost || (!$publicImageHost && auth()->guest())) {
            abort(404);
        }
        return $next($request);
    }
}
