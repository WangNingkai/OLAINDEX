<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Http\Controllers;

use App\Helpers\Tool;
use App\Http\Traits\ApiResponseTrait;
use App\Service\GraphClient;
use App\Service\OneDrive;

class DiskController extends BaseController
{
    use ApiResponseTrait;

    public function __invoke($id)
    {
        $q = request()->get('q', '/');
//        $data = (new OneDrive($id))->path2Id($q);
//        return response()->json($data);
        $data = (new OneDrive($id))->fetchList($q);
        $res = $this->paginate($data, 10, false);
        return $this->success($res);
    }
}
