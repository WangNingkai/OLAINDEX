<?php

namespace App\Providers;

use App\Service\OneDrive;
use Illuminate\Support\ServiceProvider;

class OneDriveServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('OneDrive', function () {
            return new OneDrive();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
