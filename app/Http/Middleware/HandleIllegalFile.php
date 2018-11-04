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
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $origin_path = rawurldecode(Tool::convertPath($request->getPathInfo(), false));
        $path_array = $origin_path ? explode('/', $origin_path) : [];
        $fileName = array_pop($path_array);
        $illegalFile = ['README.md', 'HEAD.md', '.password', '.deny'];
        if (in_array($fileName, $illegalFile) || preg_match('/^README\.md|HEAD\.md|\.password|\.deny/', $fileName, $arr) > 0) {
            abort(403);
        }
        return $next($request);
    }
}
