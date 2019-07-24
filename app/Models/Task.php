<?php

namespace App\Models;

class Task extends Model
{
    const UPDATED_AT = null;

    protected $casts = [
        'completed_at' => 'datetime',
        'failed_at'    => 'datetime'
    ];

    protected $columns = [
        'id',
        'gid',
        'status',
        'path',
        'created_at',
        'completed_at',
        'failed_at',
    ];
}
