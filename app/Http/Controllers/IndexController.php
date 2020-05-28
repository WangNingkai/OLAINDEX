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
use App\Service\GraphResponse;

class IndexController extends BaseController
{
    public function __invoke($id)
    {
        $limit = request()->get('limit', 10);
        if (in_array($limit, [10, 20, 50], false)) {
            $limit = 10;//默认每页显示10条
        }
        $cursor = request()->get('cursor', '');
        $options = [
            '$top' => $limit,
            '$skiptoken' => $cursor,
        ];

        $resp = $this->_request($id, 'get', '/me/drive/root:/tmp:/children', $options);
        $data = $this->_requestNextLink($id,$resp);
        dd($data);
        /*$err = $resp->getError();
        if ($resp->getError() !== null) {
            return response()->json($err);
        }
        $nextLink = $resp->getNextLink();
        $totalCount = $resp->getCount();
        $cursor = '';
        if ($nextLink) {
            $query = parse_query(parse_url($nextLink)['query']);
            $cursor = $query['$skiptoken'];
        }

        $data = $resp->getBody();
        return response()->json([
            'items' => $data,
            'perPage' => $limit,
            'totalCount' => $totalCount,
            'cursor' => $cursor
        ]);*/

    }

    private function _request($id, $method = 'GET', $query = '/me/drive/root/children', $options = [])
    {
        $query .= '?' . build_query($options, false);

        $req = new GraphClient($id);
        $req->setMethod($method)
            ->setQuery($query)
            ->addHeaders([])
            ->attachBody('')
            ->setReturnStream(false);
        return $req->execute();
    }

    /**
     * @param $id
     * @param GraphResponse|array $response
     * @param array $result
     * @return array|mixed
     */
    private function _requestNextLink($id, $response, &$result = [])
    {
        $nextLink = $response->getNextLink();
        if ($nextLink) {
            $baseLength = strlen($response->getRequest()->getBaseUrl());
            $query = substr($nextLink, $baseLength);
            $resp = $this->_request($id, 'get', $query);
            $result = $this->_requestNextLink($id, $resp, $result);
        } else {
            if ($response->getError()) {
                return $result;
            }
            $result = array_merge($response->getBody()['value'], $result);
        }
        return $result;
    }


}
