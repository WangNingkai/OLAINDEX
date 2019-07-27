<?php

namespace App\Observers;

use App\Models\OneDrive;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class OneDriveObserver
{
    /**
     * Handle the one drive "creating" event.
     *
     * @param  \App\Models\OneDrive  $oneDrive
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
     * @param  \App\Models\OneDrive  $oneDrive
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

            $redis = Redis::connection('cache');
            $caches = $redis->keys(config('cache.prefix') . 'instance:onedrive_*');
            $redis->del($caches);
        } elseif (Arr::get($newData, 'is_binded') === 0 && !empty(Arr::get($oldData, 'is_binded'))) {
            $oneDrive->is_configuraed = 0;
            $oneDrive->access_token = null;
            $oneDrive->refresh_token = null;
            $oneDrive->access_token_expires = null;
            $oneDrive->client_id = null;
            $oneDrive->client_secret = null;
            $oneDrive->redirect_uri = null;
            $oneDrive->account_type = null;
        } elseif (
            !empty(Arr::get($newData, 'access_token'))
            && !empty(Arr::get($newData, 'refresh_token'))
            && !empty(Arr::get($newData, 'access_token_expires'))
        ) {
            foreach (['access_token', 'refresh_token', 'access_token_expires'] as $key) {
                if (empty(Arr::get($oldData, $key))) {
                    $oneDrive->is_binded = 1;
                    break;
                }
            }
        }

        if (!empty($newData) && !isset($newData['is_default'])) {
            Cache::forget($oneDrive->is_default ? 'instance:onedrive_0' : 'instance:onedrive_' . $oneDrive->id);
        }

        unset($oneDrive->authorize_url, $oneDrive->access_token_url, $oneDrive->scopes);
    }

    /**
     * Handle the one drive "deleted" event.
     *
     * @param  \App\Models\OneDrive  $oneDrive
     * @return void
     */
    public function deleted(OneDrive $oneDrive)
    {
        if ($oneDrive->cover) {
            $oneDrive->cover->delete();
        }

        $oneDrive->tasks()->delete();
    }
}
