<?php

namespace App\Observers;

use App\Models\OneDrive;

class OneDriveObserver
{
    /**
     * Handle the one drive "creating" event.
     *
     * @param  \App\OneDrive  $oneDrive
     * @return void
     */
    public function creating(OneDrive $oneDrive)
    {
        $this->settings = config('onedrive');        
    }

    /**
     * Handle the one drive "updated" event.
     *
     * @param  \App\OneDrive  $oneDrive
     * @return void
     */
    public function updated(OneDrive $oneDrive)
    {
        //
    }

    /**
     * Handle the one drive "deleted" event.
     *
     * @param  \App\OneDrive  $oneDrive
     * @return void
     */
    public function deleted(OneDrive $oneDrive)
    {
        //
    }
}
