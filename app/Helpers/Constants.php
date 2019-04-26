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
    const LATEST_VERSION = 'v3.2.1';
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

    const CLIENT_ID = 'bb99d067-d0b2-42d6-8bfe-84d50965b9ab';
    const CLIENT_SECRET = 'x81G/w+VhHQS8Bqr+595iT8YqMvEZLS2k866bZg84Vc=';
    const CLIENT_ID_21V = 'f095458c-ff92-44fc-8755-a5cde412161a';
    const CLIENT_SECRET_21V = 'ABPkWTp9DcQLRQRgfe20hNk9YWnlLzXuzzKyCJjUxBs=';

    public static $client_config = [
        self::ACCOUNT_COM => [
            'client_id' => self::CLIENT_ID,
            'client_secret' => self::CLIENT_SECRET,
            'redirect_uri' => self::DEFAULT_REDIRECT_URI,
            'authorize_url' => self::AUTHORITY_URL,
            'authorize_endpoint' => self::AUTHORIZE_ENDPOINT,
            'token_endpoint' => self::TOKEN_ENDPOINT,
            'graph_endpoint' => self::REST_ENDPOINT,
            'api_version' => self::API_VERSION,
            'scopes' => self::SCOPES
        ],
        self::ACCOUNT_CN => [
            'client_id' => self::CLIENT_ID_21V,
            'client_secret' => self::CLIENT_SECRET_21V,
            'redirect_uri' => self::DEFAULT_REDIRECT_URI,
            'authorize_url' => self::AUTHORITY_URL_21V,
            'authorize_endpoint' => self::AUTHORIZE_ENDPOINT_21V,
            'token_endpoint' => self::TOKEN_ENDPOINT_21V,
            'graph_endpoint' => self::REST_ENDPOINT_21V,
            'api_version' => self::API_VERSION,
            'scopes' => self::SCOPES
        ]
    ];

    const FILE_ICON
        = [
            'stream' => ['fa-file-text-o', 'text', ['txt', 'log']],
            'image' => [
                'fa-file-image-o',
                'image',
                ['bmp', 'jpg', 'jpeg', 'png', 'gif', 'ico', 'jpe'],
            ],
            'video' => [
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
            'audio' => ['fa-file-audio-o', 'music', ['ogg', 'mp3', 'wav']],
            'code' => [
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
            'doc' => [
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
            'pdf' => ['fa-file-pdf-o', 'pdf', ['pdf']],
            'zip' => [
                'fa-file-archive-o',
                'zip',
                ['zip', '7z', 'rar', 'bz', 'gz'],
            ],
            'android' => ['fa-android', 'app', ['apk']],
            'exe' => ['fa-windows', 'exe', ['exe', 'msi']],
        ];

    const SITE_THEME
        = [
            'Cerulean' => 'cerulean',
            'Cosmo' => 'cosmo',
            'Cyborg' => 'cyborg',
            'Darkly' => 'darkly',
            'Flatly' => 'flatly',
            'Journal' => 'journal',
            'Litera' => 'litera',
            'Lumen' => 'lumen',
            'Materia' => 'materia',
            'Lux' => 'lux',
            'Minty' => 'minty',
            'Pulse' => 'pulse',
            'Sandstone' => 'sandstone',
            'Simplex' => 'simplex',
            'Sketchy' => 'sketchy',
            'Slate' => 'slate',
            'Solar' => 'solar',
            'Spacelab' => 'spacelab',
            'Superhero' => 'superhero',
            'United' => 'united',
            'Yeti' => 'yeti',
        ];

    public static $fileStream = [
        'ez' => 'application/andrew-inset',
        'hqx' => 'application/mac-binhex40',
        'cpt' => 'application/mac-compactpro',
        'doc' => 'application/msword',
        'bin' => 'application/octet-stream',
        'dms' => 'application/octet-stream',
        'lha' => 'application/octet-stream',
        'lzh' => 'application/octet-stream',
        'exe' => 'application/octet-stream',
        'class' => 'application/octet-stream',
        'so' => 'application/octet-stream',
        'dll' => 'application/octet-stream',
        'oda' => 'application/oda',
        'pdf' => 'application/pdf',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',
        'smi' => 'application/smil',
        'smil' => 'application/smil',
        'mif' => 'application/vnd.mif',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',
        'wbxml' => 'application/vnd.wap.wbxml',
        'wmlc' => 'application/vnd.wap.wmlc',
        'wmlsc' => 'application/vnd.wap.wmlscriptc',
        'bcpio' => 'application/x-bcpio',
        'vcd' => 'application/x-cdlink',
        'pgn' => 'application/x-chess-pgn',
        'cpio' => 'application/x-cpio',
        'csh' => 'application/x-csh',
        'dcr' => 'application/x-director',
        'dir' => 'application/x-director',
        'dxr' => 'application/x-director',
        'dvi' => 'application/x-dvi',
        'spl' => 'application/x-futuresplash',
        'gtar' => 'application/x-gtar',
        'hdf' => 'application/x-hdf',
        'js' => 'application/x-javascript',
        'skp' => 'application/x-koan',
        'skd' => 'application/x-koan',
        'skt' => 'application/x-koan',
        'skm' => 'application/x-koan',
        'latex' => 'application/x-latex',
        'nc' => 'application/x-netcdf',
        'cdf' => 'application/x-netcdf',
        'sh' => 'application/x-sh',
        'shar' => 'application/x-shar',
        'swf' => 'application/x-shockwave-flash',
        'sit' => 'application/x-stuffit',
        'sv4cpio' => 'application/x-sv4cpio',
        'sv4crc' => 'application/x-sv4crc',
        'tar' => 'application/x-tar',
        'tcl' => 'application/x-tcl',
        'tex' => 'application/x-tex',
        'texinfo' => 'application/x-texinfo',
        'texi' => 'application/x-texinfo',
        't' => 'application/x-troff',
        'tr' => 'application/x-troff',
        'roff' => 'application/x-troff',
        'man' => 'application/x-troff-man',
        'me' => 'application/x-troff-me',
        'ms' => 'application/x-troff-ms',
        'ustar' => 'application/x-ustar',
        'src' => 'application/x-wais-source',
        'xhtml' => 'application/xhtml+xml',
        'xht' => 'application/xhtml+xml',
        'zip' => 'application/zip',
        'au' => 'audio/basic',
        'snd' => 'audio/basic',
        'mid' => 'audio/midi',
        'midi' => 'audio/midi',
        'kar' => 'audio/midi',
        'mpga' => 'audio/mpeg',
        'mp2' => 'audio/mpeg',
        'mp3' => 'audio/mpeg',
        'aif' => 'audio/x-aiff',
        'aiff' => 'audio/x-aiff',
        'aifc' => 'audio/x-aiff',
        'm3u' => 'audio/x-mpegurl',
        'ram' => 'audio/x-pn-realaudio',
        'rm' => 'audio/x-pn-realaudio',
        'rpm' => 'audio/x-pn-realaudio-plugin',
        'ra' => 'audio/x-realaudio',
        'wav' => 'audio/x-wav',
        'pdb' => 'chemical/x-pdb',
        'xyz' => 'chemical/x-xyz',
        'bmp' => 'image/bmp',
        'gif' => 'image/gif',
        'ief' => 'image/ief',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'jpe' => 'image/jpeg',
        'png' => 'image/png',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'djvu' => 'image/vnd.djvu',
        'djv' => 'image/vnd.djvu',
        'wbmp' => 'image/vnd.wap.wbmp',
        'ras' => 'image/x-cmu-raster',
        'pnm' => 'image/x-portable-anymap',
        'pbm' => 'image/x-portable-bitmap',
        'pgm' => 'image/x-portable-graymap',
        'ppm' => 'image/x-portable-pixmap',
        'rgb' => 'image/x-rgb',
        'xbm' => 'image/x-xbitmap',
        'xpm' => 'image/x-xpixmap',
        'xwd' => 'image/x-xwindowdump',
        'igs' => 'model/iges',
        'iges' => 'model/iges',
        'msh' => 'model/mesh',
        'mesh' => 'model/mesh',
        'silo' => 'model/mesh',
        'wrl' => 'model/vrml',
        'vrml' => 'model/vrml',
        'css' => 'text/css',
        'html' => 'text/html',
        'htm' => 'text/html',
        'asc' => 'text/plain',
        'txt' => 'text/plain',
        'rtx' => 'text/richtext',
        'rtf' => 'text/rtf',
        'sgml' => 'text/sgml',
        'sgm' => 'text/sgml',
        'tsv' => 'text/tab-separated-values',
        'wml' => 'text/vnd.wap.wml',
        'wmls' => 'text/vnd.wap.wmlscript',
        'etx' => 'text/x-setext',
        'xsl' => 'text/xml',
        'xml' => 'text/xml',
        'mpeg' => 'video/mpeg',
        'mpg' => 'video/mpeg',
        'mpe' => 'video/mpeg',
        'qt' => 'video/quicktime',
        'mov' => 'video/quicktime',
        'mxu' => 'video/vnd.mpegurl',
        'avi' => 'video/x-msvideo',
        'movie' => 'video/x-sgi-movie',
        'ice' => 'x-conference/x-cooltalk',
    ];


    public static $fileType = [
        'image' => [
            0 => 'image/pjpeg',
            1 => 'image/pjpeg',
            2 => 'image/gif',
            3 => 'image/pjpeg',
        ],
        'video' => [
            0 => 'video/x-msvideo',
            1 => 'video/mpeg',
            2 => 'video/mpeg',
            3 => 'video/quicktime',
            4 => 'application/x-troll-ts',
            5 => 'audio/x-pn-realaudio',
            6 => 'video/mp4',
        ],
        'audio' => [
            0 => 'audio/x-wav',
            1 => 'audio/mpeg'
        ],
        'doc' => [
            0 => 'application/msword',
            1 => 'application/vnd.ms-powerpoint',
            2 => 'application/rtf',
            3 => 'application/vnd.ms-excel',
            4 => 'application/pdf',
            5 => 'text/html',
            6 => 'text/html',
            7 => 'text/css',
            8 => 'javascript/js',
            9 => 'text/html',
            10 => 'application/x-sh',
            11 => 'text/html',
        ],
        'zip' => [
            0 => 'application/zip',
            1 => 'application/octet-stream',
        ],
    ];
}
