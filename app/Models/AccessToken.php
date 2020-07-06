<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Models;

use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Token\AccessTokenInterface;
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
     * @param AccessTokenInterface $accessToken
     */
    private function storeTokens($accessToken): void
    {
        $data = [
            'accessToken' => $accessToken->getToken(),
            'refreshToken' => $accessToken->getRefreshToken(),
            'tokenExpires' => $accessToken->getExpires(),
        ];
        $this->account->update($data);
    }

    private function refreshAccessToken()
    {
        $accountType = $this->getAccountType();
        $clientConfig = (new Client())
            ->setAccountType($accountType);
        $oauthConfig = [
            'clientId' => $this->account->clientId,
            'clientSecret' => $this->account->clientSecret,
            'redirectUri' => $this->account->redirectUri,
            'urlAuthorize' => $clientConfig->getUrlAuthorize(),
            'urlAccessToken' => $clientConfig->getUrlAccessToken(),
            'urlResourceOwnerDetails' => '',
            'scopes' => $clientConfig->getScopes(),
            'resource' => $clientConfig->getRestEndpoint(),
        ];
        $oauthClient = new GenericProvider($oauthConfig);
        try {
            $newToken = $oauthClient->getAccessToken('refresh_token', [
                'refresh_token' => $this->account->refreshToken
            ]);

            // Store the new values
            $this->storeTokens($newToken);
            Log::info('刷新AccessToken', ['account_id', $this->account->id]);
            return $newToken->getToken();
        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return '';
        }
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
