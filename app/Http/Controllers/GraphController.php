<?php

namespace App\Http\Controllers;

use App\Helpers\Tool;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Stream;
use Illuminate\Http\Request;
use Microsoft\Graph\Graph;

class GraphController extends Controller
{
    public $graph;

    public $expires = 10;

    public $root = '/';

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
        $this->expires = Tool::config('expires',10);
        $this->root = Tool::config('root','/');
        $this->show = [
            'stream'=> explode(' ',Tool::config('stream')),
            'image' => explode(' ',Tool::config('image')),
            'video' => explode(' ',Tool::config('video')),
            'audio' => explode(' ',Tool::config('audio')),
            'code' => explode(' ',Tool::config('code')), // php文件由于web服务器原因无法预览
            'doc' => explode(' ',Tool::config('doc')),
        ];
    }

    /**
     * @param $path
     * @param $query
     * @param bool $toArray
     * @return array|mixed
     * @throws \Microsoft\Graph\Exception\GraphException
     */
    public function requestGraph($path,$query,$toArray = true)
    {
        $result = [];
        try {
            $response = $this->graph->createRequest("GET", '/me/drive/root'.$path.$query)
                ->addHeaders(["Content-Type" => "application/json"])
                ->setReturnType(Stream::class)
                ->execute();
            $result = $toArray ? $this->toArray($response->getContents()) : $response->getContents();
        } catch (ClientException $e) {
            abort($e->getCode());
        }
        return $result;
    }

    /**
     * 转换数组
     * @param $response
     * @return array
     */
    public function toArray($response)
    {
        $items = json_decode($response,true);
        if (array_key_exists('value',$items) && empty($items['value'])){
            return [];
        }
        $files = [];
        foreach($items['value'] as $item) {
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
                    $newPath =':/'.$this->root.':/';
            } else {
                $pathArr = explode('-',$path);
                $url= '';
                foreach ($pathArr as $param) {
                    $url .= '/'.$param;
                }
                $dirPath = trim($url,'/');
                if ($this->root == '/')
                    $newPath = ':/'. $dirPath .':/';
                else
                    $newPath = ':/'.$this->root.'/'. $dirPath .':/';
            }
        } else {
            if ($this->root == '' || $this->root == '/')
                $newPath = '/';
            else
                $newPath =':/'.$this->root.':/';
        }
        return $newPath;
    }

    public function testFetchItems(Request $request,$path = '')
    {
        $query = $request->get('query','children');
        $path = $this->convertPath($path);
        dd($this->requestGraph($path,$query,true));
//        dd(json_decode($this->requestGraph($path,$query,false),true));
    }

    public function testFetchFile($itemId)
    {
        $response = $this->graph->createRequest("GET", "/me/drive/items/{$itemId}/content")
            ->setReturnType(Stream::class)
            ->execute();
        dd($response->getContents()) ;
    }
}
