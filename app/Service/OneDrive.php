<?php

namespace App\Service;

use App\Entities\ClientConfigEntity;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use ErrorException;

/**
 * Class OneDrive
 * @package App\Service
 */
class OneDrive
{
    /**
     * @var $instance
     */
    private static $instances = [];

    /* @var GraphRequest $graph */
    private $graph;

    /**
     * @param $account
     * @return OneDrive
     */
    public static function getInstance($account): OneDrive
    {
        $account_id = md5(serialize($account));
        if (!array_key_exists($account_id, self::$instances)) {
            self::$instances[$account_id] = new self($account);
        }
        return self::$instances[$account_id];
    }

    /**
     * OneDrive constructor.
     * @param $account
     */
    private function __construct($account)
    {
        $this->initRequest($account);
    }

    /**
     * @param $account
     */
    private function initRequest($account): void
    {
        $accountType = Arr::get($account, 'account_type', 'com');
        $clientConfig = new ClientConfigEntity(CoreConstants::getClientConfig($accountType));
        $baseUrl = $clientConfig->graph_endpoint;
        $apiVersion = $clientConfig->api_version;
        $accessToken = Arr::get($account, 'access_token', '');
        $this->graph = (new GraphRequest())
            ->setAccessToken($accessToken)
            ->setBaseUrl($baseUrl)
            ->setApiVersion($apiVersion);
    }

    /**
     * @param      $method
     * @param      $param
     * @param null $token
     *
     * @return mixed
     * @throws ErrorException
     */
    public function request($method, $param, $token = null)
    {
        $response = $this->graph->request($method, $param, $token);
        if ($response->error) {
            return json_decode($response->getResponseError(), true);
        }
        $headers = json_decode($response->getResponseHeaders(), true);
        $response = json_decode($response->getResponse(), true);

        return [
            'errno' => 0,
            'message' => 'OK',
            'headers' => $headers,
            'data' => $response,
        ];
    }

    /**
     * 获取账户信息
     *
     * @throws ErrorException
     */
    public function getAccountInfo()
    {
        $endpoint = '/me';
        return $this->request('get', $endpoint);
    }

    /**
     * 获取网盘信息
     *
     * @return mixed
     * @throws ErrorException
     */
    public function getDriveInfo()
    {
        $endpoint = '/me/drive';
        return $this->request('get', $endpoint);
    }

    /**
     * 获取网盘资源目录列表
     *
     * @param string $itemId
     * @param string $query
     *
     * @return array|mixed
     * @throws ErrorException
     */
    public function getItemList($itemId = '', $query = '')
    {
        $endpoint = $itemId ? "/me/drive/items/{$itemId}/children{$query}"
            : "/me/drive/root/children{$query}";
        $response = $this->request('get', $endpoint);
        if ($response['errno'] === 0) {
            $response_data = Arr::get($response, 'data');
            $data = $this->getNextLinkList($response_data);
            $format = $this->formatItem($data);
            return $this->response($format);
        }
        return $response;
    }

    /**
     * 通过路径获取网盘资源目录列表
     *
     * @param string $path
     * @param string $query
     *
     * @return array|mixed
     * @throws ErrorException
     */
    public function getItemListByPath($path = '/', $query = '')
    {
        $requestPath = $this->getRequestPath($path);
        $endpoint = $requestPath === '/' ? "/me/drive/root/children{$query}"
            : "/me/drive/root{$requestPath}children{$query}";
        $response = $this->request('get', $endpoint);
        if ($response['errno'] === 0) {
            $response_data = Arr::get($response, 'data');
            $data = $this->getNextLinkList($response_data);
            $format = $this->formatItem($data);
            return $this->response($format);
        }
        return $response;
    }

    /**
     * 获取下一页全部网盘资源目录
     *
     * @param       $list
     * @param array $result
     *
     * @return array
     * @throws ErrorException
     */
    public function getNextLinkList($list, &$result = []): array
    {
        if (Arr::has($list, '@odata.nextLink')) {
            $baseLength = strlen($this->graph->getBaseUrl()) + strlen($this->graph->getApiVersion());
            $endpoint = substr($list['@odata.nextLink'], $baseLength);
            $response = $this->request('get', $endpoint);
            if ($response['errno'] === 0) {
                $data = $response['data'];
            } else {
                $data = [];
            }
            $result = array_merge(
                $list['value'],
                $this->getNextLinkList($data, $result)
            );
        } else {
            $result = array_merge($list['value'], $result);
        }
        return $result;
    }

