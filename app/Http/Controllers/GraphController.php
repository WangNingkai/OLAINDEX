<?php

namespace App\Http\Controllers;

use App\Helpers\Tool;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Stream;
use Illuminate\Http\Request;
use Microsoft\Graph\Graph;

class GraphController extends Controller
{
    /**
     * @var Graph
     */
    public $graph;

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
        $this->graph = new Graph();
        $this->graph->setBaseUrl("https://graph.microsoft.com/")
            ->setApiVersion("v1.0")
            ->setAccessToken(Tool::config('access_token'));
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
     * 发送请求
     * @param $path
     * @param $query
     * @param bool $toArray
     * @return array
     * @throws \Microsoft\Graph\Exception\GraphException
     */
    public function requestGraph($path, $query, $toArray = true)
    {
        try {
            $response = $this->graph->createRequest("GET", '/me/drive/root' . $path . $query)
                ->addHeaders(["Content-Type" => "application/json"])
                ->setReturnType(Stream::class)
                ->execute();
            return $toArray ? $this->toArray($response->getContents()) : $response->getContents();
        } catch (ClientException $e) {
            Tool::showMessage($e->getCode().':'.$e->getMessage(), false);
            return [];
        }
    }

    /**
     * 转换数组
     * @param $response
     * @return array
     */
    public function toArray($response)
    {
        $items = json_decode($response, true);
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
     * @param Request $request
     * @param string $path
     * @param bool $toArray
     * @return array
     * @throws \Microsoft\Graph\Exception\GraphException
     */
    public function testFetchList(Request $request, $path = '' ,$toArray = true)
    {
        $query = $request->get('query', 'children');
        $path = $this->convertPath($path);
        return $this->requestGraph($path, $query, $toArray);
    }

    /**
     * 获取文件
     * @param $itemId
     * @param bool $toArray
     * @return mixed
     * @throws \Microsoft\Graph\Exception\GraphException
     */
    public function testFetchFile($itemId, $toArray = true)
    {
        $itemId = Tool::encrypt($itemId, 'D', Tool::config('password'));
        try {
            $response = $this->graph->createRequest("GET", "/me/drive/items/{$itemId}")
                ->addHeaders(["Content-Type" => "application/json"])
                ->setReturnType(Stream::class)
                ->execute();
            return $toArray ? json_decode($response->getContents(), true) : $response->getContents();
        } catch (ClientException $e) {
                Tool::showMessage($e->getMessage(), false);
                return '';
        }
    }

    /**
     * 获取文件内容
     * @param $itemId
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Microsoft\Graph\Exception\GraphException
     */
    public function testFetchContent($itemId)
    {
        $file = $this->testFetchFile($itemId);
        $url = $file['@microsoft.graph.downloadUrl'];
        try {
            $client = new Client();
            $response = $client->request('get', $url);
            $content = $response->getBody()->getContents();
            return $content;
        } catch (ClientException $e) {
            Tool::showMessage($e->getMessage(), false);
            return '';
        }
    }
}
