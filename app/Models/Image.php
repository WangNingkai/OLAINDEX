<?php

namespace App\Models;

class Image extends Model
{
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function getPathAttribute($path)
    {
        return app('filesystem')->disk('public')->url($path);
    }
}
