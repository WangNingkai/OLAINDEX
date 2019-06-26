<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $is_folder = Arr::has($this->resource, 'folder');

        $data = [
            'id'                   => $this->resource['id'],
            'eTag'                 => encrypt($this->resource['eTag']),
            'name'                 => $this->resource['name'],
            'size'                 => $is_folder ? '-' : convertSize($this->resource['size']),
            'lastModifiedDateTime' => Carbon::parse($this->resource['lastModifiedDateTime'])->diffForHumans(),
            'thumbnails'           => $this->resource['thumbnails']
        ];

        $merge_data = $is_folder ? [
            'folder' => $this->resource['folder']
        ] : [
            'file' => $this->resource['file'],
            'ext'  => $this->resource['ext']
        ];

        return array_merge($data, $merge_data);
    }
}
