<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $guarded = [];

    protected $casts = [
        'is_binded' => 'boolean',
        'is_tfa'    => 'boolean'
    ];

    protected $columns = [
        'id',
        'name',
        'email',
        'site_name',
        'theme',
        'hotlink_protection',
        'copyright',
        'statistics',
        'tfa_secret',
        'is_binded',
        'is_tfa',
        'email_verified_at',
        'password',
        'remember_token',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function oneDrives()
    {
        return $this->hasMany(OneDrive::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }
}
