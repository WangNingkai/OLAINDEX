<?php
/**
 * This file is part of the wangningkai/OLAINDEX.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Service;


class OneDrive
{
    public $id;

    public function __construct($account_id)
    {
        $this->id = $account_id;
    }

    private function _request($method = 'GET', $query = '/me/drive/root/children', $options = [])
    {
        $pre_options = [
            '$top' => 200,
            '$skiptoken' => '',
        ];
        $options = array_merge($pre_options, $options);
        $query = parse_url($query)['path'] ?? '';
        $query .= '?' . build_query($options, false);
        $req = new GraphClient($this->id);
        $req->setMethod($method)
            ->setQuery($query)
            ->addHeaders([])
            ->attachBody('')
            ->setProxy('socks5://127.0.0.1:1080')
            ->setReturnStream(false);
        return $req->execute();
    }

    private function _requestNextLink($response, &$result = [])
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
            $resp = $this->_request('GET', $query, $options);
            $result = $this->_requestNextLink($resp, $data);

        }
        return $result;
    }

    public function fetchInfo()
    {
        $query = '/me/drive';
        $resp = $this->_request('get', $query);
        if (!$resp->getError()) {
            return $resp->getBody();
        }
        return [];
    }

    public function fetchList($query = '/')
    {
        $trans = trans_request_path($query, true, false);
        $query = "/me/drive/root{$trans}children";
        $resp = $this->_request('get', $query);
        return $this->_requestNextLink($resp);
    }

    public function fetchListById($id)
    {
        $query = "/me/drive/items/{$id}/children";
        $resp = $this->_request('get', $query);
        return $this->_requestNextLink($resp);
    }

    public function fetchItem($query = '/')
    {
        $trans = trans_request_path($query, true, true);
        $query = "/me/drive/root{$trans}";
        $resp = $this->_request('get', $query);
        if (!$resp->getError()) {
            return $resp->getBody();
        }
        return [];
    }

    public function fetchItemById($id)
    {
        $query = "/me/drive/items/{$id}";
        $resp = $this->_request('get', $query);
        if (!$resp->getError()) {
            return $resp->getBody();
        }
        return $resp->getError();
    }

    public function copy()
    {

    }

    public function move()
    {

    }

    public function mkdir()
    {

    }

    public function deleteItem()
    {

    }

    public function deleteItemById()
    {

    }

    public function fetchThumbnails()
    {

    }

}
