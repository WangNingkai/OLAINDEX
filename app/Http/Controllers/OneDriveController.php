<?php

namespace App\Http\Controllers;

use App\Helpers\Constants;
use App\Helpers\Tool;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;

/**
 * OneDrive Graph
 * Class OneDriveController
 * @package App\Http\Controllers
 */
class OneDriveController extends Controller
{
    /**
     * @var $access_token
     */
    public $access_token;

    /**
     * OneDriveController constructor.
     */
    public function __construct()
    {
        $access_token = Tool::config('access_token');;
        $this->access_token = $access_token;
    }

    /**
     * 发送请求
     * @param $method
     * @param $param
     * @param bool $stream
     * @return false|mixed|\Psr\Http\Message\ResponseInterface|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request($method, $param, $stream = true)
    {
        if (is_array($param)) {
            @list($endpoint, $requestBody, $requestHeaders, $timeout) = $param;
            $requestBody = $requestBody ?? '';
            $headers = $requestHeaders ?? [];
            $timeout = $timeout ?? 5;
        } else {
            $endpoint = $param;
            $requestBody = '';
            $headers = [];
            $timeout = 5;
        }
        $baseUrl = Constants::REST_ENDPOINT;
        $apiVersion = Constants::API_VERSION;
        if (stripos($endpoint, "http") === 0) {
            $requestUrl = $endpoint;
        } else {
            $requestUrl = $apiVersion . $endpoint;
        }
        try {
            $clientSettings = [
                'base_uri' => $baseUrl,
                'headers' => array_merge([
                    'Host' => $baseUrl,
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->access_token
                ], $headers)
            ];
            $client = new Client($clientSettings);
            $response = $client->request($method, $requestUrl, [
                'body' => $requestBody,
                'stream' => $stream,
                'timeout' => $timeout,
                'allow_redirects' => [
                    'track_redirects' => true
                ]
            ]);
        } catch (ClientException $e) {
            $response = json_encode(['code' => $e->getCode(), 'msg' => $e->getMessage()]);
        }
        return $response;
    }

    /**
     * 获取盘
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDrive()
    {
        $endpoint = '/me/drive';
        $response = $this->request('get', $endpoint);
        return $this->handleResponse($response);
    }

    /**
     * 获取文件目录列表
     * @param $itemId
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function listChildren($itemId = '')
    {
        $endpoint = $itemId ? "/me/drive/items/{$itemId}/children" : "/me/drive/root/children";
        $response = $this->request('get', $endpoint);
        return $this->handleResponse($response);
    }

    /**
     * 获取文件目录列表
     * @param $path
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function listChildrenByPath($path = '/')
    {
        $endpoint = $path == '/' ? "/me/drive/root/children" : "/me/drive/root{$path}children";
        $response = $this->request('get', $endpoint);
        return $this->handleResponse($response);
    }

    /**
     * @param $list
     * @param array $result
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getNextLinkList($list, &$result = [])
    {
        if (isset($data['@odata.nextLink'])) {
            $endpoint = mb_strstr($list['@odata.nextLink'], '/me');
            $response = $this->request('get', $endpoint);
            $result = array_merge($response['value'], $this->getNextLinkList($response, $result));
        }
        return $result;
    }

    /**
     * 获取文件
     * @param $itemId
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getItem($itemId)
    {
        $endpoint = "/me/drive/items/{$itemId}";
        $response = $this->request('get', $endpoint);
        return $this->handleResponse($response);
    }

    /**
     * @param $itemId
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function download($itemId)
    {
        $endpoint = "/me/drive/items/{$itemId}/content";
        $response = $this->request('get', $endpoint, false);
        if (is_a($response, 'GuzzleHttp\Psr7\Response')) {
            return $response->getHeaderLine('X-Guzzle-Redirect-History');
        } else {
            $response = json_decode($response, true);
            abort($response['code'], $response['msg']);
        }
    }

    /**
     * @param $path
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function downloadByPath($path)
    {
        $endpoint = "/me/drive/root:/{$path}:/content";
        $response = $this->request('get', $endpoint, false);
        if (is_a($response, 'GuzzleHttp\Psr7\Response')) {
            return $response->getHeaderLine('X-Guzzle-Redirect-History');
        } else {
            $response = json_decode($response, true);
            abort($response['code'], $response['msg']);
        }
    }

    /**
     * 获取文件
     * @param $path
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getItemByPath($path)
    {
        $endpoint = "/me/drive/root{$path}";
        $response = $this->request('get', $endpoint);
        return $this->handleResponse($response);
    }

    /**
     * 复制文件返回进度
     * @param $itemId
     * @param $parentItemId
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function copy($itemId, $parentItemId)
    {
        $drive = $this->getDrive();
        $driveId = $drive['id'];
        $endpoint = "/me/drive/items/{$itemId}/copy";
        $body = json_encode([
            'parentReference' => [
                'driveId' => $driveId,
                'id' => $parentItemId
            ],
        ]);
        $response = $this->request('post', [$endpoint, $body], false);
        if (is_a($response, 'GuzzleHttp\Psr7\Response')) {
            return $response->getHeaderLine('X-Guzzle-Redirect-History');
        } else {
            $response = json_decode($response, true);
            abort($response['code'], $response['msg']);
        }
    }

    /**
     * 获取操作进度
     * @param $url
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getMonitorStatus($url)
    {
        $response = $this->request('patch', $url);
        return $this->handleResponse($response);
    }

    /**
     * 移动文件
     * @param $itemId
     * @param $parentItemId
     * @param string $itemName
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function move($itemId, $parentItemId, $itemName)
    {
        $endpoint = "/me/drive/items/{$itemId}";
        $body = json_encode([
            'parentReference' => [
                'id' => $parentItemId
            ],
            'name' => $itemName
        ]);
        $response = $this->request('patch', [$endpoint, $body]);
        return $this->handleResponse($response);
    }

    /**
     * 创建文件夹
     * @param $itemName
     * @param $parentItemId
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function mkdir($itemName, $parentItemId)
    {
        $endpoint = "/me/drive/items/$parentItemId/children";
        $body = '{"name":"' . $itemName . '","folder":{},"@microsoft.graph.conflictBehavior":"rename"}';
        $response = $this->request('post', [$endpoint, $body]);
        return $this->handleResponse($response);
    }

    /**
     * 创建文件夹
     * @param $itemName
     * @param $path
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function mkdirByPath($itemName, $path)
    {
        if ($path == '/')
            $endpoint = "/me/drive/root/children";
        else {
            $item = $this->getItemByPath($path);
            $itemId = $item['id'];
            $endpoint = "/me/drive/items/{$itemId}/children";
        }
        $body = '{"name":"' . $itemName . '","folder":{},"@microsoft.graph.conflictBehavior":"rename"}';
        $response = $this->request('post', [$endpoint, $body]);
        return $this->handleResponse($response);
    }

    /**
     * 删除
     * @param $itemId
     * @param $eTag
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteItem($itemId, $eTag)
    {
        $endpoint = "/me/drive/items/{$itemId}";
        $response = $this->request('delete', [$endpoint, '', ['if-match' => $eTag]]);
        return $this->handleResponse($response);
    }

    /**
     * 搜索
     * @param $path
     * @param $query
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function search($path, $query)
    {
        if ($path == '/')
            $endpoint = "/me/drive/root/search(q='{$query}')";
        else
            $endpoint = '/me/drive/root:/' . trim($path, '/') . ':/' . "search(q='{$query}')";
        $response = $this->request('get', $endpoint);
        return $this->handleResponse($response);
    }

    /**
     * 获取缩略图
     * @param $itemId
     * @param $size
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function thumbnails($itemId, $size)
    {
        $endpoint = "/me/drive/items/{$itemId}/thumbnails/0/{$size}";
        $response = $this->request('get', $endpoint);
        return $this->handleResponse($response)['url'];
    }

    /**
     * 创建分享链接
     * @param $itemId
     * @return false|mixed|\Psr\Http\Message\ResponseInterface|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createShareLink($itemId)
    {
        $endpoint = "/me/drive/items/{$itemId}/createLink";
        $body = '{"type": "view","scope": "anonymous"}';
        $response = $this->request('post', [$endpoint, $body]);
        return $this->handleResponse($response);

    }

    /**
     * 获取分享文件列表
     * @return false|mixed|\Psr\Http\Message\ResponseInterface|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getShareWithMe()
    {
        $endpoint = '/me/drive/sharedWithMe';
        $response = $this->request('get', $endpoint);
        return $this->handleResponse($response);
    }

    /**
     * 获取分享文件详情
     * @param $driveId
     * @param $itemId
     * @return false|mixed|\Psr\Http\Message\ResponseInterface|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getShareWithMeDetail($driveId, $itemId)
    {
        $endpoint = "/drives/{$driveId}/items/{$itemId}";
        $response = $this->request('get', $endpoint);
        return $this->handleResponse($response);
    }

    /**
     * 上传文件（4m及以下）
     * @param $id
     * @param $content
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function upload($id, $content)
    {
        $stream = \GuzzleHttp\Psr7\stream_for($content);
        $endpoint = "/me/drive/items/{$id}/content";
        $requestBody = $stream;
        $response = $this->request('put', [$endpoint, $requestBody]);
        return $this->handleResponse($response);
    }

    /**
     * 上传文件（4m及以下）
     * @param $path
     * @param $content
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function uploadByPath($path, $content)
    {
        $path = trim($path, '/');
        $stream = \GuzzleHttp\Psr7\stream_for($content);
        $endpoint = "/me/drive/root:/{$path}:/content";
        $requestBody = $stream;
        $response = $this->request('put', [$endpoint, $requestBody]);
        return $this->handleResponse($response);
    }

    // todo:个人版离线下载
    public function uploadUrl($path, $url)
    {

    }

    /**
     * 大文件上传创建session
     * @param $path
     * @return false|mixed|\Psr\Http\Message\ResponseInterface|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createUploadSession($path)
    {
        $id = $this->pathToItemId($path);
        $endpoint = "/me/drive/items/{$id}/createUploadSession";
        $body = json_encode([
            'item' => [
                '@microsoft.graph.conflictBehavior' => 'rename',
            ]
        ]);
        $response = $this->request('post', [$endpoint, $body]);
        return $this->handleResponse($response);
    }

    /**
     * 分片上传
     * @param $url
     * @param $file
     * @param $offset
     * @param int $length
     * @return false|mixed|\Psr\Http\Message\ResponseInterface|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function uploadToSession($url, $file, $offset, $length = 10240)
    {
        $file_size = $this->readFileSize($file);
        $content_length = (($offset + $length) > $file_size) ? ($file_size - $offset) : $length;
        $end = $offset + $content_length - 1;
        $content = $this->readFileContent($file, $offset, $length);
        $headers = [
            'Content-Length' => $content_length,
            'Content-Content-Range' => "bytes {$offset}-{$end}/{$file_size}",
        ];
        $requestBody = $content;
        $response = $this->request('put', [$url, $requestBody, $headers, 300]);
        return $this->handleResponse($response);
    }

    /**
     * 分片上传状态
     * @param $url
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function uploadSessionStatus($url)
    {
        $response = $this->request('get', $url);
        return $this->handleResponse($response);
    }

    /**
     * 删除分片上传任务
     * @param $url
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteUploadSession($url)
    {
        $response = $this->request('delete', $url);
        return $this->handleResponse($response);
    }

    /**
     * id转path
     * @param $itemId
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function itemIdToPath($itemId)
    {
        $response = $this->getItem($itemId);
        $item = $this->formatArray($response, false);
        if (!array_key_exists('path', $item['parentReference']) && $item['name'] == 'root') {
            return '/';
        }
        $path = $item['parentReference']['path'];
        if (starts_with($path,'/drive/root:')) {
            $path = str_after($path,'/drive/root:');
        }
        // 兼容根目录
        if ($path == '') {
            $pathArr = [];
        } else {
            $pathArr = explode('/', $path);
            if (trim(Tool::config('root'), '/') != '') {
                $pathArr = array_slice($pathArr, 1);
            }
        }
        array_push($pathArr, $item['name']);
        return trim(implode('/', $pathArr), '/');
    }

    /**
     * path转id
     * @param $path
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function pathToItemId($path)
    {
        $endpoint = $path == '/' ? '/me/drive/root' : '/me/drive/root:/' . trim($path, '/');
        $response = $this->request('get', $endpoint);
        $item = $this->handleResponse($response);
        return $item['id'] ?? false;
    }

    /**
     * 返回body格式化
     * @param $response Response
     * @return mixed
     */
    public function toArray($response)
    {
        return $response = json_decode($response->getBody()->getContents(), true);
    }

