<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Http\Controllers;

use App\Service\Constants;
use Cache;
use App\Service\Client;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * 初始化安装操作
 * Class InstallController
 * @package App\Http\Controllers
 */
class InstallController extends BaseController
{
    /**
     * 申请密钥(仅支持通用版)
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function apply(Request $request): RedirectResponse
    {
        $request->validate([
            'redirectUri' => 'required',
        ]);
        $redirect_uri = $request->get('redirectUri');
        $ru = 'https://developer.microsoft.com/en-us/graph/quick-start?appID=_appId_&appName=_appName_&redirectUrl='
            . $redirect_uri . '&platform=option-php';
        $deepLink = '/quickstart/graphIO?publicClientSupport=false&appName=OLAINDEX&redirectUrl='
            . $redirect_uri . '&allowImplicitFlow=false&ru='
            . urlencode($ru);
        $app_url = 'https://apps.dev.microsoft.com/?deepLink=' . urlencode($deepLink);

        return redirect()->away($app_url);
    }

    /**
     * 安装
     * @param Request $request
     * @return RedirectResponse|View|mixed
     */
    public function install(Request $request)
    {
        //  显示基础信息的填写、申请或提交应用信息、返回
        if ($request->isMethod('get')) {
            return view('admin.install.install');
        }
        $request->validate([
            'accountType' => 'required',
            'clientId' => 'required',
            'clientSecret' => 'required',
            'redirectUri' => 'required',
        ]);
        $accountType = strtoupper($request->get('accountType', 'COM'));
        $redirectUri = $request->get('redirectUri', Constants::DEFAULT_REDIRECT_URI);
        $clientId = $request->get('clientId');
        $clientSecret = $request->get('clientSecret');

        return view(
            'admin.install.bind',
            compact('accountType', 'clientId', 'clientSecret', 'redirectUri')
        );
    }

    /**
     * 绑定
     * @param Request $request
     * @return RedirectResponse|View
     */
    public function bind(Request $request)
    {
        $request->validate([
            'accountType' => 'required',
            'clientId' => 'required',
            'clientSecret' => 'required',
            'redirectUri' => 'required',
        ]);

        $accountType = strtoupper($request->get('accountType', 'COM'));
        $redirectUri = $request->get('redirectUri', Constants::DEFAULT_REDIRECT_URI);
        $clientId = $request->get('clientId');
        $clientSecret = $request->get('clientSecret');
        $clientConfig = (new Client())
            ->setAccountType($accountType)
            ->setRedirectUri($redirectUri);

        $oauthConfig = [
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'redirectUri' => $clientConfig->redirectUri,
            'urlAuthorize' => $clientConfig->getUrlAuthorize(),
            'urlAccessToken' => $clientConfig->getUrlAccessToken(),
            'scopes' => Constants::SCOPES,
        ];
        $values = [
            'client_id' => $oauthConfig['clientId'],
            'redirect_uri' => $oauthConfig['redirectUri'],
            'scope' => $oauthConfig['scopes'],
            'response_type' => 'code',
        ];

        // 临时缓存
        $tmpKey = str_random();
        $oauthConfig = array_add($oauthConfig, 'accountType', $accountType);
        Cache::add($tmpKey, $oauthConfig, 15 * 60);// 限定15分钟内绑定成功

        // state :若代理跳转为<链接>否则为<缓存键>
        $state = $tmpKey;
        if (str_contains($redirectUri, 'github.io')) {
            $state = route('callback') . '?' . http_build_query(['state' => $state]);
        }

        $values['state'] = $state;
        $query = http_build_query($values, '', '&', PHP_QUERY_RFC3986);
        $authUrl = $clientConfig->getUrlAuthorize() . "?{$query}";
        return redirect()->away($authUrl);
    }

    /**
     * 返回重置
     * @return mixed
     */
    public function reset()
    {
        return redirect()->route('install');
    }
}
