<?php

namespace App\Observers;

use App\Models\Admin;
use Illuminate\Support\Facades\Cache;

class AdminObserver
{
    public function saving(Admin $admin)
    {
        Cache::forget('admin_settings');
    }
}
