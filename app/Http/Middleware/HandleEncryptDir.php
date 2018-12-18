<?php

namespace App\Http\Middleware;

use App\Helpers\Tool;
use Closure;
use Illuminate\Support\Facades\Session;

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
        $realPath = $request->route()->parameter('query') ?? '/';
        $encryptDir = Tool::handleEncryptDir(Tool::config('encrypt_path'));
        foreach ($encryptDir as $key => $item) {
            if (starts_with(Tool::getAbsolutePath($realPath), $key)) {
                $encryptKey = $key;
                if (Session::has('password:'.$key)) {
                    $data = Session::get('password:'.$key);
                    $encryptKey = $data['encryptKey'];
                    if (strcmp($encryptDir[$encryptKey], decrypt($data['password'])) !== 0
                        || time() > $data['expires']
                    ) {
                        Session::forget($key);
                        Tool::showMessage('密码已过期', false);

                        return response()->view(
                            config('olaindex.theme').'password',
                            compact('realPath', 'encryptKey')
                        );
                    } else {
                        return $next($request);
                    }
                } else {
                    return response()->view(
                        config('olaindex.theme').'password',
                        compact('realPath', 'encryptKey')
                    );
                }
            }
        }
        return $next($request);
    }
}
