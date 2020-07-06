<?php

namespace App\Models;

use App\Models\Traits\HelperModel;
use Illuminate\Database\Eloquent\Model;

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
