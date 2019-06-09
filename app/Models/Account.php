<?php

namespace App\Models;

use App\Traits\HelperModel;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Account
 *
 * @property int $id
 * @property string $account_type
 * @property string $slug
 * @property string $account_email
 * @property string $access_token
 * @property string $refresh_token
 * @property string|null $access_token_expires
 * @property int $status
 * @property mixed $extend
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereAccessToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereAccessTokenExpires($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereAccountEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereAccountType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereExtend($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereMap($map)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereRefreshToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Account extends Model
{
    use  HelperModel;

    protected $fillable = ['slug', 'account_type', 'account_email', 'access_token', 'refresh_token', 'access_token_expires', 'status', 'extend'];

    protected $casts = [
        'id' => 'int',
    ];

    /**
     * 如果 ext 是 json 则转成数组
     *
     * @param $value
     * @return mixed
     */
    public function getExtendAttribute($value)
    {
        return is_json($value) ? json_decode($value, true) : $value;
    }

    /**
     * 如果 ext 是数组 则转成 json
     *
     * @param $value
     */
    public function setExtendAttribute($value)
    {
        $this->attributes['extend'] = is_array($value) ? json_encode($value) : $value;
    }

    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    public static $status_text = [
        self::STATUS_OFF => '账号不可用',
        self::STATUS_ON => '账号正常',
    ];

}
