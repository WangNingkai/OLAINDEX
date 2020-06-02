<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Http\Controllers;


use App\Helpers\Tool;
use App\Service\GraphClient;
use App\Service\GraphResponse;
use App\Service\OneDrive;

class IndexController extends BaseController
{
    public function __invoke($id)
    {
        // 文件列表
        $q = request()->get('q', '/');
        $data = (new OneDrive(3))->fetchInfo();
        return response()->json($data);

    }
}
