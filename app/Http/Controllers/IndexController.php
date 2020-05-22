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
    public function __invoke()
    {
        $limit = request()->get('limit', 2);
        $offset = request()->get('offset');
        $id = request()->get('id');
        $options = [
            '$top' => $limit,
            '$skiptoken' => $offset,
        ];

        $resp = $this->_request($id, 'get', '/me/drive/root/children', $options);
        $nextLink = $resp->getNextLink();
        $offset = '';
        if ($nextLink) {
            $query = parse_query(parse_url($nextLink)['query']);
            $offset = $query['$skiptoken'];
        }

        $data = $resp->getBody();
        return response()->json([
            'items' => $data['value'],
            'limit' => $limit,
            'offset' => $offset
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
