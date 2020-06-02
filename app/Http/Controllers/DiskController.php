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
use App\Service\OneDrive;

class DiskController extends BaseController
{
    public function __invoke($id)
    {
        $data = (new OneDrive($id))->fetchList();
        $res = $this->paginate($data);
        return response()->json($res);
    }
}
