<?php
/**
 * This file is part of the wangningkai/OLAINDEX.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Http\Controllers;


use App\Http\Traits\ApiResponseTrait;
use App\Models\ShortUrl;
use Illuminate\Http\Request;

class UrlController extends BaseController
{
    use ApiResponseTrait;

    /**
     * 列表
     * @return mixed
     */
    public function list()
    {
        $urls = ShortUrl::query()
            ->simplePaginate();
        return view('admin.url-list', compact('urls'));
    }
    
    /**
     * 删除
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function delete(Request $request, $id)
    {
        $url = ShortUrl::find($id);
        if (!$url) {
            return $this->fail('链接不存在');
        }
        if ($url->delete()) {
            return $this->success();
        }
        return $this->fail('删除失败');
    }

    /**
     * 清空
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function empty(Request $request)
    {
        ShortUrl::truncate();
        return $this->success();
    }
}
