<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
    protected $guarded = [];
    protected $columns = [];

    public function scopeExclude($query, ...$value)
    {
        return $query->select(array_diff($this->columns, $value));
    }

    public function getColumns()
    {
        return $this->columns;
    }
}
