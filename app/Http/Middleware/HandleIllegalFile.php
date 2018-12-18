<?php

namespace App\Http\Middleware;

use App\Helpers\Tool;
use Closure;

class HandleIllegalFile
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $queryPath = $request->route()->parameter('query');
        $path = trim(Tool::getAbsolutePath($queryPath), '/');
        $origin_path = rawurldecode($path);
        $path_array = $origin_path ? explode('/', $origin_path) : [];
        $fileName = array_pop($path_array);
        $illegalFile = ['README.md', 'HEAD.md', '.password', '.deny'];
        $pattern = '/^README\.md|HEAD\.md|\.password|\.deny/';
        if (in_array($fileName, $illegalFile)
            || preg_match($pattern, $fileName, $arr) > 0
        ) {
            Tool::showMessage('非法请求', false);

            return response()->view(config('olaindex.theme').'message');
        }

        return $next($request);
    }
}
