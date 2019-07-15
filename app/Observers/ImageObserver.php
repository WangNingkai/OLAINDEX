<?php

namespace App\Observers;

use App\Services\ImageService;
use App\Models\Image;

class ImageObserver
{
    /**
     * Handle the one drive "deleted" event.
     *
     * @param  \App\Models\OneDrive  $image
     * @return void
     */
    public function deleting(Image $image)
    {
        (new ImageService($image))->delete();
    }
}
