<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Str;
use App\Models\Image;

class ImageService
{
    private $max_width = 1280;
    private $max_height = 0;
    private $object = null;
    private $disk = null;
    private $savedToModel = true;

    public function __construct($object, $savedToModel = true, $max_width = 1280, $max_height = 0)
    {
        if (is_integer($max_width) && $max_width > 0) {
            $this->max_width = $max_width;
        }
        if (is_integer($max_height) && $max_height > 0) {
            $this->max_height = $max_height;
        }

        $this->disk = app('filesystem')->disk('public');
        $this->object = $object;
        $this->savedToModel = $savedToModel;
    }

    public function save($saveAs = '')
    {
        if (is_object($this->object)) {
            $path = $this->object->store(date('Ym') . '/' . date('d'), 'upload');
            $filePath = $this->disk->get($path);
            $img = app('image')->make($filePath);
        } elseif (is_string($this->object)) {
            $path = date('Ym') . '/' . date('d') . '/' . time() . Str::random(8) . '.jpg' ;
            $img = app('image')->make($this->object);
        }

        if ($this->max_width > 0 && $this->max_height == 0) {
            $this->max_width = min($this->max_width, $img->width());
            $img->resize($this->max_width, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        } else {
            $img->fit($this->max_width, $this->max_height);
        }

        if (!empty($saveAs) && file_exists($saveAs)) {
            file_put_contents($saveAs, $img->encode());
        }

        try {
            $this->disk->put($path, $img->encode());
        } catch (Exception $e) {
            return false;
        }

        if ($this->savedToModel) {
            return Image::create([
                'path'     => $path,
                'mime'     => $img->mime(),
                'width'    => $img->width(),
                'height'   => $img->height(),
                'admin_id' => auth('admin')->user()->id
            ]);
        }

        return $path;
    }

    public function delete()
    {
        try {
            if (!empty($this->object)) {
                if ($this->disk->exists($this->object->getOriginal('cover'))) {
                    $this->disk->delete($this->object->getOriginal('cover'));
                    return true;
                } elseif ($this->disk->exists($this->object->getOriginal('path'))) {
                    $this->disk->delete($this->object->getOriginal('path'));
                    return true;
                }
            }
        } catch (Exception $e) {
            return false;
        }

        return false;
    }
}
