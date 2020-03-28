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
     * @param bool $success
     */
    public function showMessage($message = 'ok', $success = true): void
    {
        $alertType = $success ? 'success' : 'danger';
        Session::put('alertMessage', $message);
        Session::put('alertType', $alertType);
    }
    /**
     * 数组分页
     * @param array $items
     * @param int $perPage
     * @return array
     */
    public function paginate($items = [], $perPage = 10)
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

        $paginated = $data->toArray();

        return [
            'items' => $paginated['data'] ?? [],
            'links' => [
                'first' => $paginated['first_page_url'] ?? '',
                'last' => $paginated['last_page_url'] ?? '',
                'prev' => $paginated['prev_page_url'] ?? '',
                'next' => $paginated['next_page_url'] ?? '',
            ],
            'meta' => array_except($paginated, [
                'data',
                'first_page_url',
                'last_page_url',
                'prev_page_url',
                'next_page_url',
            ]),
        ];
    }

}