    /**
     * 根据资源id获取网盘资源
     *
     * @param        $itemId
     * @param string $query
     *
     * @return array|mixed
     * @throws ErrorException
     */
    public function getItem($itemId, $query = '')
    {
        $endpoint = "/me/drive/items/{$itemId}{$query}";
        $response = $this->request('get', $endpoint);
        if ($response['errno'] === 0) {
            $data = Arr::get($response, 'data');
            $format = $this->formatItem($data, false);
            return $this->response($format);
        }
        return $response;
    }

    /**
     * 通过路径获取网盘资源
     *
     * @param        $path
     * @param string $query
     *
     * @return array|mixed
     * @throws ErrorException
     */
    public function getItemByPath($path, $query = '')
    {
        $requestPath = $this->getRequestPath($path);
        $endpoint = "/me/drive/root{$requestPath}{$query}";
        $response = $this->request('get', $endpoint);
        if ($response['errno'] === 0) {
            $data = Arr::get($response, 'data');
            $format = $this->formatItem($data, false);
            return $this->response($format);
        }
        return $response;
    }

    /**
     * 复制资源
     *
     * @param $itemId
     * @param $parentItemId
     *
     * @return mixed
     * @throws ErrorException
     */
    public function copy($itemId, $parentItemId)
    {
        $driveResponse = $this->getDriveInfo();
        if ($driveResponse['errno'] === 0) {
            $driveId = Arr::get($driveResponse, 'data.id');
            $endpoint = "/me/drive/items/{$itemId}/copy";
            $body = json_encode([
                'parentReference' => [
                    'driveId' => $driveId,
                    'id' => $parentItemId,
                ],
            ]);
            $response = $this->request('post', [$endpoint, $body]);
            if ($response['errno'] === 0) {
                $data = [
                    'redirect' => Arr::get($response, 'headers.Location'),
                ];
                return $this->response($data);
            }
            return $response;
        }
        return $driveResponse;
    }

    /**
     * 移动资源
     *
     * @param $itemId
     * @param $parentItemId
     * @param string $itemName
     * @return mixed
     * @throws ErrorException
     */
    public function move($itemId, $parentItemId, $itemName = '')
    {
        $endpoint = "/me/drive/items/{$itemId}";
        $content = [
            'parentReference' => [
                'id' => $parentItemId,
            ],
        ];
        if ($itemName) {
            $content = Arr::add($content, 'name', $itemName);
        }
        $body = json_encode($content);

        return $this->request('patch', [$endpoint, $body]);
    }

    /**
     * 新建目录
     *
     * @param $itemName
     * @param $parentItemId
     * @return mixed
     * @throws ErrorException
     */
    public function mkdir($itemName, $parentItemId)
    {
        $endpoint = "/me/drive/items/$parentItemId/children";
        $body = '{"name":"' . $itemName . '","folder":{},"@microsoft.graph.conflictBehavior":"rename"}';
        return $this->request('post', [$endpoint, $body]);
    }

    /**
     * 新建目录（路径）
     *
     * @param $itemName
     * @param $path
     *
     * @return mixed
     * @throws ErrorException
     */
    public function mkdirByPath($itemName, $path)
    {
        $requestPath = $this->getRequestPath($path);
        $endpoint = $requestPath === '/' ? '/me/drive/root/children'
            : "/me/drive/root{$requestPath}children";
        $body = '{"name":"' . $itemName . '","folder":{},"@microsoft.graph.conflictBehavior":"rename"}';

        return $this->request('post', [$endpoint, $body]);
    }

    /**
     * 删除资源
     *
     * @param        $itemId
     * @param string $eTag
     *
     * @return array|mixed
     * @throws ErrorException
     */
    public function delete($itemId, $eTag = '')
    {
        $endpoint = "/me/drive/items/{$itemId}";
        $headers = $eTag ? ['if-match' => $eTag] : [];
        $response = $this->request('delete', [$endpoint, '', $headers]);
        if ($response['errno'] === 0) {
            return $this->response(['deleted' => true]);
        }
        return $response;
    }

