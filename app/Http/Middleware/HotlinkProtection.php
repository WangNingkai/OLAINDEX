<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;

class HotlinkProtection
{
    /**
     * 处理防盗链
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $hotlink_protection = setting('hotlink_protection');
        if ($hotlink_protection) {
            $self = $request->getHttpHost();

            // 简单处理防盗链，建议加入更加其他防盗链措施
            $whiteList = explode(' ', $hotlink_protection);
            $whiteList[] = $self; // 添加应用本身为白名单
            if (!$request->server('HTTP_REFERER')) {
                abort(403);
            }
            //判断 $_SERVER['HTTP_REFERER'] 是不是处于白名单
            if (Str::contains($request->server('HTTP_REFERER'), $whiteList)) {
                return $next($request);
            }
            abort(403);
        }

        return $next($request);
    }
}
