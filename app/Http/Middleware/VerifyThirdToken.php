<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyThirdToken
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
     * 处理第三方token 请求
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed|void
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
            if (strcmp(setting('third_access_token'), $authHeader) === 0) {
                return $next($request);
            }

        }
        abort(401);
    }
}
