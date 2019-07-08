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
        if (OneDrive::doesntExist()) {
            $oneDrive->is_default = 1;
        }
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
        $oldData = $oneDrive->getOriginal();

        if (!empty(Arr::get($newData, 'is_default'))) {
            OneDrive::where('id', '!=', $oneDrive->id)->update([
                'is_default' => 0
            ]);
        } elseif (Arr::get($newData, 'is_binded') == false && !empty(Arr::get($oldData, 'is_binded'))) {
            $oneDrive->is_configured = 0;
            $oneDrive->access_token = null;
            $oneDrive->refresh_token = null;
            $oneDrive->access_token_expires = null;
            $oneDrive->client_id = null;
            $oneDrive->client_secret = null;
            $oneDrive->redirect_uri = null;
            $oneDrive->account_type = null;
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
