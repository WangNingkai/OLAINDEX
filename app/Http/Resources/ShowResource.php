<?php

namespace App\Http\Resources;

use Illuminate\Support\Arr;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\Constants;
use App\Helpers\Tool;

class ShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $file = $this->resource;
        $realPath = $file['realPath'];
        $show = [];

        foreach (['stream', 'image', 'video', 'dash', 'audio', 'code', 'doc'] as $field) {
            $show[$field] = explode(' ', Arr::get(app('onedrive')->settings, $field, config('onedrive.' . $field)));
        }

        foreach ($show as $key => $suffix) {
            if (in_array($file['ext'], $suffix)) {
                $view = 'show.' . $key;
                // 处理文本文件
                if (in_array($key, ['stream', 'code'])) {
                    if ($file['size'] > 5 * 1024 * 1024) {
                        Tool::showMessage('文件过大，请下载查看', false);

                        return redirect()->back();
                    } else {
                        $file['content'] = getFileContent($file['@microsoft.graph.downloadUrl'], false);
                        if ($key === 'stream') {
                            $fileType = empty(Constants::FILE_STREAM[$file['ext']])
                                ? 'application/octet-stream'
                                : Constants::FILE_STREAM[$file['ext']];

                            return response(
                                $file['content'],
                                200,
                                ['Content-type' => $fileType]
                            );
                        }
                    }
                }
                // 处理缩略图
                if (in_array($key, ['image', 'dash', 'video'])) {
                    $file['thumb'] = Arr::get($file, 'thumbnails.0.large.url');
                }
                // dash视频流
                if ($key === 'dash') {
                    if (!strpos(
                        $file['@microsoft.graph.downloadUrl'],
                        'sharepoint.com'
                    )) {
                        return redirect()->away($file['download']);
                    }

                    $replace = str_replace(
                        'thumbnail',
                        'videomanifest',
                        $file['thumb']
                    );
                    $file['dash'] = $replace . '&part=index&format=dash&useScf=True&pretranscode=0&transcodeahead=0';
                }
                // 处理微软文档
                if ($key === 'doc') {
                    $url = 'https://view.officeapps.live.com/op/view.aspx?src=' . urlencode($file['@microsoft.graph.downloadUrl']);

                    return redirect()->away($url);
                }

                $origin_path = rawurldecode(trim(Tool::getAbsolutePath($realPath), '/'));
                $path_array = $origin_path ? explode('/', $origin_path) : [];
                $data = compact('file', 'path_array', 'origin_path');

                return view(config('olaindex.theme') . $view, $data);
            } else {
                $last = end($show);
                if ($last === $suffix) {
                    break;
                }
            }
        }

        return redirect()->away($file['download']);
    }
}
