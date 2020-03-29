<?php


namespace App\Models;


use App\Models\Traits\HelperModel;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Account
 *
 * @property int $id
 * @property string $accountType
 * @property string $clientId
 * @property string $clientSecret
 * @property string $redirectUri
 * @property string $accessToken
 * @property string $refreshToken
 * @property int $tokenExpires
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereAccessToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereAccountType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereClientSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereMap($map)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereRedirectUri($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereRefreshToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereTokenExpires($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Account extends Model
{
    use  HelperModel;

    /**
     * @var array $fillable
     */
    protected $fillable = [
        'accountType', 'clientId', 'redirectUri', 'accessToken', 'refreshToken', 'tokenExpires', 'status'
    ];

}
