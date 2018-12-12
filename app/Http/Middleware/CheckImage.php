<?php

namespace App\Http\Middleware;

use App\Helpers\Tool;
use Closure;
use Illuminate\Support\Facades\Session;

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
        $status = Tool::config('image_hosting', 0);
        if (!$status || $status == 2 && !Session::has('LogInfo')) {
            return redirect()->route('home');
        }

        return $next($request);
    }
}
