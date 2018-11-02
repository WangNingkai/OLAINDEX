<?php

namespace App\Http\Controllers;

use GuzzleHttp\Psr7\Response;

/**
 * OneDrive Graph
 * Class OneDriveController
 * @package App\Http\Controllers
 */
class OneDriveController extends Controller
{
    /**
     * @var RequestController
     */
    public $graph;

    /**
     * OneDriveController constructor.
     */
    public function __construct()
    {
        $graph = new RequestController();
        $this->graph = $graph;
    }

    /**
     * 获取盘
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDrive()
    {
        $endpoint = '/me/drive';
        $response = $this->graph->request('get', $endpoint);
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
        $response = $this->graph->request('get', $endpoint);
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
            $response = $this->graph->request('get', $endpoint);
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
        $response = $this->graph->request('get', $endpoint);
        return $this->handleResponse($response);
    }

    /**
     * 复制文件返回进度
     * @param $itemId
     * @param $parentItemId
     * @return mixed
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
        $response = $this->graph->request('post', [$endpoint, $body, [], 5]);
        if (is_a($response, 'GuzzleHttp\Psr7\Response')) {
            return $response->getHeader('Location');
        } else {
            return $response;
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
        $response = $this->graph->request('patch', $url);
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
        $response = $this->graph->request('patch', [$endpoint, $body, [], 5]);
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
        $response = $this->graph->request('post', [$endpoint, $body, [], 5]);
        return $this->handleResponse($response);
    }

    /**
     * 搜索
     * @param $query
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function search($query)
    {
        $endpoint = "/me/drive/root/search(q='{$query}')";
        $response = $this->graph->request('get', $endpoint);
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
        $response = $this->graph->request('get', $endpoint);
        return $this->handleResponse($response);
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
        $response = $this->graph->request('post', [$endpoint, $body, [], 5]);
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
        $response = $this->graph->request('get', $endpoint);
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
        $response = $this->graph->request('get', $endpoint);
        return $this->handleResponse($response);
    }

    /**
     * 上传文件（4m及以下）
     * @param $path
     * @param $content
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function upload($path, $content)
    {
        $path = trim($path, '/');
        $stream = \GuzzleHttp\Psr7\stream_for($content);
        $endpoint = "/me/drive/root:/{$path}:/content";
        $requestBody = $stream;
        $response = $this->graph->request('put', [$endpoint, $requestBody, [], 5]);
        return $this->handleResponse($response);
    }

    // todo:个人版离线下载
    public function uploadUrl($path, $url)
    {

    }

    /**
     * @param $path
     * @return false|mixed|\Psr\Http\Message\ResponseInterface|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createUploadSession($path)
    {
        $id = path2id($path);
        $endpoint = "/me/drive/items/{$id}/createUploadSession";
        $body = json_encode([
            'item' => [
                '@microsoft.graph.conflictBehavior' => 'rename',
            ]
        ]);
        $response = $this->graph->request('post', [$endpoint, $body, [], 5]);
        return $response;
    }

    /**
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
        $response = $this->graph->request('put', [$url, $requestBody, $headers, 300]);
        return $response;
    }

    /**
     * @param $url
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function uploadSessionStatus($url)
    {
        $response = $this->graph->request('get', $url);
        return $response;
    }

    /**
     * @param $url
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteUploadSession($url)
    {
        $response = $this->graph->request('delete', $url);
        return $response;
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

    /**
     * 数据格式化
     * @param $response Response
     * @return mixed
     */
    public function toArray($response)
    {
        return json_decode($response->getBody()->getContents(), true);
    }


    /**
     * 处理响应
     * @param $response
     * @return mixed
     */
    public function handleResponse($response)
    {
        if (!is_a($response, 'GuzzleHttp\Psr7\Response')) {
            return $response;
        } else {
            return $this->toArray($response);
        }
    }
}
