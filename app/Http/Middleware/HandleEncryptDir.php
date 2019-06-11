<?php

namespace App\Http\Middleware;

use App\Utils\Tool;
use Closure;
use Session;
use Illuminate\Support\Str;

class HandleEncryptDir
{
    /**
     * 处理加密目录
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $route = $request->route()->getName();
        $realPath = $request->route()->parameter('query') ?? '/';
        $encryptDir = Tool::handleEncryptDir(Tool::config('encrypt_path'));
        foreach ($encryptDir as $key => $item) {
            if (Str::startsWith(Tool::getAbsolutePath($realPath), $key)) {
                $encryptKey = $key;
                if (Session::has('password:' . $key)) {
                    $data = Session::get('password:' . $key);
                    $encryptKey = $data['encryptKey'];
                    if (strcmp($encryptDir[$encryptKey], decrypt($data['password'])) !== 0
                        || time() > $data['expires']
                    ) {
                        Session::forget($key);
                        Tool::showMessage('密码已过期', false);

                        return response()->view(
                            config('olaindex.theme') . 'password',
                            compact('route', 'realPath', 'encryptKey')
                        );
                    } else {
                        return $next($request);
                    }
                } else {
                    return response()->view(
                        config('olaindex.theme') . 'password',
                        compact('route', 'realPath', 'encryptKey')
                    );
                }
            }
        }

        return $next($request);
    }
}
