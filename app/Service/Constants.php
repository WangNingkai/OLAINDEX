<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Service;

final class Constants
{
    public const LOGO
        = <<<EOF
   ____  __    ___    _____   ______  _______  __
  / __ \/ /   /   |  /  _/ | / / __ \/ ____/ |/ /
 / / / / /   / /| |  / //  |/ / / / / __/  |   /
/ /_/ / /___/ ___ |_/ // /|  / /_/ / /___ /   |
\____/_____/_/  |_/___/_/ |_/_____/_____//_/|_|

- v6.0.0

- Designed by IMWNK | Powered by OLAINDEX

EOF;
    public const VERSION = 'v5.0';

    public const DEFAULT_REDIRECT_URI = 'https://olaindex.github.io/oauth.html';
    public const API_VERSION = 'v1.0';
    public const SCOPES = 'offline_access user.read files.readwrite.all';

    public const REST_ENDPOINT = 'https://graph.microsoft.com/';
    public const AUTHORITY_URL = 'https://login.microsoftonline.com/common';
    public const AUTHORIZE_ENDPOINT = '/oauth2/v2.0/authorize';
    public const TOKEN_ENDPOINT = '/oauth2/v2.0/token';

    // support 21vianet
    public const REST_ENDPOINT_CN = 'https://microsoftgraph.chinacloudapi.cn/';
    public const AUTHORITY_URL_CN = 'https://login.partner.microsoftonline.cn/common';
    public const AUTHORIZE_ENDPOINT_CN = '/oauth2/authorize';
    public const TOKEN_ENDPOINT_CN = '/oauth2/token';
}
