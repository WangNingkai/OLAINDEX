<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Stream;
use Microsoft\Graph\Graph;

class GraphTool {
    /**
     * 缓存超时时间
     * @var int|mixed|string
     */
    public $expires = 10;

    /**
     * 根目录
     * @var mixed|string
     */
    public $root = '/';

    /**
     * 展示文件数组
     * @var array
     */
    public $show = [];

    /**
     * GraphController constructor.
     */
    public function __construct()
    {
        $this->expires = Tool::config('expires', 10);
        $this->root = Tool::config('root', '/');
        $this->show = [
            'stream' => explode(' ', Tool::config('stream')),
            'image' => explode(' ', Tool::config('image')),
            'video' => explode(' ', Tool::config('video')),
            'audio' => explode(' ', Tool::config('audio')),
            'code' => explode(' ', Tool::config('code')), // php文件由于web服务器原因无法预览
            'doc' => explode(' ', Tool::config('doc')),
        ];
    }

    /**
     * 发送graph请求
     * @param $endpoint
     * @param bool $toArray
     * @return array
     * @throws \Microsoft\Graph\Exception\GraphException
     */
    public function requestGraph($endpoint, $toArray = true)
    {
        try {
            $graph = new Graph();
            $graph->setBaseUrl("https://graph.microsoft.com/")
                ->setApiVersion("v1.0")
                ->setAccessToken(Tool::config('access_token'));
            $response = $graph->createRequest("GET", $endpoint)
                ->addHeaders(["Content-Type" => "application/json"])
                ->setReturnType(Stream::class)
                ->execute();
            return $toArray ? json_decode($response->getContents(), true) : $response->getContents();
        } catch (ClientException $e) {
            Tool::showMessage($e->getCode().':'.$e->getMessage(), false);
            return [];
        }
    }

    /**
     * 发送请求
     * @param $method
     * @param $url
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestHttp($method, $url)
    {
        try {
            $client = new Client();
            $response = $client->request($method, $url);
            $content = $response->getBody()->getContents();
            return $content;
        } catch (ClientException $e) {
            Tool::showMessage($e->getMessage(), false);
            return '';
        }
    }

    /**
     * 列表数组格式转换
     * @param $response
     * @return array
     */
    public function formatArray($response)
    {
        $items = is_array($response) ? $response : json_decode($response, true);
        if (array_key_exists('value', $items) && empty($items['value'])) {
            return [];
        }
        $files = [];
        foreach ($items['value'] as $item) {
            $files[$item['name']] = $item;
        }
        return $files;
    }

    /**
     * 解析路径
     * @param $path
     * @return string
     */
    public function convertPath($path)
    {
        if ($path) {
            if ($path == 'root') {
                if ($this->root == '' || $this->root == '/')
                    $newPath = '/';
                else
                    $newPath = ':/' . $this->root . ':/';
            } else {
                $pathArr = explode('-', $path);
                $url = '';
                foreach ($pathArr as $param) {
                    $url .= '/' . $param;
                }
                $dirPath = trim($url, '/');
                if ($this->root == '/')
                    $newPath = ':/' . $dirPath . ':/';
                else
                    $newPath = ':/' . $this->root . '/' . $dirPath . ':/';
            }
        } else {
            if ($this->root == '' || $this->root == '/')
                $newPath = '/';
            else
                $newPath = ':/' . $this->root . ':/';
        }
        return $newPath;
    }

    /**
     * 获取文件列表
     * @param string $path
     * @param string $query
     * @return array
     * @throws \Microsoft\Graph\Exception\GraphException
     */
    public function fetchItemList($path = '' ,$query = 'children')
    {
        $path = $this->convertPath($path);
        $endpoint = '/me/drive/root' . $path . $query;
        $response =  $this->requestGraph($endpoint, true);
        return $this->formatArray($response);
    }

    /**
     * 获取文件
     * @param $itemId
     * @return mixed
     * @throws \Microsoft\Graph\Exception\GraphException
     */
    public function fetchItem($itemId)
    {
        $endpoint = '/me/drive/items/' . $itemId;
        return $this->requestGraph($endpoint, true);
    }

    /**
     * 获取文件内容
     * @param $itemId
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Microsoft\Graph\Exception\GraphException
     */
    public function fetchContent($itemId)
    {
        $file = $this->fetchItem($itemId);
        $url = $file['@microsoft.graph.downloadUrl'];
        return $this->requestHttp('get',$url);
    }

    /**
     * 获取缩略图
     * @param $itemId
     * @param string $size
     * @return array
     * @throws \Microsoft\Graph\Exception\GraphException
     */
    public function fetchThumb($itemId, $size = 'large')
    {
        $endpoint = "/me/drive/items/{$itemId}/thumbnails/0?select={$size}";
        return $this->requestGraph($endpoint, true);
    }
}
