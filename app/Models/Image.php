<?php

namespace App\Models;

class Image extends Model
{
    protected $columns = [
        'id',
        'admin_id',
        'name',
        'mime',
        'path',
        'width',
        'height',
        'created_at',
        'updated_at',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function getPathAttribute($path)
    {
        return app('filesystem')->disk('public')->url($path);
    }
}
