<?php

namespace App\Http\Middleware;

use App\Utils\Tool;
use Closure;

class VerifyInstallation
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
        // 检测是否配置client_id等信息
        if (!Tool::hasConfig()) {
            return redirect()->route('_1stInstall');
        }

        return $next($request);
    }
}
