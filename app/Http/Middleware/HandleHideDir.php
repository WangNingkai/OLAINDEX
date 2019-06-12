<?php


namespace App\Http\Middleware;

use App\Utils\Tool;
use Closure;

class HandleHideDir
{
    /**
     * 处理隐藏目录
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     *
     * @return mixed|void
     */
    public function handle($request, Closure $next)
    {
        $requestPath = $request->route()->parameter('query', '/');

        $hideDir = Tool::handleHideItem(setting('hide_path'));

        $requestPath = trim($requestPath, '/');
        if ($requestPath === '' || blank($hideDir)) {
            return $next($request);
        }
        if (in_array($requestPath, $hideDir, false)) {
            Tool::showMessage('非法请求', false);

            return response()->view(config('olaindex.theme') . 'message');
        }
        return $next($request);
    }
}
