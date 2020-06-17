<?php

namespace App\Http\Middleware;

use Closure;

class AccessTokenMiddleware
{
    /**
     * @var string the HTTP header name
     */
    public $header = 'Authorization';
    /**
     * @var string a pattern to use to extract the HTTP authentication value
     */
    public $pattern = '/^Bearer\s+(.*?)$/';

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $authHeader = $request->header($this->header);

        if ($authHeader !== null) {
            if ($this->pattern !== null) {
                if (preg_match($this->pattern, $authHeader, $matches)) {
                    $authHeader = $matches[1];
                } else {
                    abort(401);
                }
            }
            if (strcmp(setting('access_token'), $authHeader) === 0) {
                return $next($request);
            }

        }
        abort(401);
    }
}
