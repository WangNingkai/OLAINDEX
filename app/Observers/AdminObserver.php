<?php

namespace App\Observers;

use App\Models\Admin;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class AdminObserver
{
    public function saving(Admin $admin)
    {
        Cache::forget('admin_settings');

        $dirty_data = $admin->getDirty();
        $original_data = $admin->getOriginal();

        if (
            !empty($original_tfa_secret = Arr::get($original_data, 'tfa_secret'))
                && Arr::get($dirty_data, 'tfa_secret') != $original_tfa_secret
        ) {
            cookie()->queue(cookie()->forget(config('google2fa.remember_cookie_field')));
        }
    }
}
