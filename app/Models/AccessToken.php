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

class AccessToken
{
    private $account;

    public function __construct($id)
    {
        $account = Account::find($id);
        $this->account = $account;
    }

    private function getAccountType()
    {
        return $this->account->accountType;
    }

    /**
     * Store the access_token
     * @param AccessTokenInterface $accessToken
     */
    private function storeTokens($accessToken)
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
            'clientId' => $clientConfig->getClientId(),
            'clientSecret' => $clientConfig->getclientSecret(),
            'redirectUri' => $clientConfig->getRedirectUri(),
            'urlAuthorize' => $clientConfig->getUrlAuthorize(),
            'urlAccessToken' => $clientConfig->getUrlAccessToken(),
            'urlResourceOwnerDetails' => '',
            'scopes' => $clientConfig->getScopes(),
            'resource' => $clientConfig->getRestEndpoint(),
        ];
        $oauthClient = new GenericProvider($oauthConfig);
        try {
            $newToken = $oauthClient->getAccessToken('refresh_token', [
                'refresh_token' => $this->account->accessToken
            ]);

            // Store the new values
            $this->storeTokens($newToken);

            return $newToken->getToken();
        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
            return '';
        }
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
