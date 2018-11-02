<?php

namespace App\Http\Controllers;

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
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDrive()
    {
        $endpoint = '/me/drive';
        $response = $this->graph->request('get', $endpoint);
        return $response->getBody();
    }

    public function listChildren($itemId)
    {
        $endpoint = "GET /me/drive/items/{$itemId}/children";

    }

    public function getItem($itemId)
    {
        $endpoint = "GET /me/drive/items/{$itemId}";
    }

    public function copy($itemId, $parentItemId)
    {
        $endpoint = "POST /me/drive/items/{$itemId}/copy";
        $body = json_encode([
            'parentReference' => [
                'driveId' => $driveId,
                'id' => $parentItemId
            ],
        ]);
    }

    public function move($itemId, $parentItemId, $itemName)
    {
        $endpoint = "PATCH /me/drive/items/{$itemId}";
        $body = json_encode([
            'parentReference' => [
                'id' => $parentItemId
            ],
            'name' => $itemName
        ]);
    }

    public function mkdir($itemName, $parentItemId)
    {
        $endpoint = "POST /me/drive/items/$parentItemId/children";
        $body = '{"name":"' . $itemName . '","folder":{},"@microsoft.graph.conflictBehavior":"rename"}';
    }

    public function search($query)
    {
        $endpoint = "GET /me/drive/root/search(q='{$query}')";
    }

    public function thumbnails($itemId, $size)
    {
        $endpoint = "/me/drive/items/{$itemId}/thumbnails/0/{$size}";
    }


    /**
     * 创建分享链接
     * @param $itemId
     * @return mixed|null
     */
    public function createShareLink($itemId)
    {
        $endpoint = "/me/drive/items/{$itemId}/createLink";
        $requestBody = '{"type": "view","scope": "anonymous"}';
        $response = $this->graph->requestGraph('post', [$endpoint, $requestBody, []], true);
        return $response;

    }

    /**
     * 获取分享文件列表
     * @return mixed|null
     */
    public function getShareWithMe()
    {
        $endpoint = '/me/drive/sharedWithMe';
        $response = $this->graph->requestGraph('get', $endpoint, true);
        return $response;
    }

    /**
     * 获取分享文件详情
     * @param $driveId
     * @param $itemId
     * @return mixed|null
     */
    public function getShareWithMeDetail($driveId, $itemId)
    {
        $endpoint = "/drives/{$driveId}/items/{$itemId}";
        $response = $this->graph->requestGraph('get', $endpoint, true);
        return $response;
    }

    public function upload($path, $content)
    {
        $path = trim($path, '/');
        $stream = \GuzzleHttp\Psr7\stream_for($content);
        $endpoint = "/me/drive/root:/{$path}:/content";
        $requestBody = $stream;
        $response = $this->graph->requestGraph('put', [$endpoint, $requestBody, []], true);
        return $response;
    }

    public function uploadUrl($path, $url)
    {

    }

    public function createUploadSession($path)
    {
        $id = path2id($path);
        $endpoint = "/me/drive/items/{$id}/createUploadSession";
        $requestBody = json_encode([
            'item' => [
                '@microsoft.graph.conflictBehavior' => 'rename',
            ]
        ]);
        $response = $this->graph->requestGraph('post', [$endpoint, $requestBody, []]);
        return $response;
    }

    public function UploadToSession($url, $file, $offset, $length = 10240)
    {
        $file_size = $this->ReadFileSize($file);
        $content_length = (($offset + $length) > $file_size) ? ($file_size - $offset) : $length;
        $end = $offset + $content_length - 1;
        $content = $this->ReadFileContent($file, $offset, $length);
        $headers = [
            'Content-Length' => $content_length,
            'Content-Content-Range' => "bytes {$offset}-{$end}/{$file_size}",
        ];
        $requestBody = $content;
        $response = $this->graph->requestGraph('put', [$url, $requestBody, $headers]);
        return $response;
    }

    public function UploadSessionStatus($url)
    {
        $response = $this->graph->requestGraph('get', $url);
        return $response;
    }

    public function DeleteUploadSession($url)
    {
        $response = $this->graph->requestGraph('delete', $url);
        return $response;
    }

    /**
     * 读取文件大小
     * @param $path
     * @return bool|int|string
     */
    public function ReadFileSize($path)
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
    public function ReadFileContent($file, $offset, $length)
    {
        $handler = fopen($file, "rb") ?? die('获取文件内容失败');
        fseek($handler, $offset);
        return fread($handler, $length);
    }
}
