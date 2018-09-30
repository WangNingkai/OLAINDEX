<?php

namespace App\Http\Controllers;

use App\Helpers\Tool;
use App\Models\Parameter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericProvider;

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
        $this->provider = new GenericProvider([
            'clientId'                => config('graph.clientId'),
            'clientSecret'            => config('graph.clientSecret'),
            'redirectUri'             => config('graph.redirectUri'),
            'urlAuthorize'            => config('graph.urlAuthorize'),
            'urlAccessToken'          => config('graph.urlAccessToken'),
            'urlResourceOwnerDetails' => 'https://outlook.office.com/api/v1.0/me',
            'scopes'                  => config('graph.scopes')
        ]);
    }

    /**
     * 授权获取token
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function oauth(Request $request)
    {
        // 已授权则跳转
        if (Tool::config('access_token') != '' && Tool::config('refresh_token') != '' && Tool::config('access_token_expires') != '' ) {
            return redirect()->route('list');
        }
        // 第一次授权
        if ($request->isMethod('GET') && !$request->has('code')) {
            // 生成 state 缓存下来 跳转登录
            $authorizationUrl = $this->provider->getAuthorizationUrl();
            $state = $this->provider->getState();
            Cache::put('state',$state,10);
            return redirect()->away($authorizationUrl); // 跳转授权登录
        } elseif ( $request->isMethod('GET') && $request->has('code') ) {
            // 验证state
            if (empty($request->get('state')) || ($request->get('state') !== Cache::get('state'))) {
                if (Cache::has('state'))
                    Cache::forget('state');
                exit('Invalid state');
            }
            // 获取accessToken 和 refreshToken
            try {
                $accessToken = $this->provider->getAccessToken('authorization_code', [
                    'code'     => $_GET['code']
                ]);
                $access_token = $accessToken->getToken();
                $refresh_token = $accessToken->getRefreshToken();
                $expires = $accessToken->getExpires();
                $data = [
                    'access_token' => $access_token,
                    'refresh_token' => $refresh_token,
                    'access_token_expires' => $expires
                ];
                $this->updateCache($data);
                // 跳转首页
                return redirect()->route('list');
            } catch (IdentityProviderException $e) {
                exit($e->getMessage());
            }
        }
    }


    /**
     * 刷新Token
     * @return \Illuminate\Http\RedirectResponse
     */
    public function refreshToken()
    {
        $redirect = session('refresh_redirect') ?? '/list';
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
            $this->updateCache($data);
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
    public function updateCache($data)
    {
        // 清理缓存
        Cache::forget('config');
        // 更新数据库
        $editData = [];
        foreach ($data as $k => $v) {
            $editData[] = [
                'name' => $k,
                'value' => $v
            ];
        }
        $update = new Parameter();
        $res = $update->updateBatch($editData);
        return $res;
    }

}
