<?php

namespace App\Helpers;

class Constants
{
    const LOGO
        = <<<EOF
   ____  __    ___    _____   ______  _______  __
  / __ \/ /   /   |  /  _/ | / / __ \/ ____/ |/ /
 / / / / /   / /| |  / //  |/ / / / / __/  |   / 
/ /_/ / /___/ ___ |_/ // /|  / /_/ / /___ /   |  
\____/_____/_/  |_/___/_/ |_/_____/_____//_/|_|
EOF;
    const LATEST_VERSION = 'v3.2';
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

    const FILE_ICON
        = [
            'stream'  => ['fa-file-text-o', 'text', ['txt', 'log']],
            'image'   => [
                'fa-file-image-o',
                'image',
                ['bmp', 'jpg', 'jpeg', 'png', 'gif', 'ico', 'jpe'],
            ],
            'video'   => [
                'fa-file-video-o',
                'video',
                [
                    'mkv',
                    'mp4',
                    'webm',
                    'avi',
                    'mpg',
                    'mpeg',
                    'rm',
                    'rmvb',
                    'mov',
                    'wmv',
                    'asf',
                    'ts',
                    'flv',
                ],
            ],
            'audio'   => ['fa-file-audio-o', 'music', ['ogg', 'mp3', 'wav']],
            'code'    => [
                'fa-file-code-o',
                'code',
                [
                    'html',
                    'htm',
                    'css',
                    'go',
                    'java',
                    'js',
                    'json',
                    'txt',
                    'sh',
                    'md',
                    'php',
                ],
            ],
            'doc'     => [
                'fa-file-word-o',
                'doc',
                [
                    'csv',
                    'doc',
                    'docx',
                    'odp',
                    'ods',
                    'odt',
                    'pot',
                    'potm',
                    'potx',
                    'pps',
                    'ppsx',
                    'ppsxm',
                    'ppt',
                    'pptm',
                    'pptx',
                    'rtf',
                    'xls',
                    'xlsx',
                ],
            ],
            'pdf'     => ['fa-file-pdf-o', 'pdf', ['pdf']],
            'zip'     => [
                'fa-file-archive-o',
                'zip',
                ['zip', '7z', 'rar', 'bz', 'gz'],
            ],
            'android' => ['fa-android', 'app', ['apk']],
            'exe'     => ['fa-windows', 'exe', ['exe', 'msi']],
        ];

    const SITE_THEME
        = [
            'Cerulean'  => 'cerulean',
            'Cosmo'     => 'cosmo',
            'Cyborg'    => 'cyborg',
            'Darkly'    => 'darkly',
            'Flatly'    => 'flatly',
            'Journal'   => 'journal',
            'Litera'    => 'litera',
            'Lumen'     => 'lumen',
            'Materia'   => 'materia',
            'Lux'       => 'lux',
            'Minty'     => 'minty',
            'Pulse'     => 'pulse',
            'Sandstone' => 'sandstone',
            'Simplex'   => 'simplex',
            'Sketchy'   => 'sketchy',
            'Slate'     => 'slate',
            'Solar'     => 'solar',
            'Spacelab'  => 'spacelab',
            'Superhero' => 'superhero',
            'United'    => 'united',
            'Yeti'      => 'yeti',
        ];
}
