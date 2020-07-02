<?php
/**
 * This file is part of the wangningkai/OLAINDEX.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Service;


use Illuminate\Support\Facades\Facade;

class OneDriveFacade  extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'onedrive';
    }

}
