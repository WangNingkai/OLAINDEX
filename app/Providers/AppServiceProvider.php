<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Schema;

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
        $sqlFile = install_path('data/database.sqlite');

        if (file_exists($sqlFile) || env('DB_CONNECTION') === 'mysql') {
            Schema::defaultStringLength(191);
        }
    }
}
