<?php


namespace App\Service;

class CoreConstants
{

    const LOGO
        = <<<EOF
   ____  __    ___    _____   ______  _______  __
  / __ \/ /   /   |  /  _/ | / / __ \/ ____/ |/ /
 / / / / /   / /| |  / //  |/ / / / / __/  |   / 
/ /_/ / /___/ ___ |_/ // /|  / /_/ / /___ /   |  
\____/_____/_/  |_/___/_/ |_/_____/_____//_/|_|
EOF;
    const LATEST_VERSION = 'v4.0';

    const DEFAULT_REDIRECT_URI = 'https://olaindex.ningkai.wang';

    const API_VERSION = 'v1.0';
    const REST_ENDPOINT = 'https://graph.microsoft.com/';

    const AUTHORITY_URL = 'https://login.microsoftonline.com/common';
    const AUTHORIZE_ENDPOINT = '/oauth2/v2.0/authorize';
    const TOKEN_ENDPOINT = '/oauth2/v2.0/token';

    // support 21vianet
    const REST_ENDPOINT_21V = 'https://microsoftgraph.chinacloudapi.cn/';
    const AUTHORITY_URL_21V = 'https://login.partner.microsoftonline.cn/common';
    const AUTHORIZE_ENDPOINT_21V = '/oauth2/authorize';
    const TOKEN_ENDPOINT_21V = '/oauth2/token';

    const SCOPES = 'offline_access user.read files.readwrite.all';

    const ACCOUNT_CN = 'cn'; // 世纪互联版
    const ACCOUNT_COM = 'com'; // 国际版

    const DEFAULT_RETRY = 3; // 默认重试次数
    const DEFAULT_TIMEOUT = 120; // 默认超时时间
    const DEFAULT_CONNECT_TIMEOUT = 5; // 默认连接超时时间


    public static function getClientConfig(string $account_type = 'com')
    {
        $config = [
            self::ACCOUNT_COM => [
                'client_id' => setting('client_id'),
                'client_secret' => setting('client_secret'),
                'redirect_uri' => setting('redirect_uri', self::DEFAULT_REDIRECT_URI),
                'authorize_url' => self::AUTHORITY_URL,
                'authorize_endpoint' => self::AUTHORIZE_ENDPOINT,
                'token_endpoint' => self::TOKEN_ENDPOINT,
                'graph_endpoint' => self::REST_ENDPOINT,
                'api_version' => self::API_VERSION,
                'scopes' => self::SCOPES
            ],
            self::ACCOUNT_CN => [
                'client_id' => setting('client_id'),
                'client_secret' => setting('client_secret'),
                'redirect_uri' => setting('redirect_uri', self::DEFAULT_REDIRECT_URI),
                'authorize_url' => self::AUTHORITY_URL_21V,
                'authorize_endpoint' => self::AUTHORIZE_ENDPOINT_21V,
                'token_endpoint' => self::TOKEN_ENDPOINT_21V,
                'graph_endpoint' => self::REST_ENDPOINT_21V,
                'api_version' => self::API_VERSION,
                'scopes' => self::SCOPES
            ]
        ];
        return $config[$account_type];
    }
}
