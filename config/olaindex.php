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
| api        API设置
| proxy      访问代理设置
|
*/
return [
    'api' => [
        'allow_list' => explode(',', env('API_ALLOW'))
    ],
    'proxy' => env('PROXY', '')
];

