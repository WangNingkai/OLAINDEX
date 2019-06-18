<?php

namespace App\Http\Middleware;

use App\Utils\Tool;
use Closure;
use Illuminate\Support\Arr;
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
        $requestPath = $request->route()->parameter('query', '/');
        $encryptDir = Tool::handleEncryptItem(setting('encrypt_path'));

        if (blank($encryptDir)) {
            return $next($request);
        }
        foreach ($encryptDir as $path => $password) {
            $encryptPath = explode('>', $path)[1];
            if (Str::startsWith(Tool::getAbsolutePath($requestPath), $encryptPath)) {
                $encryptKey = 'password:' . $encryptPath;
                if (Session::has($encryptKey)) {
                    $data = Session::get($encryptKey);
                    if (time() > $data['expires'] || strcmp($password, decrypt($data['password'])) !== 0) {
                        Session::forget($encryptKey);
                        Tool::showMessage('密码已过期', false);

                        return response()->view(
                            config('olaindex.theme') . 'password',
                            compact('route', 'requestPath', 'encryptKey')
                        );
                    }
                    return $next($request);
                }
                return response()->view(
                    config('olaindex.theme') . 'password',
                    compact('route', 'requestPath', 'encryptKey')
                );
            }
        }

        return $next($request);
    }
}
