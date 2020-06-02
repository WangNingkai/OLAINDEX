<?php

namespace App\Models;

use App\Models\Traits\HelperModel;
use Illuminate\Database\Eloquent\Model;

/**
 * App\ShortUrl
 *
 * @property int $id
 * @property string $original_url
 * @property string $short_code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ShortUrl newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ShortUrl newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ShortUrl query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ShortUrl whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ShortUrl whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ShortUrl whereOriginalUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ShortUrl whereShortCode($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShortUrl whereMap($map)
 */
class ShortUrl extends Model
{
    use  HelperModel;

    const UPDATED_AT = null;

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
