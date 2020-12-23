<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Session;

class BaseController extends Controller
{
    /**
     * 操作成功或者失败的提示
     *
     * @param string $message
     * @param bool $error
     */
    public function showMessage($message = 'ok', $error = false): void
    {
        $alertType = $error ? 'danger' : 'success';
        Session::put('alertMessage', $message);
        Session::put('alertType', $alertType);
    }

    /**
     * 数组分页
     * @param array $items
     * @param int $perPage
     * @param bool $toArray
     * @return mixed
     */
    public function paginate($items = [], $perPage = 10, $toArray = true)
    {
        $pageStart = request()->get('page', 1);
        $offSet = ($pageStart * $perPage) - $perPage;
        $itemsForCurrentPage = collect($items)->lazy()->slice($offSet, $perPage);
        $data = new LengthAwarePaginator(
            $itemsForCurrentPage,
            count($items),
            $perPage,
            Paginator::resolveCurrentPage(),
            ['path' => Paginator::resolveCurrentPath()]
        );

        if ($toArray) {
            $paginated = $data->toArray();

            return [
                'items' => array_values(array_get($paginated, 'data', [])),
                'meta' => [
                    'perPage' => (int)array_get($paginated, 'per_page', 0),
                    'totalCount' => (int)array_get($paginated, 'total', 0),
                    'totalPage' => (int)array_get($paginated, 'last_page', 0),
                    'currentPage' => (int)array_get($paginated, 'current_page', 0),
                ],
            ];
        }
        return $data;
    }
}
