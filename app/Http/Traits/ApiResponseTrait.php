<?php
/**
 * This file is part of the wangningkai/OLAINDEX.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Http\Traits;

use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponseTrait
{
    public function success($data = [])
    {
        if ($data && $data instanceof LengthAwarePaginator) {
            $pageData = $data->toArray();
            $res['totalCount'] = $pageData['total'];
            $res['totalPage'] = $pageData['last_page'];
            $res['currentPage'] = $pageData['current_page'];
            $res['items'] = $pageData['data'];
            return $this->buildResponse($res);
        }
        return $this->buildResponse($data);
    }

    public function fail($errMsg, $data = [], $errCode = 500)
    {
        return $this->buildResponse($data, $errCode, $errMsg);
    }

    public function buildResponse($data = [], $code = 0, $msg = 'ok')
    {
        $data = [
            'data' => $data,
            'msg' => $msg,
            'code' => $code,
            'error' => $code ? $msg : '',
        ];
        return response()->json($data);
    }
}
