<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Service;

use App\Models\Account;
use Curl\Curl;
use Illuminate\Support\Collection;
use Log;

class AccessToken
{
    private $account;

    public function __construct($id)
    {
        $account = Account::find($id);
        if (!$account) {
            throw new \RuntimeException('Not Found Account.');
        }
        $this->account = $account;
    }

    /**
     * Store the access_token
     * @param Collection $accessToken
     */
    private function storeTokens($accessToken): void
    {
        $data = [
            'accessToken' => $accessToken->get('access_token'),
            'refreshToken' => $accessToken->get('refresh_token'),
            'tokenExpires' => $accessToken->get('expires_in') + time(),
        ];
        $this->account->update($data);
    }

    private function refreshAccessToken()
    {
        $accountType = $this->getAccountType();
        $clientConfig = (new Client())
            ->setAccountType($accountType);
        $form_params = [
            'client_id' => $this->account->clientId,
            'client_secret' => $this->account->clientSecret,
            'redirect_uri' => $this->account->redirectUri,
            'refresh_token' => $this->account->refreshToken,
            'grant_type' => 'refresh_token',
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
            Log::error('Error refresh access token. ', $error);
            return '';
        }
        $_accessToken = collect($curl->response);
        $this->storeTokens($_accessToken);
        Log::info('刷新accessToken', ['account_id', $this->account->id]);
        return $_accessToken->get('access_token');
    }

    public function getAccountType()
    {
        return $this->account->accountType;
    }

    public function getAccessToken()
    {
        if (!$this->account->accessToken || !$this->account->refreshToken || !$this->account->tokenExpires) {
            return '';
        }
        $now = time() + 300;
        if ($this->account->tokenExpires <= $now) {
            // Token is expired (or very close to it)
            // so let's refresh
            return $this->refreshAccessToken();
        }
        return $this->account->accessToken;
    }
}
