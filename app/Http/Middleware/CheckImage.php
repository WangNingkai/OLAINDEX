<?php

namespace App\Http\Middleware;

use App\Helpers\Tool;
use Closure;

class CheckImage
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
        $status = Tool::config('image_hosting', false);
        if (!$status) {
            return redirect()->route('list');
        }
        return $next($request);
    }
}
