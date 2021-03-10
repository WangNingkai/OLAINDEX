<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Service;

use Log;

class OneDrive
{
    /**
     * @var string $accessToken 请求密钥
     */
    private $accessToken;

    /**
     * @var string $restEndpoint 请求接口
     */
    private $restEndpoint;

    /**
     * @var string $apiVersion APi版本
     */
    private $apiVersion;

    /**
     * @var string $sharepoint 是否sharepoint
     */
    private $sharepoint;

    /**
     * @var bool $isBlock 是否服务受限
     */
    private $isBlock = false;

    /**
     * @var integer $blockSec 服务受限重试时间
     */
    private $blockTime = 0;

    /**
     * OneDrive constructor.
     * @param $accessToken
     * @param $restEndpoint
     * @param string $apiVersion
     */
    public function __construct($accessToken, $restEndpoint, $apiVersion = 'v1.0')
    {
        $this->accessToken = $accessToken;
        $this->restEndpoint = $restEndpoint;
        $this->apiVersion = $apiVersion;
    }

    /**
     * 声明SharePoint
     * @param false $status
     * @param string $sp_id
     * @return $this
     */
    public function sharepoint($status = false, $sp_id = ''): OneDrive
    {
        if ($status) {
            $this->sharepoint = $sp_id;
        }
        return $this;
    }

    /**
     * Fetch SharePoint
     * @param $url
     * @return array|mixed|null
     */
    public function fetchSharePoint($url)
    {
        $sp = parse_url($url);
        $host = $sp['host'];
        $path = $sp['path'];
        $query = "/sites/{$host}:$path";
        $resp = $this->_request('get', $query);
        $err = $resp->getError();
        return $err ?? $resp->getBody();
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
        return $err ?? $resp->getBody();
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
        return $err ?? $resp->getBody();
    }

    /**
     * List children
     * @param string $query
     * @param string[] $params
     * @param bool $chuck
     * @return array
     */
    public function fetchList($query = '/', $params = [], $chuck = false): array
    {
        $trans = trans_request_path($query, true, false);
        $query = "/me/drive/root{$trans}children";
        $resp = $this->_request('get', $query, ['isList' => true, 'params' => $params]);
        if ($chuck) {
            $err = $resp->getError();
            return $err ?? $resp->getBody();
        }
        return $this->_requestNextLink($resp);
    }

    /**
     * List children by item id
     * @param $id
     * @param string[] $params
     * @param bool $chuck
     * @return array
     */
    public function fetchListById($id, $params = [], $chuck = false): array
    {
        $query = "/me/drive/items/{$id}/children";
        $resp = $this->_request('get', $query, ['isList' => true, 'params' => $params]);
        if ($chuck) {
            $err = $resp->getError();
            return $err ?? $resp->getBody();
        }
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
        return $err ?? $resp->getBody();
    }

    /**
     * Get item by id
     * @param $id
     * @return array|mixed|null
     */
    public function fetchItemById($id)
    {
        $query = "/me/drive/items/{$id}";
        $resp = $this->_request('get', $query, ['params' => ['expand' => 'thumbnails']]);
        $err = $resp->getError();
        return $err ?? $resp->getBody();
    }

