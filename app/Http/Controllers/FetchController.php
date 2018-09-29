<?php

namespace App\Http\Controllers;

use App\Helpers\Tool;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class FetchController extends Controller
{
    const API_URL = 'https://graph.microsoft.com/';
    const API_VERSION = 'v1.0';

    public $expires = 10;

    public $root = '/';

    public $show = [];

    public function __construct()
    {
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
     * 获取目录
     * @param Request $request
     * @param string $path
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function fetchMenu(Request $request,$path = '')
    {
        $query = $request->get('query','children?select=id,name,size,folder,@microsoft.graph.downloadUrl,lastModifiedDateTime');
        $content = $this->fetchItemsByPath($path,$query);
        if (count($content) > 0) {
            $this->filterFolder($content['data']);
            $head = Tool::markdown2Html($this->fetchFileContent($content['data'],'HEAD.md'));
            $readme = Tool::markdown2Html($this->fetchFileContent($content['data'],'README.md'));
            $items = $this->filterFile($content['data'],['README.md','HEAD.md','.password','.deny']);
        } else {
            $head = '';
            $readme ='';
            $items = [];
            Tool::showMessage('目录为空！',false);
        }
        $pathArr =  $path ? explode('-',$path):[];
        if(count($pathArr) > 0 && $pathArr[0] == 'root') unset($pathArr[0]);
        return view('onedrive',compact('items','head','readme','pathArr','path'));
    }

    /**
     * 获取路径下全部文件
     * @param $path
     * @param string $query
     * @param bool $selected
     * @return mixed
     */
    public function fetchItemsByPath($path, $query = '',$selected = true)
    {
        $query = $query ?? 'children';
        $response = Cache::remember('one:dir:'.$path ?? 'root' . '/' . $query,$this->expires,function() use ($path,$query,$selected) {
            $content = $this->createRequest('get',['path' => $path,'query' => $query]);
            return $this->toArray($content,$selected);
        });
        return $response;
    }

    /**
     * 获取指定文件
     * @param string $path
     * @param string $fileName
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function fetchItem($path = '', $fileName = '')
    {
        if(!$path || !$fileName) abort('404');
        $pathArr = explode('-',$path);
        if($pathArr[0] == 'root') unset($pathArr[0]);
        array_push($pathArr,$fileName);
        $file = [
            'name' => $fileName,
            'path' => route('file',$path.'/'.$fileName),
            'ext' => strtolower(pathinfo($fileName, PATHINFO_EXTENSION))
        ];
        $file['downloadUrl'] = $this->fetchItemInfo($file['path'],'downloadUrl');
        $file['thumb'] = $this->fetchThumb($this->fetchItemInfo($file['path'],'id'));
        $patterns = $this->show;
        foreach ($patterns as $key => $suffix) {
            if(in_array($file['ext'],$suffix)){
                $view = 'show.'.$key;
                if (in_array($key,['stream','code']))
                    $file['content'] = $this->fetchFileContentByUrl($this->fetchItemInfo($file['path'],'downloadUrl'));
                if ($key == 'doc') {
                    $url = "https://view.officeapps.live.com/op/view.aspx?src=".urlencode($this->fetchItemInfo($file['path'],'downloadUrl'));
                    return redirect()->away($url);
                }
                return view($view,compact('file','pathArr'));
            }
        }
        return $this->downloadItem($path,$fileName);
    }

    /**
     * 获取指定文件
     * @param string $path
     * @param string $fileName
     * @return \Illuminate\Http\RedirectResponse
     */
    public function downloadItem($path = '', $fileName = '')
    {
        if(!$path || !$fileName) abort('404');
        $data = $this->fetchItemsByPath($path);
        return redirect()->away($data['data'][$fileName]['downloadUrl']);
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

    /**
     * 过滤文件
     * @param $items
     * @param $file
     * @return mixed
     */
    public function filterFile($items,$file)
    {
        if (is_array($file)) {
            foreach ($file as $item) {
                unset($items[$item]);
            }
        } else {
            unset($items[$file]);
        }
        return $items;
    }

    /**
     * 过滤目录
     * @param $items
     */
    public function filterFolder($items)
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
     * 获取列表中的文件内容
     * @param $items
     * @param $file
     * @return mixed|string
     */
    public function fetchFileContent($items,$file)
    {
        if (empty($items[$file])) {
            return '';
        }
        $url = $items[$file]['downloadUrl'];
        return $this->fetchFileContentByUrl($url);
    }

    /**
     * 获取文件内容
     * @param $url
     * @return mixed
     */
    public function fetchFileContentByUrl($url)
    {
        return Cache::remember('one:content:'.$url,$this->expires,function() use ($url) {
            try {
                $client = new Client();
                $response = $client->request('get',$url);
                $content = $response->getBody()->getContents();
                return $content;
            } catch (ClientException $e) {
                Tool::showMessage($e->getMessage(),false);
                return '';
            }
        });
    }

    /**
     * 获取文件信息
     * @param $url
     * @param $key
     * @return mixed
     */
    public function fetchItemInfo($url,$key)
    {
        $pathArr = explode('/',$url);
        $pathArr = array_reverse($pathArr);
        $path = urldecode($pathArr[1]);
        $fileName = urldecode($pathArr[0]);
        $data = $this->fetchItemsByPath($path);
        return $data['data'][$fileName][$key];
    }

    /**
     * 请求缩略图
     * @param $fileId
     * @param string $size
     * @return mixed
     */
    public function fetchThumb($fileId,$size = 'large')
    {
        $url = self::API_URL.self::API_VERSION."/me/drive/items/{$fileId}/thumbnails/0?select={$size}";
        $response = Cache::remember('one:thumb:'.$fileId,$this->expires,function() use ($url) {
            $data = $this->createRequest('get',['url' => $url]);
            $content = $data->getBody()->getContents();
            return json_decode($content,true);
        });
        return $response;
    }

    /**
     * 发送请求
     * @param $method
     * @param $option
     * @return mixed|\Psr\Http\Message\ResponseInterface|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createRequest($method,$option)
    {
        $allowMethod = ['get','post','put','patch','delete'];
        $allowOptions = ['path','query','url'];
        if (!in_array(strtolower($method),$allowMethod)) exit('请求参数异常');
        foreach ($option as $key => $value) {
            if (!in_array($key,$allowOptions)) {
                exit('请求参数异常');
            }
        }
        if (!empty($option['url']))
            $url = $option['url'];
        else {
            $path = $this->convertPath($option['path']);
            $query = $option['query'] ?? 'children?select=name,size,folder,@microsoft.graph.downloadUrl,lastModifiedDateTime';
            $url = self::API_URL.self::API_VERSION.'/me/drive/root'.$path.$query;
        }
        $response = '';
        try {
            $token = Tool::config('access_token');
            $clientSettings = [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token
                ]
            ];
            $client = new Client($clientSettings);
            $response = $client->request($method, $url ,[
                'stream' =>  true,
                'timeout' => 5
            ]);
        }  catch (ClientException $e) {
            abort($e->getCode(),$e->getMessage());
        }
        return $response;
    }

    /**
     * 转换数组
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param bool $selected
     * @return array
     */
    public function toArray($response,$selected = true)
    {
        $content = $response->getBody()->getContents();
        $data = json_decode($content,true);
        $result =  [];
        if (!empty($data['value'])){
            if(!empty($data['@odata.nextLink'])){
                $result['nextUrl'] = $data['@odata.nextLink'];
            }
            $items = $data['value'];
            foreach($items as $item) {
                if ($selected) {
                    $result['data'][$item['name']] = [
                        'id' => $item['id'],
                        'name' => $item['name'],
                        'size' => $item['size'],
                        'lastModifiedDateTime' => strtotime($item['lastModifiedDateTime']),
                        'downloadUrl' => $item['@microsoft.graph.downloadUrl'] ?? false,
                        'folder' => !empty($item['folder']) ? $item['folder'] : false,
                        'ext' => empty($item['folder']) ? strtolower(pathinfo($item['name'], PATHINFO_EXTENSION)) : false,
                    ];
                } else {
                    $result['data'][$item['name']] = $item;
                }
            }
        }
        return $result;
    }
}
