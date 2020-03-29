<?php
/**
 * Created by PhpStorm.
 * User: WangNingkai
 * Date: 2020/3/29
 * Time: 9:12
 */

namespace App\Models;


/**
 * Class Client
 * @package App\Models
 */
class Client
{
    const DEFAULT_REDIRECT_URI = 'https://olaindex.github.io/oauth.html';
    const API_VERSION = 'v1.0';
    const SCOPES = 'offline_access user.read files.readwrite.all';

    const REST_ENDPOINT = 'https://graph.microsoft.com/';
    const AUTHORITY_URL = 'https://login.microsoftonline.com/common';
    const AUTHORIZE_ENDPOINT = '/oauth2/v2.0/authorize';
    const TOKEN_ENDPOINT = '/oauth2/v2.0/token';

    // support 21vianet
    const REST_ENDPOINT_CN = 'https://microsoftgraph.chinacloudapi.cn/';
    const AUTHORITY_URL_CN = 'https://login.partner.microsoftonline.cn/common';
    const AUTHORIZE_ENDPOINT_CN = '/oauth2/authorize';
    const TOKEN_ENDPOINT_CN = '/oauth2/token';

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
            $this->authorizeUrl = self::AUTHORITY_URL;
            $this->authorizeEndpoint = self::AUTHORIZE_ENDPOINT;
            $this->tokenEndpoint = self::TOKEN_ENDPOINT;
            $this->restEndpoint = self::REST_ENDPOINT;
        } else {
            $this->authorizeUrl = self::AUTHORITY_URL_CN;
            $this->authorizeEndpoint = self::AUTHORIZE_ENDPOINT_CN;
            $this->tokenEndpoint = self::TOKEN_ENDPOINT_CN;
            $this->restEndpoint = self::REST_ENDPOINT_CN;
        }
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

}
