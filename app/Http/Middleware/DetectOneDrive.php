<?php

namespace App\Http\Middleware;

use Closure;

class DetectOneDrive
{
    /**
     * 处理验证安装
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        getDefaultOneDriveAccount(route_parameter('onedrive'));

        return $next($request);
    }
}
