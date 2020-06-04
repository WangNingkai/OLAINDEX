<?php
/**
 * This file is part of the wangningkai/OLAINDEX.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Http\Controllers;


class HomeController extends BaseController
{
    public function __invoke()
    {
        return view(config('olaindex.theme') . 'one');
    }

}