    /**
     * 搜索资源
     *
     * @param $path
     * @param $query
     *
     * @return array|mixed
     * @throws ErrorException
     */
    public function search($path, $query)
    {
        $requestPath = $this->getRequestPath($path);
        $endpoint = $requestPath === '/'
            ? "/me/drive/root/search(q='{$query}')"
            : "/me/drive/root{$requestPath}search(q='{$query}')";
        $response = $this->request('get', $endpoint);
        if ($response['errno'] === 0) {
            $response_data = Arr::get($response, 'data');
            $data = $this->getNextLinkList($response_data);

            $format = $this->formatItem($data);

            return $this->response($format);
        }
        return $response;
    }

    /**
     * 获取缩略图
     *
     * @param $itemId
     * @param $size
     *
     * @return mixed
     * @throws ErrorException
     */
    public function thumbnails($itemId, $size)
    {
        $endpoint = "/me/drive/items/{$itemId}/thumbnails/0/{$size}";
        return $this->request('get', $endpoint);
    }

    /**
     * 获取资源分享直链
     *
     * @param $itemId
     *
     * @return array|mixed
     * @throws ErrorException
     */
    public function createShareLink($itemId)
    {
        $endpoint = "/me/drive/items/{$itemId}/createLink";
        $body = '{"type": "view","scope": "anonymous"}';
        $response = $this->request('post', [$endpoint, $body]);

        if ($response['errno'] === 0) {
            $data = $response['data'];
            $webUrl = Arr::get($data, 'link.webUrl');
            if (Str::contains($webUrl, ['sharepoint.com', 'sharepoint.cn'])) {
                $parse = parse_url($webUrl);
                $domain = "{$parse['scheme']}://{$parse['host']}/";
                $param = Str::after($parse['path'], 'personal/');
                [$userInfo, $res_id] = explode('/', $param);
                $directLink = $domain . 'personal/' . $userInfo . '/_layouts/15/download.aspx?share=' . $res_id;
            } elseif (Str::contains($webUrl, '1drv.ms')) {
                $rep = $this->request('get', $webUrl);
                if ($rep['errno'] === 0) {
                    $directLink = str_replace('redir?', 'download?', $rep['headers']['Location']);
                } else {
                    return $rep;
                }
            } else {
                $directLink = '';
            }
            return $this->response([
                'redirect' => $directLink,
            ]);
        }
        return $response;
    }


    /**
     * 删除资源分享直链
     *
     * @param $itemId
     *
     * @return array|mixed
     * @throws ErrorException
     */
    public function deleteShareLink($itemId)
    {
        $response = $this->getPermission($itemId);
        if ($response['errno'] === 0) {
            $data = $response['data'];
            $permission = Arr::first($data, static function ($value) {
                return $value['roles'][0] === 'read';
            });
            $permissionId = Arr::get($permission, 'id');

            return $this->deletePermission($itemId, $permissionId);
        }
        return $response;
    }


    /**
     * 获取资源权限
     *
     * @param $itemId
     *
     * @return array|mixed
     * @throws ErrorException
     */
    public function getPermission($itemId)
    {
        $endpoint = "/me/drive/items/{$itemId}/permissions";
        $response = $this->request('get', $endpoint);
        if ($response ['errno'] === 0) {
            $data = $response ['data'];

            return $this->response($data['value']);
        }
        return $response;
    }

    /**
     * 删除资源权限
     *
     * @param $itemId
     * @param $permissionId
     *
     * @return array|mixed
     * @throws ErrorException
     */
    public function deletePermission($itemId, $permissionId)
    {
        $endpoint = "/me/drive/items/{$itemId}/permissions/{$permissionId}";
        $response = $this->request('delete', $endpoint);
        if ($response['errno'] === 0) {
            return $this->response(['deleted' => true]);
        }
        return $response;
    }

    /**
     * 上传文件
     * @param $id
     * @param $content
     *
     * @return mixed
     * @throws ErrorException
     */
    public function upload($id, $content)
    {
        $endpoint = "/me/drive/items/{$id}/content";
        $body = $content;
        return $this->request('put', [$endpoint, $body]);
    }

