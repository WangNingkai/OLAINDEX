<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * Class OneDrive
 * @package App\Helpers
 */
class OneDrive
{
    /**
     * @var $access_token
     */
    public $access_token;

    /**
     * @var $base_url
     */
    public $base_url;

    /**
     * @var $api_version
     */
    public $api_version;

    /**
     * OneDrive constructor.
     */
    public function __construct()
    {
        $this->access_token = Tool::config('access_token');
        $this->base_url = Tool::config('account_type') == 'com' ? Constants::REST_ENDPOINT : Constants::REST_ENDPOINT_21V;
        $this->api_version = Constants::API_VERSION;
    }

    /**
     * Request API
     * @param $method
     * @param $param
     * @param bool $stream
     * @return false|mixed|\Psr\Http\Message\ResponseInterface|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function requestApi($method, $param, $stream = true)
    {
        $od = new self();
        if (is_array($param)) {
            @list($endpoint, $requestBody, $requestHeaders, $timeout) = $param;
            $body = $requestBody ?? '';
            $headers = $requestHeaders ?? [];
            $timeout = $timeout ?? 5;
        } else {
            $endpoint = $param;
            $body = '';
            $headers = [];
            $timeout = 5;
        }
        if (stripos($endpoint, "http") === 0) {
            $requestUrl = $endpoint;
        } else {
            $requestUrl = $od->api_version . $endpoint;
        }
        try {
            $clientSettings = [
                'base_uri' => $od->base_url,
                'headers' => array_merge([
                    'Host' => $od->base_url,
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $od->access_token
                ], $headers)
            ];
            $client = new Client($clientSettings);
            $response = $client->request($method, $requestUrl, [
                'body' => $body,
                'stream' => $stream,
                'timeout' => $timeout,
                'allow_redirects' => [
                    'track_redirects' => true
                ]
            ]);
            return $response;
        } catch (ClientException $e) {
            Log::error('OneDrive API', ['code' => $e->getCode(), 'msg' => $e->getMessage()]);
            return self::response('', $e->getCode(), $e->getMessage());
        }
    }

    /**
     * Request URL
     * @param $method
     * @param $param
     * @param bool $stream
     * @return false|mixed|\Psr\Http\Message\ResponseInterface|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function requestUrl($method, $param, $stream = true)
    {
        if (is_array($param)) {
            @list($endpoint, $requestBody, $requestHeaders, $timeout) = $param;
            $body = $requestBody ?? '';
            $headers = $requestHeaders ?? [];
            $timeout = $timeout ?? 5;
        } else {
            $endpoint = $param;
            $body = '';
            $headers = [];
            $timeout = 5;
        }
        try {
            $clientSettings = [
                'headers' => $headers
            ];
            $client = new Client($clientSettings);
            $response = $client->request($method, $endpoint, [
                'body' => $body,
                'stream' => $stream,
                'timeout' => $timeout,
                'allow_redirects' => [
                    'track_redirects' => true
                ]
            ]);
            return $response;
        } catch (ClientException $e) {
            Log::error('OneDrive HTTP', ['code' => $e->getCode(), 'msg' => $e->getMessage()]);
            return self::response('', $e->getCode(), $e->getMessage());
        }
    }

    /**
     * Get Account Info
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getMe()
    {
        $endpoint = '/me';
        $response = self::requestApi('get', $endpoint);
        return self::handleResponse($response);
    }

    /**
     * Get Drive Info
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getDrive()
    {
        $endpoint = '/me/drive';
        $response = self::requestApi('get', $endpoint);
        return self::handleResponse($response);
    }

    /**
     * Get Drive Item Children
     * @param $itemId
     * @param $query
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getChildren($itemId = '', $query = '')
    {
        $endpoint = $itemId ? "/me/drive/items/{$itemId}/children{$query}" : "/me/drive/root/children{$query}";
        $response = self::requestApi('get', $endpoint);
        if ($response instanceof Response) {
            $response = json_decode($response->getBody()->getContents(), true);
            $data = self::getNextLinkList($response);
            $res = self::formatArray($data);
            return self::response($res);
        } else {
            return $response;
        }
    }

    /**
     * Get Drive Item Children by Path
     * @param $path
     * @param $query
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getChildrenByPath($path = '/', $query = '')
    {
        $endpoint = $path === '/' ? "/me/drive/root/children{$query}" : "/me/drive/root{$path}children{$query}";
        $response = self::requestApi('get', $endpoint);
        if ($response instanceof Response) {
            $response = json_decode($response->getBody()->getContents(), true);
            $data = self::getNextLinkList($response);
            $res = self::formatArray($data);
            return self::response($res);
        } else {
            return $response;
        }
    }

    /**
     * Get Drive Item Children Next Page
     * @param $list
     * @param array $result
     * @return array|false|mixed|\Psr\Http\Message\ResponseInterface|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getNextLinkList($list, &$result = [])
    {
        if (array_has($list, '@odata.nextLink')) {
            $baseLength = strlen((new self())->base_url) + strlen((new self())->api_version);
            $endpoint = substr($list['@odata.nextLink'], $baseLength);
            $response = self::requestApi('get', $endpoint);
            $data = json_decode($response->getBody()->getContents(), true);
            $result = array_merge($list['value'], self::getNextLinkList($data, $result));
        } else {
            $result = array_merge($list['value'], $result);
        }
        return $result;
    }

    /**
     * Get Item
     * @param $itemId
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getItem($itemId)
    {
        $endpoint = "/me/drive/items/{$itemId}";
        $response = self::requestApi('get', $endpoint);
        if ($response instanceof Response) {
            $data = json_decode($response->getBody()->getContents(), true);
            $res = self::formatArray($data, false);
            return self::response($res);
        } else {
            return $response;
        }
    }

    /**
     * Get Item By Path
     * @param $path
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getItemByPath($path)
    {
        $endpoint = "/me/drive/root{$path}";
        $response = self::requestApi('get', $endpoint);
        if ($response instanceof Response) {
            $data = json_decode($response->getBody()->getContents(), true);
            $res = self::formatArray($data, false);
            return self::response($res);
        } else {
            return $response;
        }
    }

    /**
     * Copy Item
     * @param $itemId
     * @param $parentItemId
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function copy($itemId, $parentItemId)
    {
        $drive = self::responseToArray(self::getDrive());
        if ($drive['code'] === 200) {
            $driveId = array_get($drive, 'data.id');
            $endpoint = "/me/drive/items/{$itemId}/copy";
            $body = json_encode([
                'parentReference' => [
                    'driveId' => $driveId,
                    'id' => $parentItemId
                ],
            ]);
            $response = self::requestApi('post', [$endpoint, $body], false);
            if ($response instanceof Response) {
                $data = [
                    'redirect' => $response->getHeaderLine('Location')
                ];
                return self::response($data);
            } else {
                return $response;
            }
        } else {
            return self::response('', 400, 'Error');
        }
    }

    /**
     * Move Item
     * @param $itemId
     * @param $parentItemId
     * @param string $itemName
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function move($itemId, $parentItemId, $itemName = '')
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
        $response = self::requestApi('patch', [$endpoint, $body]);
        return self::handleResponse($response);
    }

    /**
     * Create Folder
     * @param $itemName
     * @param $parentItemId
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function mkdir($itemName, $parentItemId)
    {
        $endpoint = "/me/drive/items/$parentItemId/children";
        $body = '{"name":"' . $itemName . '","folder":{},"@microsoft.graph.conflictBehavior":"rename"}';
        $response = self::requestApi('post', [$endpoint, $body]);
        return self::handleResponse($response);
    }

    /**
     * Create Folder By Path
     * @param $itemName
     * @param $path
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function mkdirByPath($itemName, $path)
    {
        $endpoint = $path === '/' ? "/me/drive/root/children" : "/me/drive/root{$path}children";
        $body = '{"name":"' . $itemName . '","folder":{},"@microsoft.graph.conflictBehavior":"rename"}';
        $response = self::requestApi('post', [$endpoint, $body]);
        return self::handleResponse($response);
    }

    /**
     * Remove Item
     * @param $itemId
     * @param $eTag
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function delete($itemId, $eTag = '')
    {
        $endpoint = "/me/drive/items/{$itemId}";
        $headers = $eTag ? ['if-match' => $eTag] : [];
        $response = self::requestApi('delete', [$endpoint, '', $headers]);
        if ($response instanceof Response) {
            $statusCode = $response->getStatusCode();
            if ($statusCode === 204) {
                return self::response(['deleted' => true]);
            } else {
                return self::handleResponse($response);
            }
        } else {
            return $response;
        }
    }

    /**
     * Search
     * @param $path
     * @param $query
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function search($path, $query)
    {
        $endpoint = $path === '/' ? "/me/drive/root/search(q='{$query}')" : "/me/drive/root{$path}search(q='{$query}')";
        $response = self::requestApi('get', $endpoint);
        if ($response instanceof Response) {
            $response = json_decode($response->getBody()->getContents(), true);
            $data = self::getNextLinkList($response);
            $res = self::formatArray($data);
            return self::response($res);
        } else {
            return $response;
        }
    }

    /**
     * Get Thumbnails
     * @param $itemId
     * @param $size
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function thumbnails($itemId, $size)
    {
        $endpoint = "/me/drive/items/{$itemId}/thumbnails/0/{$size}";
        $response = self::requestApi('get', $endpoint);
        return self::handleResponse($response);
    }

    /**
     * Create Share Link
     * @param $itemId
     * @return false|mixed|\Psr\Http\Message\ResponseInterface|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function createShareLink($itemId)
    {
        $endpoint = "/me/drive/items/{$itemId}/createLink";
        $body = '{"type": "view","scope": "anonymous"}';
        $response = self::requestApi('post', [$endpoint, $body]);
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
                    return self::response('', $e->getCode(), $e->getMessage());
                }
            } else {
                $direct_link = '';
            }
            return self::response([
                'redirect' => $direct_link
            ]);
        } else {
            return $response;
        }
    }

    /**
     * Delete Share Link
     * @param $itemId
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function deleteShareLink($itemId)
    {
        $result = self::getPermission($itemId);
        $response = self::responseToArray($result);
        if ($response['code'] === 200) {
            $data = $response['data'];
            $permission = array_first($data, function ($value) {
                return $value['roles'][0] === 'read';
            });
            $permissionId = array_get($permission, 'id');
            return self::deletePermission($itemId, $permissionId);
        } else {
            return $result;
        }
    }

    /**
     * List Item permission
     * @param $itemId
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getPermission($itemId)
    {
        $endpoint = "/me/drive/items/{$itemId}/permissions";
        $response = self::requestApi('get', $endpoint);
        if ($response instanceof Response) {
            $data = json_decode($response->getBody()->getContents(), true);
            return self::response($data['value']);
        } else {
            return $response;
        }
    }

    /**
     * Delete Item permission
     * @param $itemId
     * @param $permissionId
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function deletePermission($itemId, $permissionId)
    {
        $endpoint = "/me/drive/items/{$itemId}/permissions/{$permissionId}";
        $response = self::requestApi('delete', $endpoint);
        if ($response instanceof Response) {
            $statusCode = $response->getStatusCode();
            if ($statusCode == 204) {
                return self::response(['deleted' => true]);
            } else {
                return self::handleResponse($response);
            }
        } else {
            return $response;
        }
    }

    /**
     * Get Shared Item
     * @return false|mixed|\Psr\Http\Message\ResponseInterface|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getShareWithMe()
    {
        $endpoint = '/me/drive/sharedWithMe';
        $response = self::requestApi('get', $endpoint);
        return self::handleResponse($response);
    }

    /**
     *  Get Shared Item Detail
     * @param $driveId
     * @param $itemId
     * @return false|mixed|\Psr\Http\Message\ResponseInterface|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getShareWithMeDetail($driveId, $itemId)
    {
        $endpoint = "/drives/{$driveId}/items/{$itemId}";
        $response = self::requestApi('get', $endpoint);
        return self::handleResponse($response);
    }

    /**
     * Upload File(less 4MB)
     * @param $id
     * @param $content
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function upload($id, $content)
    {
        $stream = \GuzzleHttp\Psr7\stream_for($content);
        $endpoint = "/me/drive/items/{$id}/content";
        $body = $stream;
        $response = self::requestApi('put', [$endpoint, $body]);
        return self::handleResponse($response);
    }

    /**
     * Upload File(less 4MB) by path
     * @param $path
     * @param $content
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function uploadByPath($path, $content)
    {
        $stream = \GuzzleHttp\Psr7\stream_for($content);
        $endpoint = "/me/drive/root{$path}content";
        $body = $stream;
        $response = self::requestApi('put', [$endpoint, $body]);
        return self::handleResponse($response);
    }

    /**
     * Download via Url
     * @param string $remote remote uri with filename
     * @param string $url
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function uploadUrl($remote, $url)
    {
        $drive = self::responseToArray(self::getDrive());
        if ($drive['code'] == 200) {
            if ($drive['data']['driveType'] == 'business') {
                return self::response(['driveType' => $drive['data']['driveType']], 400, 'Account Not Support');
            } else {
                $path = self::getAbsolutePath(dirname($remote));
                // $pathId = $this->pathToItemId($path);
                // $endpoint = "/me/drive/items/{$pathId}/children"; // by id
                $handledPath = self::handleUrl(trim($path, '/'));
                $graphPath = empty($handledPath) ? '/' : ":/{$handledPath}:/";
                $endpoint = "/me/drive/root{$graphPath}children";
                $headers = ['Prefer' => 'respond-async'];
                $body = '{"@microsoft.graph.sourceUrl":"' . $url . '","name":"' . pathinfo($remote, PATHINFO_BASENAME) . '","file":{}}';
                $response = self::requestApi('post', [$endpoint, $body, $headers]);
                if ($response instanceof Response) {
                    $data = [
                        'redirect' => $response->getHeaderLine('Location')
                    ];
                    return self::response($data);
                } else {
                    return $response;
                }
            }
        } else {
            return $drive;
        }
    }

    /**
     * Create Upload Session
     * @param $remote
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function createUploadSession($remote)
    {
        $endpoint = "/me/drive/root{$remote}createUploadSession";
        $body = json_encode([
            'item' => [
                '@microsoft.graph.conflictBehavior' => 'fail',
            ]
        ]);
        $response = self::requestApi('post', [$endpoint, $body]);
        return self::handleResponse($response);

    }

    /**
     * Upload Partly
     * @param $url
     * @param $file
     * @param $offset
     * @param int $length
     * @return false|mixed|\Psr\Http\Message\ResponseInterface|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function uploadToSession($url, $file, $offset, $length = 5242880)
    {
        $file_size = self::readFileSize($file);
        $content_length = (($offset + $length) > $file_size) ? ($file_size - $offset) : $length;
        $end = (($offset + $length) > $file_size) ? ($file_size - 1) : $offset + $content_length - 1;
        $content = self::readFileContent($file, $offset, $length);
        $headers = [
            'Content-Length' => $content_length,
            'Content-Range' => "bytes {$offset}-{$end}/{$file_size}",
        ];
        $requestBody = $content;
        $response = self::requestUrl('put', [$url, $requestBody, $headers, 360]);
        return self::handleResponse($response);
    }

    /**
     * Get Upload Status
     * @param $url
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function uploadSessionStatus($url)
    {
        $response = self::requestUrl('get', $url);
        return self::handleResponse($response);
    }

    /**
     * Delete Upload Session
     * @param $url
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function deleteUploadSession($url)
    {
        $response = self::requestUrl('delete', $url);
        return self::handleResponse($response);
    }

    /**
     * Transfer Item ID To Path
     * @param $itemId
     * @param bool $start
     * @return false|mixed|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function itemIdToPath($itemId, $start = false)
    {
        $result = self::getItem($itemId);
        $response = self::responseToArray($result);
        if ($response['code'] === 200) {
            $item = $response['data'];
            if (!array_key_exists('path', $item['parentReference']) && $item['name'] == 'root') {
                return self::response([
                    'path' => '/'
                ]);
            }
            $path = $item['parentReference']['path'];
            if (starts_with($path, '/drive/root:')) {
                $path = str_after($path, '/drive/root:');
            }
            if (!$start) {
                $pathArr = $path === '' ? [] : explode('/', $path);
            } else {
                // 兼容根目录
                if ($path === '') {
                    $pathArr = [];
                } else {
                    $pathArr = explode('/', $path);
                    if (trim($start, '/') !== '') {
                        $pathArr = array_slice($pathArr, 1);
                    }
                }
            }
            array_push($pathArr, $item['name']);
            $path = self::getAbsolutePath(implode('/', $pathArr));
            return self::response([
                'path' => $path
            ]);
        } else {
            return $result;
        }
    }

    /**
     * Transfer Item Path To ID
     * @param $path
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function pathToItemId($path)
    {
        $endpoint = $path === '/' ? '/me/drive/root' : '/me/drive/root' . $path;
        $response = self::requestApi('get', $endpoint);
        if ($response instanceof Response) {
            $response = json_decode($response->getBody()->getContents(), true);
            return self::response(['id' => $response['id']]);
        } else {
            return $response;
        }
    }

    /**
     * Format Response Data
     * @param $response
     * @param bool $isList
     * @return array
     */
    public static function formatArray($response, $isList = true)
    {
        if ($isList) {
            $items = [];
            foreach ($response as $item) {
                if (array_has($item, 'file')) $item['ext'] = strtolower(pathinfo($item['name'], PATHINFO_EXTENSION));
                $items[$item['name']] = $item;
            }
            return $items;
        } else {
            $response['ext'] = strtolower(pathinfo($response['name'], PATHINFO_EXTENSION));
            return $response;
        }
    }

