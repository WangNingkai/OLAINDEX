<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Http\Controllers;


use App\Utils\Tool;

/**
 * 初始化安装操作
 * Class InstallController
 * @package App\Http\Controllers
 */
class InstallController extends BaseController
{
    /**
     * 申请相关密钥
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function apply(Request $request): RedirectResponse
    {
        // 感谢 donwa 提供的方法
        /*if (Tool::hasConfig()) {
            $this->>showMessage('已配置相关信息', false);

            return view(config('olaindex.theme') . 'message');
        }*/
        $redirect_uri = $request->get('redirect_uri');
        if (!$redirect_uri) {
            /*Tool::showMessage('重定向地址缺失', false);*/

            return view(config('olaindex.theme') . 'message');
        }
        $ru = 'https://developer.microsoft.com/en-us/graph/quick-start?appID=_appId_&appName=_appName_&redirectUrl='
            . $redirect_uri . '&platform=option-php';
        $deepLink = '/quickstart/graphIO?publicClientSupport=false&appName=OLAINDEX&redirectUrl='
            . $redirect_uri . '&allowImplicitFlow=false&ru='
            . urlencode($ru);
        $app_url = 'https://apps.dev.microsoft.com/?deepLink=' . urlencode($deepLink);

        return redirect()->away($app_url);
    }

}
