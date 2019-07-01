<?php

namespace App\Models;

class OneDrive extends Model
{
    protected $casts = [
        'admin_id'   => 'integer',
        'is_default' => 'boolean',
        'settings'   => 'array'
    ];

    protected $columns = [
        'id',
        'admin_id',
        'is_default',
        'app_version',
        'access_token',
        'refresh_token',
        'access_token_expires',
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
