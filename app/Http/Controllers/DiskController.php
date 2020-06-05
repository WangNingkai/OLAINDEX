<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Http\Controllers;

use App\Helpers\HashidsHelper;
use App\Helpers\Tool;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Account;
use App\Service\GraphClient;
use App\Service\OneDrive;

class DiskController extends BaseController
{
    use ApiResponseTrait;

    public function __invoke($hash, $query = '/')
    {
        $account_id = HashidsHelper::decode($hash);

    }
}
