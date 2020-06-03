<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Models;

use App\Models\Traits\HelperModel;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use  HelperModel;

    /**
     * @var array $fillable
     */
    protected $fillable = [
        'remark', 'accountType', 'clientId', 'clientSecret', 'redirectUri', 'accessToken', 'refreshToken', 'tokenExpires', 'status'
    ];

    protected $casts = [
        'tokenExpires' => 'int',
        'status' => 'int'
    ];
}