    /**
     * 上传文件到指定路径
     * @param $path
     * @param $content
     *
     * @return mixed
     * @throws ErrorException
     */
    public function uploadByPath($path, $content)
    {
        $requestPath = $this->getRequestPath($path);
        $endpoint = "/me/drive/root{$requestPath}content";
        $body = $content;
        return $this->request('put', [$endpoint, $body]);
    }

    /**
     * 离线上传 （个人账号 50M以下）
     * @param $remote
     * @param $url
     *
     * @return array|mixed
     * @throws ErrorException
     */
    public function uploadUrl($remote, $url)
    {
        $driveResp = $this->getDriveInfo();
        if ($driveResp['errno'] === 0) {
            if ($driveResp['data']['driveType'] !== 'business') {
                $path = $this->getAbsolutePath(dirname($remote));
                // $pathId = $this->pathToItemId($path);
                // $endpoint = "/me/drive/items/{$pathId}/children"; // by id
                $handledPath = $this->encodeUrl(trim($path, '/'));
                $graphPath = empty($handledPath) ? '/' : ":/{$handledPath}:/";
                $endpoint = "/me/drive/root{$graphPath}children";
                $headers = ['Prefer' => 'respond-async'];
                $body = '{"@microsoft.graph.sourceUrl":"' . $url . '","name":"'
                    . pathinfo($remote, PATHINFO_BASENAME) . '","file":{}}';
                $response = $this->request('post', [$endpoint, $body, $headers]);
                if ($response['errno'] === 0) {
                    $data = [
                        'redirect' => $response['headers']['Location'],
                    ];

                    return $this->response($data);
                }
                return $response;
            }
            return $this->response(
                ['driveType' => $driveResp['data']['driveType']],
                400,
                'Account Not Support'
            );
        }
        return $driveResp;
    }

    /**
     * 创建分片上传
     * @param $remote
     *
     * @return mixed
     * @throws ErrorException
     */
    public function createUploadSession($remote)
    {
        $graphPath = $this->getRequestPath($remote);
        $endpoint = "/me/drive/root{$graphPath}createUploadSession";
        $body = json_encode([
            'item' => [
                '@microsoft.graph.conflictBehavior' => 'fail',
            ],
        ]);
        return $this->request('post', [$endpoint, $body]);
    }

    /**
     * 上传分片
     * @param     $url
     * @param     $file
     * @param     $offset
     * @param int $length
     *
     * @return mixed
     * @throws ErrorException
     */
    public function uploadToSession($url, $file, $offset, $length = 5242880)
    {
        $file_size = $this->readFileSize($file);
        $content_length = (($offset + $length) > $file_size) ? ($file_size
            - $offset) : $length;
        $end = (($offset + $length) > $file_size) ? ($file_size - 1)
            : $offset + $content_length - 1;
        $content = $this->readFileContent($file, $offset, $length);
        $headers = [
            'Content-Length' => $content_length,
            'Content-Range' => "bytes {$offset}-{$end}/{$file_size}",
        ];
        $requestBody = $content;
        $response = $this->request(
            'put',
            [$url, $requestBody, $headers, 360]
        );
        return $response;
    }

    /**
     * 获取分片上传状态
     * @param $url
     * @return mixed
     * @throws ErrorException
     */
    public function uploadSessionStatus($url)
    {
        return $this->request('get', $url, false);
    }

    /**
     * 删除分片上传session
     *
     * @param $url
     * @return mixed
     * @throws ErrorException
     */
    public function deleteUploadSession($url)
    {
        return $this->request('delete', $url, false);
    }

    /**
     * 资源id转路径
     *
     * @param      $itemId
     * @param bool $start
     *
     * @return array|mixed
     * @throws ErrorException
     */
    public function itemIdToPath($itemId, $start = false)
    {
        $response = $this->getItem($itemId);
        if ($response['errno'] === 0) {
            $item = $response['data'];
            if (!array_key_exists('path', $item['parentReference']) && $item['name'] === 'root') {
                return $this->response([
                    'path' => '/',
                ]);
            }
            $path = $item['parentReference']['path'];
            if (Str::startsWith($path, '/drive/root:')) {
                $path = Str::after($path, '/drive/root:');
            }

            // 兼容根目录
            if ($path === '') {
                $pathArr = [];
            } elseif ($start) {
                $pathArr = explode('/', $path);
            } else {
                $pathArr = explode('/', $path);
                if (trim($start, '/') !== '') {
                    $pathArr = array_slice($pathArr, 1);
                }
            }


            $pathArr[] = $item['name'];

            $path = $this->getAbsolutePath(implode('/', $pathArr));

            return $this->response([
                'path' => $path,
            ]);
        }
        return $response;
    }

