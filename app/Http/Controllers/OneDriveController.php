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
        $this->access_token = Tool::config('access_token');
    }

    /**
     * 请求API
     * @param $method
     * @param $param
     * @param bool $stream
     * @return false|mixed|\Psr\Http\Message\ResponseInterface|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestApi($method, $param, $stream = true)
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
        $baseUrl = Tool::config('app_type') == 'com' ? Constants::REST_ENDPOINT : Constants::REST_ENDPOINT_21V;
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
            return $response;
        } catch (ClientException $e) {
            return $this->response('', $e->getCode(), $e->getMessage());
        }
    }

    /**
     * 请求URL
     * @param $method
     * @param $param
     * @param bool $stream
     * @return \Illuminate\Http\JsonResponse|mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestUrl($method, $param, $stream = true)
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
        try {
            $clientSettings = [
                'headers' => $headers
            ];
            $client = new Client($clientSettings);
            $response = $client->request($method, $endpoint, [
                'body' => $requestBody,
                'stream' => $stream,
                'timeout' => $timeout,
                'allow_redirects' => [
                    'track_redirects' => true
                ]
            ]);
            return $response;
        } catch (ClientException $e) {
            return $this->response('', $e->getCode(), $e->getMessage());
        }
    }

    /**
     * 获取个人资料
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getMe()
    {
        $endpoint = '/me';
        $response = $this->requestApi('get', $endpoint);
        return $this->handleResponse($response);
    }

    /**
     * 获取盘
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDrive()
    {
        $endpoint = '/me/drive';
        $response = $this->requestApi('get', $endpoint);
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
        $response = $this->requestApi('get', $endpoint);
        if ($response instanceof Response) {
            $response = json_decode($response->getBody()->getContents(), true);
            $data = $this->getNextLinkList($response);
            $res = $this->formatArray($data);
            return $this->response($res);
        } else {
            return $response;
        }
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
        $response = $this->requestApi('get', $endpoint);
        if ($response instanceof Response) {
            $response = json_decode($response->getBody()->getContents(), true);
            $data = $this->getNextLinkList($response);
            $res = $this->formatArray($data);
            return $this->response($res);
        } else {
            return $response;
        }
    }

    /**
     * @param $list
     * @param array $result
     * @return array|false|mixed|\Psr\Http\Message\ResponseInterface|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getNextLinkList($list, &$result = [])
    {
        if (array_has($list, '@odata.nextLink')) {
            $endpoint = str_after($list['@odata.nextLink'], Constants::REST_ENDPOINT . Constants::API_VERSION);
            $response = $this->requestApi('get', $endpoint);
            $data = json_decode($response->getBody()->getContents(), true);
            $result = array_merge($list['value'], $this->getNextLinkList($data, $result));
        } else {
            $result = array_merge($list['value'], $result);
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
        $response = $this->requestApi('get', $endpoint);
        if ($response instanceof Response) {
            $data = json_decode($response->getBody()->getContents(), true);
            $res = $this->formatArray($data, false);
            return $this->response($res);
        } else {
            return $response;
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
        $response = $this->requestApi('get', $endpoint);
        if ($response instanceof Response) {
            $data = json_decode($response->getBody()->getContents(), true);
            $res = $this->formatArray($data, false);
            return $this->response($res);
        } else {
            return $response;
        }
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
        $drive = Tool::handleResponse($this->getDrive());
        if ($drive['code'] == 200) {
            $driveId = $drive['data']['id'];
            $endpoint = "/me/drive/items/{$itemId}/copy";
            $body = json_encode([
                'parentReference' => [
                    'driveId' => $driveId,
                    'id' => $parentItemId
                ],
            ]);
            $response = $this->requestApi('post', [$endpoint, $body], false);
            if ($response instanceof Response) {
                $data = [
                    'redirect' => $response->getHeaderLine('Location')
                ];
                return $this->response($data);
            } else {
                return $response;
            }
        } else {
            return $this->response('', 400, '获取磁盘信息错误');
        }
    }

    /**
     * 移动文件
     * @param $itemId
     * @param $parentItemId
     * @param string $itemName
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function move($itemId, $parentItemId, $itemName = '')
    {
        $endpoint = "/me/drive/items/{$itemId}";
        $content = [
            'parentReference' => [
                'id' => $parentItemId
            ]
        ];
        if ($itemName)
            $content = array_add($content, 'name', $itemName);
        $body = json_encode($content);
        $response = $this->requestApi('patch', [$endpoint, $body]);
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
        $response = $this->requestApi('post', [$endpoint, $body]);
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
            $endpoint = "/me/drive/root{$path}children";
        }
        $body = '{"name":"' . $itemName . '","folder":{},"@microsoft.graph.conflictBehavior":"rename"}';
        $response = $this->requestApi('post', [$endpoint, $body]);
        return $this->handleResponse($response);
    }

    /**
     * 删除
     * @param $itemId
     * @param $eTag
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteItem($itemId, $eTag = '')
    {
        $endpoint = "/me/drive/items/{$itemId}";
        $headers = $eTag ? ['if-match' => $eTag] : [];
        $response = $this->requestApi('delete', [$endpoint, '', $headers]);
        if ($response instanceof Response) {
            $statusCode = $response->getStatusCode();
            if ($statusCode == 204) {
                return $this->response(['deleted' => true]);
            } else {
                return $this->handleResponse($response);
            }
        } else {
            return $response;
        }
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
        if (trim($path, '/') == '')
            $endpoint = "/me/drive/root/search(q='{$query}')";
        else
            $endpoint = '/me/drive/root:/' . trim($path, '/') . ':/' . "search(q='{$query}')";
        $response = $this->requestApi('get', $endpoint);
        if ($response instanceof Response) {
            $response = json_decode($response->getBody()->getContents(), true);
            $data = $this->getNextLinkList($response);
            $res = $this->formatArray($data);
            return $this->response($res);
        } else {
            return $response;
        }
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
        $response = $this->requestApi('get', $endpoint);
        return $this->handleResponse($response);
    }

    /**
     * 创建分享直链下载
     * @param $itemId
     * @return false|mixed|\Psr\Http\Message\ResponseInterface|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createShareLink($itemId)
    {
        $endpoint = "/me/drive/items/{$itemId}/createLink";
        $body = '{"type": "view","scope": "anonymous"}';
        $response = $this->requestApi('post', [$endpoint, $body]);
        if ($response instanceof Response) {
            $data = json_decode($response->getBody()->getContents(), true);
            $web_url = array_get($data, 'link.webUrl');
            if (str_contains($web_url, ['sharepoint.com', 'sharepoint.cn'])) {
                $parse = parse_url($web_url);
                $domain = "{$parse['scheme']}://{$parse['host']}/";
                $param = str_after($parse['path'], 'personal/');
                $info = explode('/', $param);
                $res_id = $info[1];
                $user_info = $info[0];
                $direct_link = $domain . 'personal/' . $user_info . '/_layouts/15/download.aspx?share=' . $res_id;
            } elseif (str_contains($web_url, '1drv.ms')) {
                $client = new Client();
                try {
                    $request = $client->get($web_url, ['allow_redirects' => false]);
                    $direct_link = str_replace('redir?', 'download?', $request->getHeaderLine('Location'));
                } catch (ClientException $e) {
                    return $this->response('', $e->getCode(), $e->getMessage());
                }
            } else {
                $direct_link = '';
            }
            return $this->response([
                'redirect' => $direct_link
            ]);
        } else {
            return $response;
        }
    }

    /**
     * 删除分享链接
     * @param $itemId
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteShareLink($itemId)
    {
        $result = $this->listPermission($itemId);
        $response = Tool::handleResponse($result);
        if ($response['code'] == 200) {
            $data = $response['data'];
            $permission = array_first($data, function ($value) {
                return $value['roles'][0] == 'read';
            });
            $permissionId = array_get($permission, 'id');
            return $this->deletePermission($itemId, $permissionId);
        } else {
            return $result;
        }
    }

    /**
     * 列举文件权限
     * @param $itemId
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function listPermission($itemId)
    {
        $endpoint = "/me/drive/items/{$itemId}/permissions";
        $response = $this->requestApi('get', $endpoint);
        if ($response instanceof Response) {
            $data = json_decode($response->getBody()->getContents(), true);
            return $this->response($data['value']);
        } else {
            return $response;
        }
    }

    /**
     * 删除指定权限
     * @param $itemId
     * @param $permissionId
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deletePermission($itemId, $permissionId)
    {
        $endpoint = "/me/drive/items/{$itemId}/permissions/{$permissionId}";
        $response = $this->requestApi('delete', $endpoint);
        if ($response instanceof Response) {
            $statusCode = $response->getStatusCode();
            if ($statusCode == 204) {
                return $this->response(['deleted' => true]);
            } else {
                return $this->handleResponse($response);
            }
        } else {
            return $response;
        }
    }

    /**
     * 获取分享文件列表
     * @return false|mixed|\Psr\Http\Message\ResponseInterface|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getShareWithMe()
    {
        $endpoint = '/me/drive/sharedWithMe';
        $response = $this->requestApi('get', $endpoint);
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
        $response = $this->requestApi('get', $endpoint);
        return $this->handleResponse($response);
    }

    /**
     * 更新文件内容上传（4m及以下）
     * @param $id
     * @param $content
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function upload($id, $content)
    {
        $stream = \GuzzleHttp\Psr7\stream_for($content);
        $endpoint = "/me/drive/items/{$id}/content";
        $body = $stream;
        $response = $this->requestApi('put', [$endpoint, $body]);
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
        $stream = \GuzzleHttp\Psr7\stream_for($content);
        $endpoint = "/me/drive/root{$path}content";
        $body = $stream;
        $response = $this->requestApi('put', [$endpoint, $body]);
        return $this->handleResponse($response);
    }

    /**
     * 个人版离线下载 (实验性)
     * @param string $remote 带文件名的远程路径
     * @param string $url 链接
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function uploadUrl($remote, $url)
    {
        $drive = Tool::handleResponse($this->getDrive());
        if ($drive['code'] == 200) {
            if ($drive['data']['driveType'] == 'business') {
                return $this->response(['driveType' => $drive['data']['driveType']], 400, '企业账号无法使用离线下载');
            } else {
                $path = Tool::getAbsolutePath(dirname($remote));
                // $pathId = $this->pathToItemId($path);
                // $endpoint = "/me/drive/items/{$pathId}/children"; // by id
                $handledPath = Tool::handleUrl(trim($path, '/'));
                $graphPath = empty($handledPath) ? '/' : ":/{$handledPath}:/";
                $endpoint = "/me/drive/root{$graphPath}children";
                $headers = ['Prefer' => 'respond-async'];
                $body = '{"@microsoft.graph.sourceUrl":"' . $url . '","name":"' . pathinfo($remote, PATHINFO_BASENAME) . '","file":{}}';
                $response = $this->requestApi('post', [$endpoint, $body, $headers]);
                if ($response instanceof Response) {
                    $data = [
                        'redirect' => $response->getHeaderLine('Location')
                    ];
                    return $this->response($data);
                } else {
                    return $response;
                }
            }
        } else {
            return $drive;
        }
    }

    /**
     * 大文件上传创建session
     * @param $remote
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createUploadSession($remote)
    {
        $endpoint = "/me/drive/root{$remote}createUploadSession";
        $body = json_encode([
            'item' => [
                '@microsoft.graph.conflictBehavior' => 'fail',
            ]
        ]);
        $response = $this->requestApi('post', [$endpoint, $body]);
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
    public function uploadToSession($url, $file, $offset, $length = 5242880)
    {
        $file_size = Tool::readFileSize($file);
        $content_length = (($offset + $length) > $file_size) ? ($file_size - $offset) : $length;
        $end = (($offset + $length) > $file_size) ? ($file_size - 1) : $offset + $content_length - 1;
        $content = Tool::readFileContent($file, $offset, $length);
        $headers = [
            'Content-Length' => $content_length,
            'Content-Range' => "bytes {$offset}-{$end}/{$file_size}",
        ];
        $requestBody = $content;
        $response = $this->requestUrl('put', [$url, $requestBody, $headers, 360]);
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
        $response = $this->requestUrl('get', $url);
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
        $response = $this->requestUrl('delete', $url);
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
        $result = $this->getItem($itemId);
        $response = Tool::handleResponse($result);
        if ($response['code'] == 200) {
            $item = $response['data'];
            if (!array_key_exists('path', $item['parentReference']) && $item['name'] == 'root') {
                return $this->response([
                    'path' => '/'
                ]);
            }
            $path = $item['parentReference']['path'];
            if (starts_with($path, '/drive/root:')) {
                $path = str_after($path, '/drive/root:');
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
            $path = trim(implode('/', $pathArr), '/');
            return $this->response([
                'path' => $path
            ]);
        } else {
            return $result;
        }
    }

    /**
     * path转id
     * @param $path
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function pathToItemId($path)
    {
        $endpoint = $path == '/' ? '/me/drive/root' : '/me/drive/root' . $path;
        $response = $this->requestApi('get', $endpoint);
        if ($response instanceof Response) {
            $response = json_decode($response->getBody()->getContents(), true);
            return $this->response(['id' => $response['id']]);
        } else {
            return $response;
        }
    }

    /**
     * 处理响应
     * @param $response Response|\Illuminate\Http\JsonResponse
     * @return mixed
     */
    public function handleResponse($response)
    {

        if (in_array($response->getStatusCode(), [200, 201, 202, 204])) {
            $data = json_decode($response->getBody()->getContents(), true);
            return $this->response($data);
        } else {
            return $response;
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
            $items = [];
            foreach ($response as $item) {
                if (array_has($item, 'file')) $item['ext'] = strtolower(pathinfo($item['name'], PATHINFO_EXTENSION));
                $items[$item['name']] = $item;
            }
            return $items;
        } else {
            // 兼容文件信息
            $response['ext'] = strtolower(pathinfo($response['name'], PATHINFO_EXTENSION));
            return $response;
        }
    }

    /**
     * 返回
     * @param $data
     * @param string $msg
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function response($data, $code = 200, $msg = 'ok')
    {
        return response()->json([
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        ], $code);
    }
}
