<?php
/**
 * This file is part of the wangningkai/OLAINDEX.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\BaseController;
use App\Http\Traits\ApiResponseTrait;

class IndexController extends BaseController
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware('access_token');
        $api_limit = setting('api_limit', 10);
        $this->middleware("throttle:{$api_limit},1");
    }

    public function index()
    {
        return $this->success();
    }

}
