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
        // 图床过滤
        $openImageHost = setting('open_image_host', 0);
        if (!$openImageHost && $request->routeIs(['image', 'image.upload'])) {
            abort(404);
        }
        return $next($request);
    }
}
