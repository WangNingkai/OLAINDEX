<?php
/**
 * Created by PhpStorm.
 * User: WangNingkai
 * Date: 2020/3/29
 * Time: 9:39
 */

namespace App\Http\Controllers;


use App\Helpers\Tool;
use App\Models\Client;
use App\Service\Constants;
use Illuminate\Http\Request;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Cache;

class OauthController extends BaseController
{
    public function authorize(Request $request)
    {
        $accountType = strtoupper($request->get('accountType', 'COM'));
        $redirectUri = $request->get('redirect', Client::DEFAULT_REDIRECT_URI);
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
            'scopes' => Client::SCOPES,
        ];
        $oauthClient = new GenericProvider($oauthConfig);

        // 临时缓存
        $tmpKey = str_random();
        $oauthConfig = array_add($oauthConfig, 'accountType', $accountType);
        Cache::add($tmpKey, $oauthConfig, 15 * 60);

        // state :若代理跳转为<链接>否则为<缓存键>
        $state = $tmpKey;
        if (str_contains($redirectUri, 'ningkai.wang')) {
            $state = Tool::addUrlQueryParams($redirectUri, 'state', $state);
        }

        $authUrl = $oauthClient->getAuthorizationUrl([
            'state' => $state
        ]);
        return redirect()->away($authUrl);
    }

    public function callback(Request $request)
    {
        $state = $request->get('state');
        $authCode = $request->get('code');
        $oauthConfig = Cache::get($state);
        if (!$oauthConfig) {
            $this->showMessage('Invalid state');
//            return '';
        }
        unset($oauthConfig['accountType']);
        $oauthClient = new GenericProvider($oauthConfig);
        try {
            // Make the token request
            $accessToken = $oauthClient->getAccessToken('authorization_code', [
                'code' => $authCode
            ]);
            // 保存账号

        } catch (IdentityProviderException $e) {

        }
    }

}
