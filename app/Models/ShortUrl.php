<?php

namespace App\Models;

use App\Models\Traits\HelperModel;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ShortUrl
 *
 * @property int $id
 * @property string $original_url
 * @property string $short_code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|ShortUrl newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortUrl newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortUrl query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortUrl whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortUrl whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortUrl whereMap(array $map)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortUrl whereOriginalUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortUrl whereShortCode($value)
 * @mixin \Eloquent
 */
class ShortUrl extends Model
{
    use  HelperModel;

    public const UPDATED_AT = null;

    /**
     * @var array $fillable
     */
    protected $fillable = ['original_url', 'short_code'];

    /**
     * @var array $casts
     */
    protected $casts = [
        'id' => 'int',
        'original_url' => 'string',
        'short_code' => 'string',
    ];
}
