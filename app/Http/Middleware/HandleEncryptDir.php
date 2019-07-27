<?php

namespace App\Http\Middleware;

use App\Helpers\Tool;
use Closure;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class HandleEncryptDir
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
        if (auth()->guard('admin')->user()) {
            return $next($request);
        }

        $methodName = explode('@', Arr::get($request->route()->action, 'uses', 'home'));
        if (count($methodName) > 1 && !in_array($methodName[1], Arr::get(app('onedrive')->settings, 'encrypt_options'))) {
            return $next($request);
        }

        $route = $request->route()->getName();
        $realPath = $request->route()->parameter('query') ?? '/';
        $encryptDir = Tool::handleEncryptDir(app('onedrive')->encrypt_path);

        // TODO:
        foreach ($encryptDir as $key => $item) {
            if (Str::startsWith(Tool::getAbsolutePath($realPath), $key)) {
                $encryptKey = $key;
                if (Session::has('password:' . $key)) {
                    $data = Session::get('password:' . $key);
                    $encryptKey = $data['encryptKey'];
                    if (
                        strcmp($encryptDir[$encryptKey], decrypt($data['password'])) !== 0
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
