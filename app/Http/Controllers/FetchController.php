<?php

namespace App\Http\Controllers;

use App\Helpers\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

/**
 * 文件获取操作
 * Class FetchController2
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
     * @param bool $cache
     * @return mixed|null
     */
    public function requestGraph($endpoint, $toArray = true, $cache = true)
    {
        if ($cache) {
            return Cache::remember('one:endpoint:' . $endpoint, $this->expires, function () use ($endpoint, $toArray) {
                $fetch = new RequestController();
                return $fetch->requestGraph('get', $endpoint, $toArray);
            });
        } else {
            $fetch = new RequestController();
            return $fetch->requestGraph('get', $endpoint, $toArray);
        }
    }

    /**
     * 构造http请求
     * @param $method
     * @param $url
     * @param bool $cache
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestHttp($method, $url, $cache = true)
    {
        if ($cache) {
            return Cache::remember('one:url:' . $url, $this->expires, function () use ($method, $url) {
                $fetch = new RequestController();
                return $fetch->requestHttp($method, $url);
            });
        } else {
            $fetch = new RequestController();
            return $fetch->requestHttp($method, $url);
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
     * @param bool $isQueryPath
     * @param bool $isFile
     * @param bool $isDownload
     * @return string
     */
    public function convertPath($path, $isQueryPath = true, $isFile = false, $isDownload = false)
    {

        $origin_path = trim(urldecode($path), '/');
        if (!$isDownload)
            $query_path = mb_substr($origin_path, 5);
        else
            $query_path = mb_substr($origin_path, 9);
        if (!$isQueryPath) return $query_path;
        if ($query_path) {
            if ($this->root == '/') {
                $request_path = ':/' . $query_path . ':/';
            } else
                $request_path = ':/' . trim($this->root, '/') . '/' . $query_path . ':/';
        } else {
            if ($this->root == '' || $this->root == '/')
                $request_path = '/';
            else
                $request_path = ':/' . trim($this->root, '/') . ':/';
        }
        if ($isFile) {
            return rtrim($request_path, ':/');
        }
        return $request_path;
    }

    /**
     * 获取文件
     * @param Request $request
     * @param bool $isDownload
     * @return array
     */
    public function getFile(Request $request, $isDownload = false)
    {
        $graphPath = $this->convertPath($request->getPathInfo(), true, true, $isDownload);
        $endpoint = '/me/drive/root' . $graphPath;
        $response = $this->requestGraph($endpoint, true);
        return $this->formatArray($response, false);
    }

    /**
     * @param $id
     * @return array
     */
    public function getFileById($id)
    {
        $endpoint = '/me/drive/items/' . $id;
        $response = $this->requestGraph($endpoint, true);
        return $this->formatArray($response, false);
    }

    /**
     * 获取缩略图
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getThumb(Request $request, $id)
    {
        $size = $request->get('size', 'large');
        $url = $this->getThumbUrl($id, false, $size);
        $content = $this->requestHttp('get', $url);
        return response($content, 200, [
            'Content-Type' => 'image/png',
        ]);
    }

    /**
     * 获取缩略图原始链接
     * @param $id
     * @param bool $redirect
     * @param string $size
     * @return mixed
     */
    public function getThumbUrl($id, $redirect = true, $size = 'large')
    {
        $endpoint = "/me/drive/items/{$id}/thumbnails/0/{$size}";
        $response = $this->requestGraph($endpoint, true);
        if (!$response) abort(404);
        if ($redirect) return redirect()->away($response['url']);
        return $response['url'];
    }

    /**
     * 获取内容
     * @param $url
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getContent($url)
    {
        return $this->requestHttp('get', $url);
    }

    /**
     * ID获取内容
     * @param $id
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getContentById($id)
    {
        $file = $this->getFileById($id);
        $url = $file['@microsoft.graph.downloadUrl'];
        return $this->requestHttp('get', $url);
    }

    /**
     * 获取过滤文件内容
     * @param $filename
     * @param $items
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getContentByName($filename, $items)
    {
        if (empty($items[$filename])) return '';
        $url = $items[$filename]['@microsoft.graph.downloadUrl'];
        return $this->requestHttp('get', $url);
    }

    /**
     * 合并分页数据
     * @param $data
     * @param array $result
     * @return array
     */
    public function getNextLinkList($data, &$result = [])
    {
        if (isset($data['@odata.nextLink'])) {
            $endpoint = mb_strstr($data['@odata.nextLink'], '/me');
            $response = $this->requestGraph($endpoint, true);
            $result = array_merge($response['value'], $this->getNextLinkList($response, $result));
        }
        return $result;
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
     * 过滤文件
     * @param $items
     * @param $itemName
     * @return mixed
     */
    public function filterFiles($items, $itemName)
    {
        if (is_array($itemName)) {
            foreach ($itemName as $item) {
                unset($items[$item]);
            }
        } else unset($items[$itemName]);
        return $items;
    }

    /**
     * 过滤禁用目录
     * @param $items
     */
    public function filterForbidFolder($items)
    {
        // .deny目录无法访问
        if (!empty($items['.deny'])) {
            if (!Session::has('LogInfo')) {
                Tool::showMessage('目录访问受限，仅管理员可以访问！', false);
                abort(403);
            }
        }
    }
}
