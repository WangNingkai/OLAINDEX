<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Arr;

class CheckImage
{
    /**
     * 处理图床开启
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $status = Arr::get(app('onedrive')->settings, 'image_hosting');
        if ($status == 'disabled' || $status == 'admin_enabled' && auth()->guard('admin')->check()) {
            return redirect()->route('home');
        }

        return $next($request);
    }
}
