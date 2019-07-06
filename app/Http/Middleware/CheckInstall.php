<?php

namespace App\Http\Middleware;

use App\Helpers\Tool;
use Closure;

class CheckInstall
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
        // 检测是否配置client_id等信息
        // if (!Tool::hasConfig()) {
        //     return redirect()->route('_1stInstall');
        // }

        return $next($request);
    }
}
