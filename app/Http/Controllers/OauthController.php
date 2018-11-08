<?php

namespace App\Http\Controllers;

use App\Helpers\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericProvider;

/**
 * 授权操作
 * Class OauthController
 * @package App\Http\Controllers
 */
class OauthController extends Controller
{
    /**
     * @var GenericProvider
     */
    public $provider;

    /**
     * OauthController constructor.
     */
    public function __construct()
    {
        $this->middleware('checkInstall');
        $this->provider = new GenericProvider([
            'clientId' => Tool::config('client_id'),
            'clientSecret' => Tool::config('client_secret'),
            'redirectUri' => Tool::config('redirect_uri'),
            'urlAuthorize' => 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize',
            'urlAccessToken' => 'https://login.microsoftonline.com/common/oauth2/v2.0/token',
            'urlResourceOwnerDetails' => 'https://outlook.office.com/api/v1.0/me',
            'scopes' => 'offline_access files.readwrite.all'
        ]);
    }

    /**
     * 获取授权
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function oauth(Request $request)
    {
        // 检测是否已授权
        if (Tool::config('access_token') != '' && Tool::config('refresh_token') != '' && Tool::config('access_token_expires') != '') {
            return redirect()->route('home'); // 检测授权状态
        }
        if ($request->isMethod('GET') && !$request->has('code')) {
            // 生成state缓存，跳转授权登录
            $authorizationUrl = $this->provider->getAuthorizationUrl();
            $state = $this->provider->getState();
            Cache::put('state', $state, 10);
            return redirect()->away($authorizationUrl); // 跳转授权登录
        } elseif ($request->isMethod('GET') && $request->has('code')) {
            // 验证 state
            if (empty($request->get('state')) || ($request->get('state') !== Cache::get('state'))) {
                if (Cache::has('state'))
                    Cache::forget('state');
                exit('Invalid state');
            }
            // 获取 accessToken & refreshToken
            try {
                $accessToken = $this->provider->getAccessToken('authorization_code', [
                    'code' => $_GET['code']
                ]);
                $access_token = $accessToken->getToken();
                $refresh_token = $accessToken->getRefreshToken();
                $expires = $accessToken->getExpires();
                $data = [
                    'access_token' => $access_token,
                    'refresh_token' => $refresh_token,
                    'access_token_expires' => $expires
                ];
                $this->update($data);
                // 保存授权跳转
                return redirect()->route('home');
            } catch (IdentityProviderException $e) {
                exit($e->getMessage());
            }
        }
    }


    /**
     * 刷新授权
     * @return \Illuminate\Http\RedirectResponse
     */
    public function refreshToken()
    {
        $redirect = session('refresh_redirect') ?? '/';
        $existingRefreshToken = Tool::config('refresh_token');
        try {
            $newAccessToken = $this->provider->getAccessToken('refresh_token', [
                'refresh_token' => $existingRefreshToken
            ]);
            $access_token = $newAccessToken->getToken();
            $refresh_token = $newAccessToken->getRefreshToken();
            $expires = $newAccessToken->getExpires();
            $data = [
                'access_token' => $access_token,
                'refresh_token' => $refresh_token,
                'access_token_expires' => $expires
            ];
            $this->update($data);
            return redirect()->away($redirect);
        } catch (IdentityProviderException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * 更新授权信息并清理缓存
     * @param $data
     * @return bool|int
     */
    public function update($data)
    {
        Cache::forget('config');
        $config = Tool::config();
        $config = array_merge($config, $data);
        $saved = Tool::saveConfig($config);
        return $saved;
    }

}
