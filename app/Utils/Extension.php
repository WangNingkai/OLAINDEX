<?php

namespace App\Utils;

class Extension
{

    const FILE_ICON = [
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

    const FILE_STREAM = [
        'file' => 'application/octet-stream',
        'chm' => 'application/octet-stream',
        'ppt' => 'application/vnd.ms-powerpoint',
        'xls' => 'application/vnd.ms-excel',
        'doc' => 'application/msword',
        'exe' => 'application/octet-stream',
        'rar' => 'application/octet-stream',
        'js' => 'javascript/js',
        'css' => 'text/css',
        'hqx' => 'application/mac-binhex40',
        'bin' => 'application/octet-stream',
        'oda' => 'application/oda',
        'pdf' => 'application/pdf',
        'ai' => 'application/postsrcipt',
        'eps' => 'application/postsrcipt',
        'es' => 'application/postsrcipt',
        'rtf' => 'application/rtf',
        'mif' => 'application/x-mif',
        'csh' => 'application/x-csh',
        'dvi' => 'application/x-dvi',
        'hdf' => 'application/x-hdf',
        'nc' => 'application/x-netcdf',
        'cdf' => 'application/x-netcdf',
        'latex' => 'application/x-latex',
        'ts' => 'application/x-troll-ts',
        'src' => 'application/x-wais-source',
        'zip' => 'application/zip',
        'bcpio' => 'application/x-bcpio',
        'cpio' => 'application/x-cpio',
        'gtar' => 'application/x-gtar',
        'shar' => 'application/x-shar',
        'sv4cpio' => 'application/x-sv4cpio',
        'sv4crc' => 'application/x-sv4crc',
        'tar' => 'application/x-tar',
        'ustar' => 'application/x-ustar',
        'man' => 'application/x-troff-man',
        'sh' => 'application/x-sh',
        'tcl' => 'application/x-tcl',
        'tex' => 'application/x-tex',
        'texi' => 'application/x-texinfo',
        'texinfo' => 'application/x-texinfo',
        't' => 'application/x-troff',
        'tr' => 'application/x-troff',
        'roff' => 'application/x-troff',
        'shar' => 'application/x-shar',
        'me' => 'application/x-troll-me',
        'ts' => 'application/x-troll-ts',
        'gif' => 'image/gif',
        'jpeg' => 'image/pjpeg',
        'jpg' => 'image/pjpeg',
        'jpe' => 'image/pjpeg',
        'ras' => 'image/x-cmu-raster',
        'pbm' => 'image/x-portable-bitmap',
        'ppm' => 'image/x-portable-pixmap',
        'xbm' => 'image/x-xbitmap',
        'xwd' => 'image/x-xwindowdump',
        'ief' => 'image/ief',
        'tif' => 'image/tiff',
        'tiff' => 'image/tiff',
        'pnm' => 'image/x-portable-anymap',
        'pgm' => 'image/x-portable-graymap',
        'rgb' => 'image/x-rgb',
        'xpm' => 'image/x-xpixmap',
        'txt' => 'text/plain',
        'c' => 'text/plain',
        'cc' => 'text/plain',
        'h' => 'text/plain',
        'html' => 'text/html',
        'htm' => 'text/html',
        'htl' => 'text/html',
        'txt' => 'text/html',
        'php' => 'text/html',
        'rtx' => 'text/richtext',
        'etx' => 'text/x-setext',
        'tsv' => 'text/tab-separated-values',
        'mpeg' => 'video/mpeg',
        'mpg' => 'video/mpeg',
        'mpe' => 'video/mpeg',
        'avi' => 'video/x-msvideo',
        'qt' => 'video/quicktime',
        'mov' => 'video/quicktime',
        'moov' => 'video/quicktime',
        'movie' => 'video/x-sgi-movie',
        'au' => 'audio/basic',
        'snd' => 'audio/basic',
        'wav' => 'audio/x-wav',
        'aif' => 'audio/x-aiff',
        'aiff' => 'audio/x-aiff',
        'aifc' => 'audio/x-aiff',
        'swf' => 'application/x-shockwave-flash',
        'myz' => 'application/myz',
    ];

    const THEME = [
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

    /**
     * @param $ext
     * @return string
     */
    public static function getFileIcon($ext): string
    {
        if (in_array($ext, ['ogg', 'mp3', 'wav'], false)) {
            return 'audiotrack';
        }
        if ($ext === 'apk') {
            return 'android';
        }
        if ($ext === 'pdf') {
            return 'picture_as_pdf';
        }
        if (in_array($ext, [
            'bmp',
            'jpg',
            'jpeg',
            'png',
            'gif',
            'ico',
            'jpe',
        ])
        ) {
            return 'image';
        }
        if (in_array($ext, [
            'mp4',
            'mkv',
            'webm',
            'avi',
            'mpg',
            'mpeg',
            'rm',
            'rmvb',
            'mov',
            'wmv',
            'mkv',
            'asf',
        ])
        ) {
            return 'ondemand_video';
        }
        if (in_array($ext, [
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
        ])
        ) {
            return 'code';
        }

        return 'insert_drive_file';
    }
}
