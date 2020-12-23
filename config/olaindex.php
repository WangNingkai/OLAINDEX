<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
/*
|--------------------------------------------------------------------------
| 自定义配置
|--------------------------------------------------------------------------
|
| theme      主题
| php_path   PHP路径
| api        API设置
| proxy      访问代理设置
|
*/
return [
    'theme' => env('THEME', 'default') . '.',
    'php_path' => env('PHP_PATH', '/usr/bin/php'),
    'api' => [
        'allow_list' => explode(',', env('API_ALLOW'))
    ],
    'proxy' => env('PROXY', '')
];

