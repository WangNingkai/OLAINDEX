<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Models;

use App\Helpers\HashidsHelper;
use App\Models\Traits\HelperModel;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Account
 * @package App\Models
 * @property $hash_id
 */
class Account extends Model
{
    use  HelperModel;

    /**
     * @var string[] $fillable
     */
    protected $fillable = [
        'remark', 'accountType', 'clientId', 'clientSecret', 'redirectUri', 'accessToken', 'refreshToken', 'tokenExpires', 'status'
    ];

    /**
     * @var string[] $casts
     */
    protected $casts = [
        'tokenExpires' => 'int',
        'status' => 'int'
    ];

    /**
     * id => HashId
     * @return string
     */
    public function getHashIdAttribute()
    {
        return HashidsHelper::encode($this->id);
    }
}
