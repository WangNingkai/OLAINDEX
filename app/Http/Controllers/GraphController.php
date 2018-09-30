<?php

namespace App\Http\Controllers;

use App\Helpers\Tool;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Stream;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Microsoft\Graph\Graph;

class GraphController extends Controller
{
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
     * @param $method
     * @return array|mixed
     * @throws \Microsoft\Graph\Exception\GraphException
     */
    public function requestGraph($endpoint, $toArray = true, $method = 'get')
    {
        try {
            $graph = new Graph();
            $graph->setBaseUrl("https://graph.microsoft.com/")
                ->setApiVersion("v1.0")
                ->setAccessToken(Tool::config('access_token'));
            $response = $graph->createRequest($method, $endpoint)
                ->addHeaders(["Content-Type" => "application/json"])
                ->setReturnType(Stream::class)
                ->execute();
            return $toArray ? json_decode($response->getContents(), true) : $response->getContents();
        } catch (ClientException $e) {
            Tool::showMessage($e->getCode().': 请检查地址是否正确', false);
            return null;
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
            Tool::showMessage($e->getCode().': 请检查链接是否正确', false);
            return null;
        }
    }

    /**
     * 数组处理
     * @param $response
     * @param bool $isList
     * @return array
     */
    public function formatArray($response, $isList = true)
    {
        if (!$response) abort(404);
        $items = is_array($response) ? $response : json_decode($response, true);
        if ($isList) {
            if (array_key_exists('value', $items)) {
                if (empty($items['value'])) {
                    return [];
                }
                $files = [];
                foreach ($items['value'] as $item) {
                    $item['ext'] = !isset($item['folder']) ? strtolower(pathinfo($item['name'], PATHINFO_EXTENSION)) : false;
                    $files[$item['name']] = $item;
                }
                return $files;
            } else {
                return [];
            }
        } else {
            // 兼容文件信息
            $items['ext'] = strtolower(pathinfo($items['name'], PATHINFO_EXTENSION));
            return $items;
        }
    }

    /**
     * 解析路径
     * @param $path
     * @return string
     */
    public function convertPath($path)
    {
        if ($path) {
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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Microsoft\Graph\Exception\GraphException
     */
    public function testFetchItemList(Request $request, $path = '')
    {
        $graphPath = $this->convertPath($path);
        $query = $request->get('query', 'children');
        $endpoint = '/me/drive/root' . $graphPath . $query;
        $response =  $this->requestGraph($endpoint, true);
        $items =  $this->formatArray($response);
        $this->testFilterFolder($items);
        $head = Tool::markdown2Html($this->testFetchFilterContent('HEAD.md',$items));
        $readme = Tool::markdown2Html($this->testFetchFilterContent('README.md',$items));
        $pathArr =  $path ? explode('-',$path):[];
        $items = $this->testFilterItem($items,['README.md','HEAD.md','.password','.deny']);
        return view('dev',compact('items','path','pathArr','head','readme'));
    }

    public function testFetchItem($itemId)
    {
        $endpoint = '/me/drive/items/' . $itemId;
        $response =  $this->requestGraph($endpoint, true);
        return $this->formatArray($response,false);
    }

    /**
     * 展示文件信息
     * @param $itemId
     * @return \Illuminate\Http\RedirectResponse|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Microsoft\Graph\Exception\GraphException
     */
    public function testShowItem($itemId)
    {
        $endpoint = '/me/drive/items/' . $itemId;
        $response =  $this->requestGraph($endpoint, true);
        $item =  $this->formatArray($response,false);
        $path = $item['parentReference']['path'];
        if ($this->root == '/') {
            $key = mb_strpos($path,':');
            $path = mb_substr($path,$key + 1);
            $pathArr = explode('/', $path);
        } else {
            $path = mb_strstr($path,$this->root,'','utf8');
            $pathArr = explode('/', $path);
        }
        unset($pathArr[0]);
        array_push($pathArr,$item['name']);
        $item['thumb'] = $this->testFetchThumb($item['id']);
        $item['path'] = route('download',$item['id']);
        $patterns = $this->show;
        foreach ($patterns as $key => $suffix) {
            if(in_array($item['ext'],$suffix)){
                $view = 'show.'.$key;
                if (in_array($key,['stream','code']))
                    $item['content'] = $this->requestHttp('get', $item['@microsoft.graph.downloadUrl']);
                if ($key == 'doc') {
                    $url = "https://view.officeapps.live.com/op/view.aspx?src=".urlencode($item['@microsoft.graph.downloadUrl']);
                    return redirect()->away($url);
                }
                $file = $item;
                return view($view,compact('file','pathArr'));
            }
        }
        return $this->testFetchDownload($item['id']);
    }

    /**
     * 获取缩略图
     * @param $itemId
     * @param string $size
     * @return array
     * @throws \Microsoft\Graph\Exception\GraphException
     */
    public function testFetchThumb($itemId, $size = 'large')
    {
        $endpoint = "/me/drive/items/{$itemId}/thumbnails/0?select={$size}";
        return $this->requestGraph($endpoint, true);
    }

    /**
     * 获取文件下载信息
     * @param $itemId
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Microsoft\Graph\Exception\GraphException
     */
    public function testFetchDownload($itemId)
    {
        $file = $this->testFetchItem($itemId);
        $url = $file['@microsoft.graph.downloadUrl'];
        return redirect()->away($url);
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
        $file = $this->testFetchItem($itemId);
        $url = $file['@microsoft.graph.downloadUrl'];
        return $this->requestHttp('get',$url);
    }

    /**
     * 获取过滤文件内容
     * @param $itemName
     * @param $items
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testFetchFilterContent($itemName,$items)
    {
        if (empty($items[$itemName])) {
            return '';
        }
        $url = $items[$itemName]['@microsoft.graph.downloadUrl'];
        return $this->requestHttp('get',$url);
    }

    /**
     * 过滤目录
     * @param $items
     */
    public function testFilterFolder($items)
    {
        // .deny目录无法访问 兼容 .password
        if (!empty($items['.deny']) || !empty($items['.password'])) {
            if (!Session::has('LogInfo')) {
                Tool::showMessage('目录访问受限，仅管理员可以访问！',false);
                abort(403);
            }
        }
    }

    /**
     * 过滤文件
     * @param $items
     * @param $itemName
     * @return mixed
     */
    public function testFilterItem($items,$itemName)
    {
        if (is_array($itemName)) {
            foreach ($itemName as $item) {
                unset($items[$item]);
            }
        } else {
            unset($items[$itemName]);
        }
        return $items;
    }
}
