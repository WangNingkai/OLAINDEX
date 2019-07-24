<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\OneDrive;
use App\Observers\OneDriveObserver;
use App\Models\Admin;
use App\Observers\AdminObserver;
use App\Models\Image;
use App\Observers\ImageObserver;
use App\Models\Task;
use App\Observers\TaskObserver;

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

        Task::observe(TaskObserver::class);
        Admin::observe(AdminObserver::class);
        Image::observe(ImageObserver::class);
        OneDrive::observe(OneDriveObserver::class);
    }
}
