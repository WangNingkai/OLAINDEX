<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\OneDrive;
use App\Observers\OneDriveObserver;
use App\Models\Admin;
use App\Observers\AdminObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (app('env') == 'local') {
            \Debugbar::enable();
        }

        Admin::observe(AdminObserver::class);
        OneDrive::observe(OneDriveObserver::class);
    }
}
