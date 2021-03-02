<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Http\Controllers;

use App\Helpers\Tool;

class ShareController extends BaseController
{
    /**
     * 短链转换
     * @param $code
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke($code)
    {
        $url = Tool::decodeShortUrl($code);
        return redirect()->away($url);
    }
}
