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
    /**
     * @var int $id account_id
     */
    public $id;

    /**
     * Bind account
     * @param $account_id
     * @return $this
     */
    public function account($account_id): OneDrive
    {
        $this->id = $account_id;
        return $this;
    }

    /**
     * Get drive
     * @return array|mixed|null
     */
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

    /**
     * Get profile
     * @return array|mixed|null
     */
    public function fetchMe()
    {
        $query = '/me';
        $resp = $this->_request('get', $query);
        $err = $resp->getError();
        if (!$err) {
            return $resp->getBody();
        }
        return $err;
    }

    /**
     * List children
     * @param string $query
     * @return array
     */
    public function fetchList($query = '/')
    {
        $trans = trans_request_path($query, true, false);
        $query = "/me/drive/root{$trans}children";
        $resp = $this->_request('get', $query, ['isList' => true, 'params' => ['expand' => 'thumbnails']]);
        return $this->_requestNextLink($resp);
    }

    /**
     * List children by item id
     * @param $id
     * @return array
     */
    public function fetchListById($id)
    {
        $query = "/me/drive/items/{$id}/children";
        $resp = $this->_request('get', $query, ['isList' => true, 'params' => ['expand' => 'thumbnails']]);
        return $this->_requestNextLink($resp);
    }

    /**
     * Get item
     * @param string $query
     * @return array|mixed|null
     */
    public function fetchItem($query = '/')
    {
        $trans = trans_request_path($query, true, true);
        $query = "/me/drive/root{$trans}";
        $resp = $this->_request('get', $query, ['params' => ['expand' => 'thumbnails']]);
        $err = $resp->getError();
        if (!$err) {
            return $resp->getBody();
        }
        return $err;
    }

    /**
     * Get item by id
     * @param string $id
     * @return array|mixed|null
     */
    public function fetchItemById($id)
    {
        $query = "/me/drive/items/{$id}";
        $resp = $this->_request('get', $query, ['params' => ['expand' => 'thumbnails']]);
        $err = $resp->getError();
        if (!$err) {
            return $resp->getBody();
        }
        return $err;
    }

    /**
     * Search items
     * @param string $query
     * @param string $keyword
     * @return array
     */
    public function search($query = '/', $keyword = '')
    {
        $trans = trans_request_path($query, true, false);
        $query = "/me/drive/root{$trans}search(q='{$keyword}')";
        $resp = $this->_request('get', $query, ['isList' => true]);
        return $this->_requestNextLink($resp);
    }

    /**
     * Copy item
     * @param $id
     * @param $target_id
     * @param $fileName
     * @return array|mixed|null
     */
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

    /**
     * Move item
     * @param $id
     * @param $target_id
     * @param $fileName
     * @return array|mixed|null
     */
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

    /**
     * Create folder
     * @param $fileName
     * @param $target_id
     * @return array|mixed|null
     */
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

    /**
     * Delete item
     * @param $id
     * @param string $eTag
     * @return array|mixed|null
     */
    public function delete($id, $eTag = '')
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

    /**
     * Get thumbnails
     * @param $id
     * @param string $size
     * @return array|mixed|null
     */
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

    /**
     * Upload
     * @param $query
     * @param $content
     * @return array|mixed|null
     */
    public function upload($query, $content)
    {
        $trans = trans_request_path($query, true, false);
        $query = "/me/drive/root{$trans}content";
        $resp = $this->_request('put', $query, ['body' => $content]);
        $err = $resp->getError();
        if (!$err) {
            return $resp->getBody();
        }
        return $err;
    }

    /**
     * Upload by id
     * @param $id
     * @param $content
     * @return array|mixed|null
     */
    public function uploadById($id, $content)
    {
        $query = "/me/drive/items/{$id}/content";
        $resp = $this->_request('put', $query, ['body' => $content]);
        $err = $resp->getError();
        if (!$err) {
            return $resp->getBody();
        }
        return $err;
    }

    /**
     * Upload from parent item
     * @param $parentId
     * @param $filename
     * @param $content
     * @return array|mixed|null
     */
    public function uploadByParentId($parentId, $filename, $content)
    {
        $query = "/me/drive/items/{$parentId}:/{$filename}:/content";
        $resp = $this->_request('put', $query, ['body' => $content]);
        $err = $resp->getError();
        if (!$err) {
            return $resp->getBody();
        }
        return $err;
    }

    /**
     * Item id to item path
     * @param $id
     * @return array|mixed|string|null
     */
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

    /**
     * Item path to item id
     * @param $path
     * @return array|mixed|null
     */
    public function path2Id($path)
    {
        $resp = $this->fetchItem($path);
        $id = array_get($resp, 'id', '');
        if (!$id) {
            return $resp;
        }
        return array_get($resp, 'id', '');
    }

    /**
     * Request graph serve
     * @param string $method
     * @param string $query
     * @param array $options
     * @return GraphResponse|false|\Microsoft\Graph\Http\GraphResponse|mixed|string|null
     */
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
//            ->setProxy('socks5://127.0.0.1:1080')
            ->setReturnStream(false);
        return $req->execute();
    }

    /**
     * Request next page
     * @param $response
     * @param array $result
     * @return array
     */
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
}
