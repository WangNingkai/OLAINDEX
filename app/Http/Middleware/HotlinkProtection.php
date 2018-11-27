<?php

namespace App\Http\Middleware;

use App\Helpers\Tool;
use Closure;

class HotlinkProtection
{
    /**
     * 处理防盗链
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $hotlink_protection = Tool::config('hotlink_protection', '');
        if (!$hotlink_protection) {
            return $next($request);
        }
        // 简单处理防盗链，建议加入更加其他防盗链措施
        $whiteList = explode(' ', $hotlink_protection);
        if (!$request->server('HTTP_REFERER')) {
            abort(403);
        }
        //判断 $_SERVER['HTTP_REFERER'] 是不是处于白名单
        foreach ($whiteList as $item) {
            if (strpos($request->server('HTTP_REFERER'), $item) === 0) {
                return $next($request);
            } else {
                abort(403);
            }
        }
    }
}
