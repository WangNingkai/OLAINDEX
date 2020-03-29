<?php
/**
 * Created by PhpStorm.
 * User: WangNingkai
 * Date: 2020/3/29
 * Time: 9:39
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
        unset($oauthConfig['accountType']);
        $oauthClient = new GenericProvider($oauthConfig);
        try {
            // Make the token request
            $_accessToken = $oauthClient->getAccessToken('authorization_code', [
                'code' => $authCode
            ]);
            // 保存账号
            $accessToken = $_accessToken->getToken();
            $refreshToken = $_accessToken->getRefreshToken();
            $tokenExpires = $_accessToken->getExpires();

        } catch (IdentityProviderException $e) {

        }
    }

}