    /**
     * Return Response
     * @param $data
     * @param int $code
     * @param string $msg
     * @return false|string
     */
    public static function response($data, $code = 200, $msg = '')
    {
        return response()->json([
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        ], $code);
    }

    /**
     * Handle Response
     * @param $response Response
     * @return false|string
     */
    public static function handleResponse($response)
    {
        if ($response instanceof Response) {
            if (in_array($response->getStatusCode(), [200, 201, 202, 204])) {
                $data = json_decode($response->getBody()->getContents(), true);
                return self::response($data);
            } else {
                return $response;
            }
        } else {
            return $response;
        }
    }

    /**
     * Response To Array
     * @param $response Response|JsonResponse
     * @param bool $origin
     * @return array
     */
    public static function responseToArray($response, $origin = true)
    {
        if ($response instanceof JsonResponse)
            $data = json_encode($response->getData());
        else $data = $response;
        if ($origin) {
            return json_decode($data, true);
        } else {
            return json_decode($data, true)['data'];
        }
    }

    /**
     * Transfer Path
     * @param $path
     * @return mixed
     */
    public static function getAbsolutePath($path)
    {
        $path = str_replace(['/', '\\', '//'], '/', $path);

        $parts = array_filter(explode('/', $path), 'strlen');
        $absolutes = [];
        foreach ($parts as $part) {
            if ('.' == $part) continue;
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        return str_replace('//', '/', '/' . implode('/', $absolutes) . '/');
    }

    /**
     * Handle Url
     * @param $path
     * @return string
     */
    public static function handleUrl($path)
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
     * Read File Size
     * @param $path
     * @return bool|int|string
     */
    public static function readFileSize($path)
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
     * @param $file
     * @param $offset
     * @param $length
     * @return bool|string
     */
    public static function readFileContent($file, $offset, $length)
    {
        $handler = fopen($file, "rb") ?? die('Failed Get Content');
        fseek($handler, $offset);
        return fread($handler, $length);
    }
}
