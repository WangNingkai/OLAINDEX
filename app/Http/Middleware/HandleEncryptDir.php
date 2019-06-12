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
        $requestPath = $request->route()->parameter('query', '/');

        $encryptDir = Tool::handleEncryptItem(setting('encrypt_path'));

        if (blank($encryptDir)) {
            return $next($request);
        }
        foreach ($encryptDir as $key => $item) {
            [$prefix, $key] = explode('-', $key);
            if (Str::startsWith(Tool::getAbsolutePath($requestPath), $key)) {
                $encryptKey = $key;
                if (Session::has('password:' . $key)) {
                    $data = Session::get('password:' . $key);
                    $encryptKey = $data['encryptKey'];
                    if (time() > $data['expires'] ||
                        strcmp($encryptDir[$prefix . '-' . $encryptKey], decrypt($data['password'])) !== 0) {
                        Session::forget($key);
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
