<?php

namespace App\Models;

use Illuminate\Support\Str;

class OneDrive extends Model
{
    protected $casts = [
        'admin_id'       => 'integer',
        'is_default'     => 'boolean',
        'is_binded'      => 'boolean',
        'is_configuraed' => 'boolean',
        'settings'       => 'array'
    ];

    protected $columns = [
        'id',
        'admin_id',
        'name',
        'root',
        'is_default',
        'is_binded',
        'is_configuraed',
        'app_version',
        'cover',
        'access_token',
        'refresh_token',
        'access_token_expires',
        'expires',
        'client_id',
        'client_secret',
        'redirect_uri',
        'account_type',
        'settings',
        'created_at',
        'updated_at',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function getCoverAttribute($path)
    {
        return app('filesystem')->disk('public')->url($path);
    }

    public function setCoverAttribute($cover)
    {
        $this->attributes['cover'] = $cover;

        if (Str::startsWith($cover, env('APP_URL'))) {
            $this->attributes['cover'] = str_replace(env('APP_URL'), '', $cover);
        }
    }
}
