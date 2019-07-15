<?php

namespace App\Http\Controllers\Admin;

use App\Models\Image;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Http\Controllers\Controller;

class UtilController extends Controller
{
    /**
     * 多图传图
     */
    public function storeImage(Request $request)
    {
        $data = $request->validate([
            'image' => 'required|image'
        ]);

        $file = request()->file('image');

        if (empty($file) || !$file->isValid()) {
            return $this->error(40401);
        }

        $image = (new ImageService($file->path()))->save();

        return $this->success([
            'id'   => $image->id,
            'path' => $image->path
        ]);
    }

    /**
     * 多图删图
     */
    public function destroyImage(Request $request)
    {
        $data = $request->validate([
            'image_ids'   => 'required|array',
            'image_ids.*' => 'integer'
        ]);

        $ids = array_unique($data['image_ids']);

        if (!empty($ids)) {
            $images = Image::whereIn('id', $ids)->where('admin_id', $this->user()->id)->get();

            foreach ($images as $image) {
                $image->delete();
            }
        }

        return $this->success();
    }
}
