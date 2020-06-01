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
use App\Service\OneDrive;

class IndexController extends BaseController
{
    public function __invoke($id)
    {
        // 文件列表
        $path = request()->get('q', '/');
        $data = (new OneDrive(3))->fetchInfo();
        return response()->json($data);
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

    private function _request($id, $method = 'GET', $query = '/me/drive/root/children')
    {
        $options = [
            '$top' => 200,
            '$skiptoken' => '',
        ];
        $query = parse_url($query)['path'] ?? '';
        $query .= '?' . build_query($options, false);
        $req = new GraphClient($id);
        $req->setMethod($method)
            ->setQuery($query)
            ->addHeaders([])
            ->attachBody('')
            ->setProxy('socks5://127.0.0.1:1080')
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
        if ($response->getError()) {
            return $result;
        }
        $nextLink = $response->getNextLink();
        if (!$nextLink) {
            $data = $response->getBody()['value'];
            $result = array_merge_recursive($data, $result);
        } else {
            $data = array_merge_recursive($response->getBody()['value'], $result);
            $baseLength = strlen($response->getRequest()->getBaseUrl());
            $query = substr($nextLink, $baseLength);
            $options = parse_query(parse_url($nextLink)['query']);
            $resp = $this->_request($id, 'get', $query, $options);
            $result = $this->_requestNextLink($id, $resp, $data);

        }
        return $result;
    }


}
