<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Service;

/**
 * Class Client
 * @package App\Models
 */
class Client
{
    /**
     * @var string
     */
    public $accountType = 'COM';
    /**
     * @var string
     */
    public $clientId = '';
    /**
     * @var string
     */
    public $clientSecret = '';
    /**
     * @var string
     */
    public $redirectUri = 'https://olaindex.github.io/oauth.html';
    /**
     * @var string
     */
    public $authorizeUrl = '';
    /**
     * @var string
     */
    public $authorizeEndpoint = '';
    /**
     * @var string
     */
    public $tokenEndpoint = '';
    /**
     * @var string
     */
    public $restEndpoint = '';
    /**
     * @var string
     */
    public $apiVersion = '1.0';
    /**
     * @var string
     */
    public $scopes = 'offline_access user.read files.readwrite.all';

    public function __construct($array = [])
    {
        foreach ($array as $k => $v) {
            if (property_exists($this, $k)) {
                $this->$k = $v;
            }
        }
    }

    public function setAccountType($accountType = 'COM')
    {
        $this->accountType = $accountType;
        if ($this->accountType === 'COM') {
            $this->authorizeUrl = Constants::AUTHORITY_URL;
            $this->authorizeEndpoint = Constants::AUTHORIZE_ENDPOINT;
            $this->tokenEndpoint = Constants::TOKEN_ENDPOINT;
            $this->restEndpoint = Constants::REST_ENDPOINT;
        } else {
            $this->authorizeUrl = Constants::AUTHORITY_URL_CN;
            $this->authorizeEndpoint = Constants::AUTHORIZE_ENDPOINT_CN;
            $this->tokenEndpoint = Constants::TOKEN_ENDPOINT_CN;
            $this->restEndpoint = Constants::REST_ENDPOINT_CN;
        }
        return $this;
    }

    public function setApiVersion($apiVersion)
    {
        $this->apiVersion = $apiVersion;
        return $this;
    }

    public function setRedirectUri($redirectUri)
    {
        $this->redirectUri = $redirectUri;
        return $this;
    }

    public function getUrlAuthorize()
    {
        return $this->authorizeUrl . $this->authorizeEndpoint;
    }

    public function getUrlAccessToken()
    {
        return $this->authorizeUrl . $this->tokenEndpoint;
    }

    public function getRedirectUri()
    {
        return $this->redirectUri ?? Constants::DEFAULT_REDIRECT_URI;
    }

    public function getScopes()
    {
        return $this->scopes ?? Constants::SCOPES;
    }

    public function getClientId()
    {
        return $this->clientId;
    }

    public function getclientSecret()
    {
        return $this->clientSecret;
    }

    public function getApiVersion()
    {
        return $this->apiVersion ?? Constants::API_VERSION;
    }

    public function getRestEndpoint()
    {
        return $this->restEndpoint;
    }
}
