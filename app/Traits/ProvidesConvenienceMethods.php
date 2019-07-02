<?php

namespace App\Traits;

use Auth;

trait ProvidesConvenienceMethods
{
    protected function user()
    {
        return Auth::user();
    }
}