    /**
     * 路径转资源id
     *
     * @param $path
     *
     * @return array|mixed
     * @throws ErrorException
     */
    public function pathToItemId($path)
    {
        $requestPath = $this->getRequestPath($path);
        $endpoint = $requestPath === '/'
            ? '/me/drive/root'
            : '/me/drive/root' . $requestPath;
        $response = $this->request('get', $endpoint);
        if ($response['errno'] === 0) {
            $data = $response['data'];

            return $this->response(['id' => $data['id']]);
        }
        return $response;
    }

    /**
     * 处理url
     *
     * @param $path
     *
     * @return string
     */
    public function encodeUrl($path): string
    {
        $url = [];
        foreach (explode('/', $path) as $key => $value) {
            if (empty(!$value)) {
                $url[] = rawurlencode($value);
            }
        }
        return @implode('/', $url);
    }

    /**
     * 获取格式化请求路径
     *
     * @param      $path
     * @param bool $isFile
     *
     * @return string
     */
    public function getRequestPath($path, $isFile = false): string
    {
        $origin_path = $this->getAbsolutePath($path);
        $query_path = trim($origin_path, '/');
        $query_path = $this->encodeUrl(rawurldecode($query_path));
        $request_path = empty($query_path) ? '/' : ":/{$query_path}:/";
        if ($isFile) {
            return rtrim($request_path, ':/');
        }
        return $request_path;
    }

    /**
     * 转换为绝对路径
     *
     * @param $path
     *
     * @return mixed
     */
    public function getAbsolutePath($path)
    {
        $path = str_replace(['/', '\\', '//'], '/', $path);
        $parts = array_filter(explode('/', $path), 'strlen');
        $absolutes = [];
        foreach ($parts as $part) {
            if ('.' === (string)$part) {
                continue;
            }
            if ('..' === (string)$part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        return str_replace('//', '/', '/' . implode('/', $absolutes) . '/');
    }

    /**
     * 格式化目录数据
     *
     * @param      $response
     * @param bool $isList
     *
     * @return array
     */
    public function formatItem($response, $isList = true): array
    {
        if ($isList) {
            $items = [];
            foreach ($response as $item) {
                if (Arr::has($item, 'file')) {
                    $item['ext'] = strtolower(
                        pathinfo(
                            $item['name'],
                            PATHINFO_EXTENSION
                        )
                    );
                }
                $items[$item['name']] = $item;
            }
            return $items;
        }
        $response['ext'] = strtolower(
            pathinfo(
                $response['name'],
                PATHINFO_EXTENSION
            )
        );
        return $response;
    }

    /**
     * @param $data
     * @param int $errno
     * @param string $msg
     * @return array
     */
    public function response($data, $errno = 0, $msg = 'ok'): array
    {
        return [
            'errno' => $errno,
            'msg' => $msg,
            'data' => $data,
        ];
    }

    /**
     * Read File Size
     *
     * @param $path
     *
     * @return bool|int|string
     */
    public function readFileSize($path)
    {
        if (!file_exists($path)) {
            return false;
        }
        $size = filesize($path);
        if (!($file = fopen($path, 'rb'))) {
            return false;
        }

        //Check if it really is a small file (< 2 GB)
        if ($size >= 0 && fseek($file, 0, SEEK_END) === 0) { //It really is a small file
            fclose($file);
            return $size;
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
     * Read File Content
     *
     * @param $file
     * @param $offset
     * @param $length
     *
     * @return bool|string
     */
    public function readFileContent($file, $offset, $length)
    {
        $handler = fopen($file, 'rb') or die('Failed Get Content');
        fseek($handler, $offset);
        return fread($handler, $length);
    }


    /**
     * 防止实例被克隆（这会创建实例的副本）
     */
    private function __clone()
    {
    }
}