    /**
     * 处理响应
     * @param $response
     * @return mixed
     */
    public function handleResponse($response)
    {
        if (!is_a($response, 'GuzzleHttp\Psr7\Response')) {
            $response = json_decode($response, true);
            abort($response['code'], $response['msg']);
        } else {
            return $this->toArray($response);
        }
    }


    /**
     * 文件信息格式化
     * @param $response
     * @param bool $isList
     * @return array
     */
    public function formatArray($response, $isList = true)
    {
        if ($isList) {
            if (array_key_exists('value', $response)) {
                if (empty($response['value'])) return [];
                $files = [];
                foreach ($response['value'] as $item) {
                    if (isset($item['file'])) $item['ext'] = strtolower(pathinfo($item['name'], PATHINFO_EXTENSION));
                    $files[$item['name']] = $item;
                }
                return $files;
            } else return [];
        } else {
            // 兼容文件信息
            $response['ext'] = strtolower(pathinfo($response['name'], PATHINFO_EXTENSION));
            return $response;
        }
    }

    /**
     * 读取文件大小
     * @param $path
     * @return bool|int|string
     */
    public function readFileSize($path)
    {
        if (!file_exists($path))
            return false;
        $size = filesize($path);
        if (!($file = fopen($path, 'rb')))
            return false;
        if ($size >= 0) { //Check if it really is a small file (< 2 GB)
            if (fseek($file, 0, SEEK_END) === 0) { //It really is a small file
                fclose($file);
                return $size;
            }
        }
        //Quickly jump the first 2 GB with fseek. After that fseek is not working on 32 bit php (it uses int internally)
        $size = PHP_INT_MAX - 1;
        if (fseek($file, PHP_INT_MAX - 1) !== 0) {
            fclose($file);
            return false;
        }
        $length = 1024 * 1024;
        $read = '';
        while (!feof($file)) { //Read the file until end
            $read = fread($file, $length);
            $size = bcadd($size, $length);
        }
        $size = bcsub($size, $length);
        $size = bcadd($size, strlen($read));
        fclose($file);
        return $size;
    }

    /**
     * 读取文件内容
     * @param $file
     * @param $offset
     * @param $length
     * @return bool|string
     */
    public function readFileContent($file, $offset, $length)
    {
        $handler = fopen($file, "rb") ?? die('获取文件内容失败');
        fseek($handler, $offset);
        return fread($handler, $length);
    }
}
