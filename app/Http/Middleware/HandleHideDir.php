<?php


namespace App\Http\Middleware;

use Closure;

class HandleHideDir
{
    /**
     * 处理隐藏目录
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        return $next($request);
    }

}
