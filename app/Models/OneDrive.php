<?php

namespace App\Models;

use App\Helpers\Constants;
use Illuminate\Database\Eloquent\SoftDeletes;

class OneDrive extends Model
{
    use SoftDeletes;

    protected $casts = [
        'admin_id'       => 'integer',
        'cover_id'       => 'integer',
        'is_default'     => 'boolean',
        'is_binded'      => 'boolean',
        'is_configuraed' => 'boolean',
        'settings'       => 'array'
    ];

    protected $columns = [
        'id',
        'admin_id',
        'cover_id',
        'name',
        'root',
        'is_default',
        'is_binded',
        'is_configuraed',
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
        'deleted_at'
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function cover()
    {
        return $this->belongsTo(Image::class, 'cover_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'onedrive_id');
    }

    public function getAuthorizeUrlAttribute($value)
    {
        return $this->account_type == 'com'
            ? Constants::AUTHORITY_URL . Constants::AUTHORIZE_ENDPOINT
            : Constants::AUTHORITY_URL_21V . Constants::AUTHORIZE_ENDPOINT_21V;
    }

    public function getAccessTokenUrlAttribute($value)
    {
        return $this->account_type == 'com'
            ? Constants::AUTHORITY_URL . Constants::TOKEN_ENDPOINT
            : Constants::AUTHORITY_URL_21V . Constants::TOKEN_ENDPOINT_21V;
    }

    public function getScopesAttribute($value)
    {
        return $this->scopes = Constants::SCOPES;
    }
}
