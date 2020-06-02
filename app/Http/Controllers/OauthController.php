<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Cache;

class OauthController extends BaseController
{
    public function callback(Request $request)
    {
        $state = $request->get('state');
        $authCode = $request->get('code');
        $oauthConfig = Cache::pull($state);
        if (!$oauthConfig) {
            $this->showMessage('Invalid state');
            return redirect()->route('message');
        }
        $config = $oauthConfig;
        unset($oauthConfig['accountType']);
        $oauthClient = new GenericProvider($oauthConfig);
        try {
            $_accessToken = $oauthClient->getAccessToken('authorization_code', [
                'code' => $authCode
            ]);
            $remark = $state;
            // 保存账号
            $accessToken = $_accessToken->getToken();
            $refreshToken = $_accessToken->getRefreshToken();
            $tokenExpires = $_accessToken->getExpires();
            $params = array_merge($config, compact('remark', 'accessToken', 'refreshToken', 'tokenExpires'));
            Account::create($params);
            return redirect()->route('admin.account.list');
        } catch (IdentityProviderException $e) {
            $this->showMessage('Error requesting access token. ' . $e->getMessage());
            return redirect()->route('message');
        }
    }
}
