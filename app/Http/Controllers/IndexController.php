<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Http\Controllers;


use App\Helpers\Tool;
use App\Service\GraphClient;

class IndexController extends BaseController
{
    public function __invoke($id)
    {
        $limit = request()->get('limit', 20);
        $cursor = request()->get('cursor');
        $options = [
            '$top' => $limit,
            '$skiptoken' => $cursor,
        ];

        $resp = $this->_request($id, 'get', '/me/drive/root/children', $options);
        $nextLink = $resp->getNextLink();
        $totalCount = $resp->getCount();
        $cursor = '';
        if ($nextLink) {
            $query = parse_query(parse_url($nextLink)['query']);
            $cursor = $query['$skiptoken'];
        }

        $data = $resp->getBody();
        return response()->json([
            'items' => $data['value'],
            'perPage' => $limit,
            'totalCount' => $totalCount,
            'cursor' => $cursor
        ]);

    }

    private function _request($id, $method = 'GET', $query = '/me/drive/root/children', $options = [])
    {
        $query .= '?' . build_query($options);

        $req = new GraphClient($id);
        $req->setMethod($method)
            ->setQuery($query)
            ->setReturnStream(false);
        return $req->execute();
    }
}
