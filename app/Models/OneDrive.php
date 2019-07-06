<?php

namespace App\Models;

class OneDrive extends Model
{
    protected $casts = [
        'admin_id'      => 'integer',
        'is_default'    => 'boolean',
        'is_binded'     => 'boolean',
        'is_configured' => 'boolean',
        'settings'      => 'array'
    ];

    protected $columns = [
        'id',
        'admin_id',
        'name',
        'root',
        'is_default',
        'is_binded',
        'is_configured',
        'app_version',
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
}
