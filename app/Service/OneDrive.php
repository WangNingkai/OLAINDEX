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
        $headers = array_get($options, 'headers', []);
        $body = array_get($options, 'body', '');
        $params = array_get($options, 'params', []);
        $isList = array_get($options, 'isList', false);
        if ($isList) {
            $pre_params = [
                '$top' => 500,
                '$skiptoken' => '',
            ];
            $params = array_merge($pre_params, $params);
        }

        $query = parse_url($query)['path'] ?? '';
        if (!empty($params)) {
            $query .= '?' . build_query($params, false);
        }
        $req = new GraphClient($this->id);
        $req->setMethod($method)
            ->setQuery($query)
            ->addHeaders($headers)
            ->attachBody($body)
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
            $params = parse_query(parse_url($nextLink)['query']);
            $resp = $this->_request('GET', $query, ['params' => $params, 'isList' => true]);
            $result = $this->_requestNextLink($resp, $data);
        }
        return $result;
    }

    public function fetchInfo()
    {
        $query = '/me/drive';
        $resp = $this->_request('get', $query);
        $err = $resp->getError();
        if (!$err) {
            return $resp->getBody();
        }
        return $err;
    }

    public function fetchList($query = '/')
    {
        $trans = trans_request_path($query, true, false);
        $query = "/me/drive/root{$trans}children";
        $resp = $this->_request('get', $query, ['isList' => true]);
        return $this->_requestNextLink($resp);
    }

    public function fetchListById($id)
    {
        $query = "/me/drive/items/{$id}/children";
        $resp = $this->_request('get', $query, ['isList' => true]);
        return $this->_requestNextLink($resp);
    }

    public function fetchItem($query = '/')
    {
        $trans = trans_request_path($query, true, true);
        $query = "/me/drive/root{$trans}";
        $resp = $this->_request('get', $query);
        $err = $resp->getError();
        if (!$err) {
            return $resp->getBody();
        }
        return $err;
    }

    public function fetchItemById($id)
    {
        $query = "/me/drive/items/{$id}";
        $resp = $this->_request('get', $query);
        $err = $resp->getError();
        if (!$err) {
            return $resp->getBody();
        }
        return $err;
    }

    public function search($query = '/', $keyword = '')
    {
        $trans = trans_request_path($query, true, false);
        $query = "/me/drive/root{$trans}search(q='{$keyword}')";
        $resp = $this->_request('get', $query, ['isList' => true]);
        return $this->_requestNextLink($resp);
    }

    public function copy($id, $target_id, $fileName)
    {
        $driveResp = $this->fetchInfo();
        $driveId = array_get($driveResp, 'id', '');
        if (!$driveId) {
            return $driveResp;
        }
        $query = "/me/drive/items/{$id}/copy";
        $body = [
            'parentReference' => [
                'driveId' => $driveId,
                'id' => $target_id
            ],
        ];
        if ($fileName) {
            $body = array_add($body, 'name', $fileName);
        }
        $resp = $this->_request('post', $query, ['body' => $body]);
        $err = $resp->getError();
        if (!$err) {
            return $resp->getBody();
        }
        return $err;

    }

    public function move($id, $target_id, $fileName)
    {
        $query = "/me/drive/items/{$id}";
        $body = [
            'parentReference' => [
                'id' => $target_id
            ],
        ];
        if ($fileName) {
            $body = array_add($body, 'name', $fileName);
        }
        $resp = $this->_request('patch', $query, ['body' => $body]);
        $err = $resp->getError();
        if (!$err) {
            return $resp->getBody();
        }
        return $err;
    }

    public function mkdir($fileName, $target_id)
    {
        $query = "/me/drive/items/{$target_id}/children";
        $body = '{"name":"' . $fileName . '","folder":{},"@microsoft.graph.conflictBehavior":"rename"}';
        $resp = $this->_request('post', $query, ['body' => $body]);
        $err = $resp->getError();
        if (!$err) {
            return $resp->getBody();
        }
        return $err;

    }

    public function deleteItem($id, $eTag = '')
    {
        $query = "/me/drive/items/{$id}";
        $headers = [];
        if ($eTag) {
            $headers = ['if-match' => $eTag];
        }
        $resp = $this->_request('delete', $query, ['headers' => $headers]);
        $err = $resp->getError();
        if (!$err) {
            return $resp->getBody();
        }
        return $err;
    }

    public function fetchThumbnails($id, $size = 'large')
    {
        $query = "/me/drive/items/{$id}/thumbnails/0/{$size}";
        $resp = $this->_request('get', $query);
        $err = $resp->getError();
        if (!$err) {
            return $resp->getBody();
        }
        return $err;
    }

    public function upload($query, $content)
    {
        $trans = trans_request_path($query, true, false);
        $query = "/me/drive/root{$trans}content";
        $resp = $this->_request('post', $query, ['body' => $content]);
        $err = $resp->getError();
        if (!$err) {
            return $resp->getBody();
        }
        return $err;
    }

    public function uploadById($id, $content)
    {
        $query = "/me/drive/items/{$id}/content";
        $resp = $this->_request('post', $query, ['body' => $content]);
        $err = $resp->getError();
        if (!$err) {
            return $resp->getBody();
        }
        return $err;
    }

    public function id2Path($id)
    {
        $resp = $this->fetchItemById($id);
        $id = array_get($resp, 'id', '');
        if (!$id) {
            return $resp;
        }
        if (!array_key_exists('path', $resp['parentReference']) && $resp['name'] === 'root') {
            return '/';
        }
        $path = $resp['parentReference']['path'];
        if (str_starts_with($path, '/drive/root:')) {
            $path = str_after($path, '/drive/root:');
        }

        if ($path === '') {
            $pathArr = [];
        } else {
            $pathArr = explode('/', $path);
        }

        $pathArr[] = $resp['name'];

        $path = trans_absolute_path(implode('/', $pathArr));

        return $path;
    }

    public function path2Id($path)
    {
        $resp = $this->fetchItem($path);
        $id = array_get($resp, 'id', '');
        if (!$id) {
            return $resp;
        }
        return array_get($resp, 'id', '');
    }
}
