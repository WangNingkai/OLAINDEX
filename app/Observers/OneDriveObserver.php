<?php

namespace App\Observers;

use App\Models\OneDrive;
use Illuminate\Support\Arr;

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
        $oneDrive->settings = config('onedrive');
    }

    /**
     * Handle the one drive "saving" event.
     *
     * @param  \App\OneDrive  $oneDrive
     * @return void
     */
    public function saving(OneDrive $oneDrive)
    {
        $newData = $oneDrive->getDirty();

        if (!empty($is_default = Arr::get($newData, 'is_default'))) {
            if ($is_default) {
                OneDrive::where('id', '!=', $oneDrive->id)->update([
                    'is_default' => 0
                ]);
            }
        }
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
