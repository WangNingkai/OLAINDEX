<?php

namespace App\Http\Controllers;

use App\Helpers\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

/**
 * 文件获取操作
 * Class FetchController
 * @package App\Http\Controllers
 */
class FetchController extends Controller
{
    /**
     * 缓存超时时间 建议10分钟以下，否则会导致资源失效
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
        $this->middleware('checkToken');
        $this->expires = Tool::config('expires', 10);
        $this->root = Tool::config('root', '/');
        $this->show = [
            'stream' => explode(' ', Tool::config('stream')),
            'image' => explode(' ', Tool::config('image')),
            'video' => explode(' ', Tool::config('video')),
            'dash' => explode(' ', Tool::config('dash')),
            'audio' => explode(' ', Tool::config('audio')),
            'code' => explode(' ', Tool::config('code')),
            'doc' => explode(' ', Tool::config('doc')),
        ];
    }

    /**
     * 构造graph请求
     * @param $endpoint
     * @param bool $toArray
     * @return mixed
     */
    public function requestGraph($endpoint, $toArray = true)
    {
        return Cache::remember('one:endpoint:' . $endpoint, $this->expires, function () use ($endpoint, $toArray) {
            $fetch = new RequestController();
            return $fetch->requestGraph('get', $endpoint, $toArray);
        });
    }