    /**
     * Search items
     * @param string $query
     * @param string $keyword
     * @return array
     */
    public function search($query = '/', $keyword = ''): array
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
        return $err ?? $resp->getBody();
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
        return $err ?? $resp->getBody();
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
        return $err ?? $resp->getBody();
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
        return $err ?? $resp->getBody();
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
        return $err ?? $resp->getBody();
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
        return $err ?? $resp->getBody();
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
        return $err ?? $resp->getBody();
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
        return $err ?? $resp->getBody();
    }

    public function createUploadSession($path, $filename)
    {
        $graphPath = trans_request_path("{$path}/{$filename}", false, false);
        $query = "/me/drive/root:/{$graphPath}:/createUploadSession";
        $body = [
            'item' => [
                '@microsoft.graph.conflictBehavior' => 'fail',
                'name' => $filename,
            ],
        ];
        $resp = $this->_request('post', $query, ['body' => $body]);
        $err = $resp->getError();
        return $err ?? $resp->getBody();
    }

    /**
     * fetch share link
     * @param $id
     * @return array|mixed
     */
    public function share($id)
    {
        $query = "/me/drive/items/{$id}/createLink";
        $body = ['type' => 'view', 'scope' => 'anonymous'];
        $resp = $this->_request('post', $query, ['body' => $body]);
        $err = $resp->getError();
        return $err ?? $resp->getBody();
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
        if (starts_with($path, '/drive/root:')) {
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
     * 是否服务受限
     * @return bool
     */
    public function isBlock(): bool
    {
        return $this->isBlock;
    }

    /**
     * 获取受限重试时间
     * @return integer
     */
    public function getBlockTime(): int
    {
        return $this->blockTime;
    }

    /**
     * Request next page
     * @param GraphResponse $response
     * @param array $result
     * @return array
     */
    private function _requestNextLink($response, &$result = []): array
    {
        if (null !== $response->getError()) {
            return $result;
        }
        $nextLink = $response->getNextLink();
        if (blank($nextLink)) {
            $data = $response->getBody()['value'] ?? [];
            $result = array_merge_recursive($data, $result);
        } else {
            $data = array_merge_recursive($response->getBody()['value'] ?? [], $result);
            $baseLength = strlen($response->getRequest()->getBaseUrl());
            $query = substr($nextLink, $baseLength);
            $params = parse_query(parse_url($nextLink)['query']);
            $resp = $this->_request('GET', $query, ['params' => $params, 'isList' => true]);
            $result = $this->_requestNextLink($resp, $data);
        }
        return $result;
    }

    /**
     * Request graph serve
     * @param string $method
     * @param string $query
     * @param array $options
     * @return GraphResponse|false|\Microsoft\Graph\Http\GraphResponse|mixed|string|null
     */
    private function _request($method = 'GET', $query = '', $options = [])
    {
        if ($this->sharepoint && str_start($query, '/me')) {
            $query = '/sites/' . $this->sharepoint . str_after($query, '/me');
        }

        $headers = array_get($options, 'headers', []);
        $body = array_get($options, 'body', '');
        $params = array_get($options, 'params', []);
        $isList = array_get($options, 'isList', false);
        if ($isList) {
            $pre_params = [
                '$top' => 500,
                '$skiptoken' => '',
                'expand' => 'thumbnails'
            ];
            $params = array_merge($pre_params, $params);
        }

        $query = parse_url($query)['path'] ?? '';
        if (!empty($params)) {
            $query .= '?' . build_query($params, false);
        }
        $body = is_array($body) ? json_encode($body) : $body;
        $req = new GraphClient($this->accessToken, $this->restEndpoint);
        $req->setApiVersion($this->apiVersion)
            ->setMethod($method)
            ->setQuery($query)
            ->addHeaders($headers)
            ->attachBody($body);

        $resp = $req->execute();

        if (null === $resp) {
            abort(500, '网络开小差了，请稍后重试');
        }
        if (blank($resp->getBody())) {
            $flag = 'request_id:' . str_random(10);
            Log::info($flag . ' 请求MsGraph参数', [
                'apiVersion' => 'v1.0',
                'method' => $method,
                'query' => $query,
                'restEndpoint' => $this->restEndpoint,
            ]);
            Log::info($flag . ' 请求MsGraph响应', [$resp->getBody(), $resp->getHeaders(), $resp->getError(), $resp->getStatus()]);
        }

        if (null !== $resp->getError()) {
            $body = $resp->getBody();
            $headers = $resp->getHeaders();
            $retryAfter = (int)array_get($headers, 'Retry-After', 0);
            if ($retryAfter > 0) {
                $this->isBlock = true;
                $this->blockTime = $retryAfter;
            }
            Log::error('请求MsGraph响应错误', [$body, $headers]);
            Log::error('请求参数', [
                'apiVersion' => 'v1.0',
                'method' => $method,
                'query' => $query,
                'restEndpoint' => $this->restEndpoint,
            ]);
        }
        return $resp;
    }
}
