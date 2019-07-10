<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'web')
    {
        if ($request->segment(1) == 'admin') {
            $guard = 'admin';
        }

        if (Auth::guard($guard)->check()) {
            if ($guard == 'admin') {
                return redirect()->route('admin.basic');
            }

            return redirect()->route('onedrive.list');
        }

        return $next($request);
    }
}