    /**
     * 发送请求
     * @param $method
     * @param $url
     * @return mixed
     */
    public function requestHttp($method, $url)
    {
        return Cache::remember('one:url:' . $url, $this->expires, function () use ($method, $url) {
            $fetch = new RequestController();
            return $fetch->requestHttp($method, $url);
        });

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
                if (empty($items['value'])) return [];
                $files = [];
                foreach ($items['value'] as $item) {
                    if (isset($item['file'])) $item['ext'] = strtolower(pathinfo($item['name'], PATHINFO_EXTENSION)); // mimeType显示有误
                    $files[$item['name']] = $item;
                }
                return $files;
            } else return [];
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
            $pathArr = explode('|', $path);
            $url = '';
            foreach ($pathArr as $param) {
                $url .= '/' . $param;
            }
            $dirPath = trim($url, '/');
            if ($this->root == '/')
                $newPath = ':/' . $dirPath . ':/';
            else
                $newPath = ':/' . trim($this->root, '/') . '/' . $dirPath . ':/';
        } else {
            if ($this->root == '' || $this->root == '/')
                $newPath = '/';
            else
                $newPath = ':/' . trim($this->root, '/') . ':/';
        }
        return $newPath;
    }

    /**
     * 获取文件列表
     * @param string $path
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function fetchItemList($path = '')
    {
        $graphPath = $this->convertPath($path);
        $query = 'children';
        $endpoint = '/me/drive/root' . $graphPath . $query;
        $response = $this->requestGraph($endpoint, true);
        $origin_items = $this->formatArray($response);
        if (!empty($origin_items['.password'])) {
            $pass_id = $origin_items['.password']['id'];
            if (Session::has('password:' . $path)) {
                $data = Session::get('password:' . $path);
                $expires = $data['expires'];
                $password = $this->fetchContent($pass_id);
                if ($password != decrypt($data['password']) || time() > $expires) {
                    Session::forget('password:' . $path);
                    Tool::showMessage('密码已过期', false);
                    return view('password', compact('path', 'pass_id'));
                }
            } else return view('password', compact('path', 'pass_id'));
        }
        $this->forbidFolder($origin_items);
        $head = Tool::markdown2Html($this->fetchFilterContent('HEAD.md', $origin_items));
        $readme = Tool::markdown2Html($this->fetchFilterContent('README.md', $origin_items));
        $pathArr = $path ? explode('|', $path) : [];
        if (!session()->has('LogInfo')) $origin_items = $this->filterItem($origin_items, ['README.md', 'HEAD.md', '.password', '.deny']);
        $items = Tool::arrayPage($origin_items, '/list/' . $path, 20);
        return view('one', compact('items', 'origin_items', 'path', 'pathArr', 'head', 'readme'));
    }

    /**
     * 搜索
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function searchItemLIst(Request $request)
    {
        $keywords = $request->get('keywords');
        $query = "search(q='{$keywords}')";
        if ($this->root == '/')
            $endpoint = '/me/drive/root/' . $query;
        else
            $endpoint = '/me/drive/root:/' . trim($this->root, '/') . ':/' . $query;
        $response = $this->requestGraph($endpoint, true);
        $response['value'] = $this->fetchNextLinkItem($response, $response['value']);
        $origin_items = $this->formatArray($response);
        $items = $this->filterFolder($origin_items); // 过滤结果中的文件夹
        $items = Tool::arrayPage($items, '/search', 20);
        return view('search', compact('items'));
    }

    /**
     * 过滤目录中的文件夹
     * @param $items
     * @return mixed
     */
    public function filterFolder($items)
    {
        foreach ($items as $key => $item) {
            if (isset($item['folder'])) unset($items[$key]);
        }
        return $items;
    }

    /**
     * 合并分页数据
     * @param $data
     * @param array $result
     * @return array
     */
    public function fetchNextLinkItem($data, &$result = [])
    {
        if (isset($data['@odata.nextLink'])) {
            $endpoint = mb_strstr($data['@odata.nextLink'], '/me');
            $response = $this->requestGraph($endpoint, true);
            $result = array_merge($response['value'], $this->fetchNextLinkItem($response, $result));
        }
        return $result;
    }

    /**
     * 获取文件
     * @param $itemId
     * @return array
     */
    public function fetchItem($itemId)
    {
        $endpoint = '/me/drive/items/' . $itemId;
        $response = $this->requestGraph($endpoint, true);
        return $this->formatArray($response, false);
    }

    /**
     * 展示文件信息
     * @param $itemId
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function showItem($itemId)
    {
        $item = $this->fetchItem($itemId);
        $path = $item['parentReference']['path'];
        if ($this->root == '/') {
            $key = mb_strpos($path, ':');
            $path = mb_substr($path, $key + 1);
            $pathArr = explode('/', $path);
            unset($pathArr[0]);
        } else {
            $path = mb_strstr($path, $this->root, false, 'utf8');
            $start = mb_strlen($this->root, 'utf8');
            $rest = mb_substr($path, $start, null, 'utf8');
            $pathArr = explode('/', $rest);
        }
        array_push($pathArr, $item['name']);
        $item['thumb'] = $this->fetchThumbUrl($itemId, false);
        $item['path'] = route('download', $item['id']);
        $patterns = $this->show;
        foreach ($patterns as $key => $suffix) {
            if (in_array($item['ext'], $suffix)) {
                $view = 'show.' . $key;
                if (in_array($key, ['stream', 'code'])) {
                    if ($item['size'] > 5 * 1024 * 1024) {
                        Tool::showMessage('文件过大，请下载查看', false);
                        return redirect()->back();
                    } else $item['content'] = $this->requestHttp('get', $item['@microsoft.graph.downloadUrl']);
                }
                if ($key == 'dash') {
                    if (strpos($item['@microsoft.graph.downloadUrl'], "sharepoint.com") == false) return $this->fetchDownload($item['id']);
                    $item['dash'] = str_replace("thumbnail", "videomanifest", $item['thumb']) . "&part=index&format=dash&useScf=True&pretranscode=0&transcodeahead=0";
                }
                if ($key == 'doc') {
                    $url = "https://view.officeapps.live.com/op/view.aspx?src=" . urlencode($item['@microsoft.graph.downloadUrl']);
                    return redirect()->away($url);
                }
                $file = $item;
                return view($view, compact('file', 'pathArr'));
            }
        }
        return $this->fetchDownload($item['id']);
    }


    /**
     * 获取缩略图
     * @param Request $request
     * @param $itemId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function fetchThumb(Request $request, $itemId)
    {
        $size = $request->get('size', 'large');
        $url = $this->fetchThumbUrl($itemId, false, $size);
        $content = $this->requestHttp('get', $url);
        return response($content, 200, [
            'Content-Type' => 'image/png',
        ]);
    }

    /**
     * 获取缩略图原始链接
     * @param $itemId
     * @param bool $redirect
     * @param string $size
     * @return mixed
     */
    public function fetchThumbUrl($itemId, $redirect = true, $size = 'large')
    {
        $endpoint = "/me/drive/items/{$itemId}/thumbnails/0/{$size}";
        $response = $this->requestGraph($endpoint, true);
        if (!$response) abort(404);
        if ($redirect) return redirect()->away($response['url']);
        return $response['url'];
    }

    /**
     * 返回原图
     * @param $itemId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function fetchView($itemId)
    {
        $file = $this->fetchItem($itemId);
        $isBigFile = $file['size'] > 5 * 1024 * 1024 ?: false;
        if ($isBigFile) {
            Tool::showMessage('文件过大，请下载查看', false);
            return redirect()->route('list');
        }
        $url = $file['@microsoft.graph.downloadUrl'];
        $content = $this->requestHttp('get', $url);
        return response($content, 200, [
            'Content-Type' => 'image/png',
        ]);
    }

    /**
     * 获取文件下载信息
     * @param $itemId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function fetchDownload($itemId)
    {
        $file = $this->fetchItem($itemId);
        $url = $file['@microsoft.graph.downloadUrl'];
        return redirect()->away($url);
    }

    /**
     * 获取文件内容
     * @param $itemId
     * @return string
     */
    public function fetchContent($itemId)
    {
        $file = $this->fetchItem($itemId);
        $url = $file['@microsoft.graph.downloadUrl'];
        return $this->requestHttp('get', $url);
    }

    /**
     * 获取过滤文件内容
     * @param $itemName
     * @param $items
     * @return string
     */
    public function fetchFilterContent($itemName, $items)
    {
        if (empty($items[$itemName]))  return '';
        $url = $items[$itemName]['@microsoft.graph.downloadUrl'];
        return $this->requestHttp('get', $url);
    }

    /**
     * 过滤文件
     * @param $items
     * @param $itemName
     * @return mixed
     */
    public function filterItem($items, $itemName)
    {
        if (is_array($itemName)) {
            foreach ($itemName as $item) {
                unset($items[$item]);
            }
        } else unset($items[$itemName]);
        return $items;
    }

    /**
     * 过滤目录
     * @param $items
     */
    public function forbidFolder($items)
    {
        // .deny目录无法访问
        if (!empty($items['.deny'])) {
            if (!Session::has('LogInfo')) {
                Tool::showMessage('目录访问受限，仅管理员可以访问！', false);
                abort(403);
            }
        }
    }

    /**
     * 校验目录密码
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function handlePassword()
    {
        $password = request()->get('password');
        $path = decrypt(request()->get('path'));
        $pass_id = decrypt(request()->get('pass_id'));
        $data = [
            'password' => encrypt($password),
            'expires' => time() + $this->expires * 60, // 目录密码过期时间
        ];
        Session::put('password:' . $path, $data);
        $directory_password = $this->fetchContent($pass_id);
        if ($password == $directory_password)
            return redirect()->route('list', $path);
        else {
            Tool::showMessage('密码错误', false);
            return view('password', compact('path', 'pass_id'));
        }
    }
}
