<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Http\Controllers;

use App\Models\Account;
use App\Service\Client;
use Curl\Curl;
use Illuminate\Http\Request;
use Cache;
use Log;

/**
 * 绑定回调
 * Class OauthController
 * @package App\Http\Controllers
 */
class AuthController extends BaseController
{
    public function callback(Request $request)
    {
        $state = $request->get('state');
        $authCode = $request->get('code');
        $oauthConfig = Cache::pull($state);
        if (!$oauthConfig) {
            $this->showMessage('Invalid state', true);
            return redirect()->route('message');
        }
        $config = $oauthConfig;
        $accountType = $config['accountType'];
        $clientConfig = (new Client())
            ->setAccountType($accountType);
        $form_params = [
            'client_id' => $config['clientId'],
            'client_secret' => $config['clientSecret'],
            'redirect_uri' => $config['redirectUri'],
            'code' => $authCode,
            'grant_type' => 'authorization_code',
        ];
        if ($accountType === 'CN') {
            $form_params['resource'] = $clientConfig->getRestEndpoint();
        }
        $curl = new Curl();
        $curl->setHeader('Content-Type', 'application/x-www-form-urlencoded');
        $curl->post($clientConfig->getUrlAccessToken(), $form_params);
        if ($curl->error) {
            $error = [
                'errorCode' => $curl->errorCode,
                'errorMessage' => $curl->errorMessage,
                'request' => $form_params,
                'response' => collect($curl->response)->toArray()
            ];
            Log::error('Error requesting access token. ', $error);
            $message = $curl->errorCode . ': ' . $curl->errorMessage . "\n";
            $this->showMessage('Error requesting access token. ' . $message, true);
            return redirect()->route('message');
        }
        $_accessToken = collect($curl->response);
        $remark = $state;
        $accessToken = $_accessToken->get('access_token');
        $refreshToken = $_accessToken->get('refresh_token');
        $tokenExpires = $_accessToken->get('expires_in') + time();

        $params = array_merge($config, compact('remark', 'accessToken', 'refreshToken', 'tokenExpires'));
        // Log::info('获取accessToken', $params);
        $account = Account::create($params);
        $account->refreshOneDriveQuota(true);
        $this->showMessage('绑定成功！');
        if (setting('primary_account',0) === 0) {
            setting_set('primary_account', $account->id);
        }
        return redirect()->route('admin.account.list');
    }
}
