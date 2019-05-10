<?php

namespace App\Helpers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Chumper\Zipper\Zipper;
use Carbon\Carbon;

/**
 * Class OneDrive
 *
 * @package App\Helpers
 */
class OneDrive
{
    /**
     * @var GraphRequest
     */
    protected $graph;

    /**
     * @var $baseUrl
     */
    protected $baseUrl;

    /**
     * @var $apiVersion
     */
    protected $apiVersion;

    /**
     * OneDriveGraph constructor.
     */
    public function __construct()
    {
        $access_token = Tool::config('access_token');
        $base_url = Tool::config('account_type', 'com') === 'com'
            ? Constants::REST_ENDPOINT : Constants::REST_ENDPOINT_21V;
        $api_version = Constants::API_VERSION;
        $this->graph = new GraphRequest();
        $this->graph->setAccessToken($access_token);
        $this->graph->setBaseUrl($base_url);
        $this->graph->setApiVersion($api_version);
        $this->baseUrl = $base_url;
        $this->apiVersion = $api_version;
    }

    /**
     * @param      $method
     * @param      $param
     * @param bool $token
     *
     * @return mixed
     * @throws \ErrorException
     */
    public static function request(
        $method,
        $param,
        $token = false
    ) {
        $od = new self();
        $response = $od->graph->request(
            $method,
            $param,
            $token
        );
        if (is_null($response->getResponseError())) {
            $headers = json_decode($response->getResponseHeaders(), true);
            $response = json_decode($response->getResponse(), true);

            return [
                'errno'   => 0,
                'msg'     => 'OK',
                'headers' => $headers,
                'data'    => $response,
            ];
        } else {
            return json_decode($response->getResponseError(), true);
        }
    }

    /**
     * Get Account Info
     *
     * @throws \ErrorException
     */
    public static function getMe()
    {
        $endpoint = '/me';
        $response = self::request('get', $endpoint);

        return $response;
    }

    /**
     * Get Drive Info
     *
     * @return mixed
     * @throws \ErrorException
     */
    public static function getDrive()
    {
        $endpoint = '/me/drive';
        $response = self::request('get', $endpoint);

        return $response;
    }

    /**
     * Get Drive Item Children
     *
     * @param string $itemId
     * @param string $query
     *
     * @return array|mixed
     * @throws \ErrorException
     */
    public static function getChildren($itemId = '', $query = '')
    {
        $endpoint = $itemId ? "/me/drive/items/{$itemId}/children{$query}"
            : "/me/drive/root/children{$query}";
        $response = self::request('get', $endpoint);

        if ($response['errno'] === 0) {
            $response_data = Arr::get($response, 'data');
            $data = self::getNextLinkList($response_data);

            $format = self::formatArray($data);

            return self::response($format);
        } else {
            return $response;
        }
    }

    /**
     * Get Drive Item Children by Path
     *
     * @param string $path
     * @param string $query
     *
     * @return array|mixed
     * @throws \ErrorException
     */
    public static function getChildrenByPath($path = '/', $query = '')
    {
        $requestPath = self::getRequestPath($path);
        $endpoint = $requestPath === '/' ? "/me/drive/root/children{$query}"
            : "/me/drive/root{$requestPath}children{$query}";
        $response = self::request('get', $endpoint);
        if ($response['errno'] === 0) {
            $response_data = Arr::get($response, 'data');
            $data = self::getNextLinkList($response_data);

            $format = self::formatArray($data);

            return self::response($format);
        } else {
            return $response;
        }
    }

    /**
     * Get Drive Item Children Next Page
     *
     * @param       $list
     * @param array $result
     *
     * @return array
     * @throws \ErrorException
     */
    public static function getNextLinkList($list, &$result = [])
    {
        if (Arr::has($list, '@odata.nextLink')) {
            $od = new self();
            $baseLength = strlen($od->baseUrl) + strlen($od->apiVersion);
            $endpoint = substr($list['@odata.nextLink'], $baseLength);
            $response = self::request('get', $endpoint);
            if ($response['errno'] === 0) {
                $data = $response['data'];
            } else {
                $data = [];
            }
            $result = array_merge(
                $list['value'],
                self::getNextLinkList($data, $result)
            );
        } else {
            $result = array_merge($list['value'], $result);
        }

        return $result;
    }

    /**
     * Get Item
     *
     * @param        $itemId
     * @param string $query
     *
     * @return array|mixed
     * @throws \ErrorException
     */
    public static function getItem($itemId, $query = '')
    {
        $endpoint = "/me/drive/items/{$itemId}{$query}";
        $response = self::request('get', $endpoint);
        if ($response['errno'] === 0) {
            $data = Arr::get($response, 'data');

            $format = self::formatArray($data, false);

            return self::response($format);
        } else {
            return $response;
        }
    }

    /**
     * Get Item By Path
     *
     * @param        $path
     * @param string $query
     *
     * @return array|mixed
     * @throws \ErrorException
     */
    public static function getItemByPath($path, $query = '')
    {
        $requestPath = self::getRequestPath($path);
        $endpoint = "/me/drive/root{$requestPath}{$query}";
        $response = self::request('get', $endpoint);
        if ($response['errno'] === 0) {
            $data = Arr::get($response, 'data');

            $format = self::formatArray($data, false);

            return self::response($format);
        } else {
            return $response;
        }
    }

    /**
     * @param $itemId
     * @param $parentItemId
     *
     * @return mixed
     * @throws \ErrorException
     */
    public static function copy($itemId, $parentItemId)
    {
        $drive = self::getDrive();
        if ($drive['errno'] === 0) {
            $driveId = Arr::get($drive, 'data.id');
            $endpoint = "/me/drive/items/{$itemId}/copy";
            $body = json_encode([
                'parentReference' => [
                    'driveId' => $driveId,
                    'id'      => $parentItemId,
                ],
            ]);
            $response = self::request('post', [$endpoint, $body], false);
            if ($response['errno'] === 0) {
                $data = [
                    'redirect' => Arr::get($response, 'headers.Location'),
                ];

                return self::response($data);
            } else {
                return $response;
            }
        } else {
            return $drive;
        }
    }

    /**
     * @param        $itemId
     * @param        $parentItemId
     * @param string $itemName
     *
     * @return mixed
     * @throws \ErrorException
     */
    public static function move($itemId, $parentItemId, $itemName = '')
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

        $response = self::request('patch', [$endpoint, $body]);

        return $response;
    }

    /**
     * @param $itemName
     * @param $parentItemId
     *
     * @return mixed
     * @throws \ErrorException
     */
    public static function mkdir($itemName, $parentItemId)
    {
        $endpoint = "/me/drive/items/$parentItemId/children";
        $body = '{"name":"' . $itemName
            . '","folder":{},"@microsoft.graph.conflictBehavior":"rename"}';
        $response = self::request('post', [$endpoint, $body]);

        return $response;
    }

    /**
     * @param $itemName
     * @param $path
     *
     * @return mixed
     * @throws \ErrorException
     */
    public static function mkdirByPath($itemName, $path)
    {
        $requestPath = self::getRequestPath($path);
        $endpoint = $requestPath === '/' ? '/me/drive/root/children'
            : "/me/drive/root{$requestPath}children";
        $body = '{"name":"' . $itemName
            . '","folder":{},"@microsoft.graph.conflictBehavior":"rename"}';
        $response = self::request('post', [$endpoint, $body]);

        return $response;
    }

    /**
     * @param        $itemId
     * @param string $eTag
     *
     * @return array|mixed
     * @throws \ErrorException
     */
    public static function delete($itemId, $eTag = '')
    {
        $endpoint = "/me/drive/items/{$itemId}";
        $headers = $eTag ? ['if-match' => $eTag] : [];
        $response = self::request('delete', [$endpoint, '', $headers]);
        if ($response['errno'] === 0) {
            return self::response(['deleted' => true]);
        } else {
            return $response;
        }
    }

    /**
     * @param $path
     * @param $query
     *
     * @return array|mixed
     * @throws \ErrorException
     */
    public static function search($path, $query)
    {
        $graphPath = self::getRequestPath($path);
        $endpoint = $graphPath === '/' ? "/me/drive/root/search(q='{$query}')"
            : "/me/drive/root{$graphPath}search(q='{$query}')";
        $response = self::request('get', $endpoint);
        if ($response['errno'] === 0) {
            $response_data = Arr::get($response, 'data');
            $data = self::getNextLinkList($response_data);

            $format = self::formatArray($data);

            return self::response($format);
        } else {
            return $response;
        }
    }

    /**
     * @param $itemId
     * @param $size
     *
     * @return mixed
     * @throws \ErrorException
     */
    public static function thumbnails($itemId, $size)
    {
        $endpoint = "/me/drive/items/{$itemId}/thumbnails/0/{$size}";
        $response = self::request('get', $endpoint);

        return $response;
    }

    /**
     * @param $itemId
     *
     * @return array|mixed
     * @throws \ErrorException
     */
    public static function createShareLink($itemId)
    {
        $endpoint = "/me/drive/items/{$itemId}/createLink";
        $body = '{"type": "view","scope": "anonymous"}';
        $response = self::request('post', [$endpoint, $body]);

        if ($response['errno'] === 0) {
            $data = $response['data'];
            $web_url = Arr::get($data, 'link.webUrl');
            if (Str::contains($web_url, ['sharepoint.com', 'sharepoint.cn'])) {
                $parse = parse_url($web_url);
                $domain = "{$parse['scheme']}://{$parse['host']}/";
                $param = Str::after($parse['path'], 'personal/');
                $info = explode('/', $param);
                $res_id = $info[1];
                $user_info = $info[0];
                $direct_link = $domain . 'personal/' . $user_info
                    . '/_layouts/15/download.aspx?share=' . $res_id;
            } elseif (Str::contains($web_url, '1drv.ms')) {
                $req = self::request('get', $web_url);
                if ($req['errno'] === 0) {
                    $direct_link = str_replace(
                        'redir?',
                        'download?',
                        $req['headers']['Location']
                    );
                } else {
                    return $req;
                }
            } else {
                $direct_link = '';
            }

            return self::response([
                'redirect' => $direct_link,
            ]);
        } else {
            return $response;
        }
    }

    /**
     * @param $itemId
     *
     * @return array|mixed
     * @throws \ErrorException
     */
    public static function deleteShareLink($itemId)
    {
        $response = self::getPermission($itemId);
        if ($response['errno'] === 0) {
            $data = $response['data'];
            $permission = Arr::first($data, function ($value) {
                return $value['roles'][0] === 'read';
            });
            $permissionId = Arr::get($permission, 'id');

            return self::deletePermission($itemId, $permissionId);
        } else {
            return $response;
        }
    }

    /**
     * @param $itemId
     *
     * @return array|mixed
     * @throws \ErrorException
     */
    public static function getPermission($itemId)
    {
        $endpoint = "/me/drive/items/{$itemId}/permissions";
        $response = self::request('get', $endpoint);
        if ($response['errno'] === 0) {
            $data = $response['data'];

            return self::response($data['value']);
        } else {
            return $response;
        }
    }

    /**
     * @param $itemId
     * @param $permissionId
     *
     * @return array|mixed
     * @throws \ErrorException
     */
    public static function deletePermission($itemId, $permissionId)
    {
        $endpoint = "/me/drive/items/{$itemId}/permissions/{$permissionId}";
        $response = self::request('delete', $endpoint);
        if ($response['errno'] === 0) {
            return self::response(['deleted' => true]);
        } else {
            return $response;
        }
    }

    /**
     * @param $id
     * @param $content
     *
     * @return mixed
     * @throws \ErrorException
     */
    public static function upload($id, $content)
    {
        $endpoint = "/me/drive/items/{$id}/content";
        $body = $content;
        $response = self::request('put', [$endpoint, $body]);

        return $response;
    }

    /**
     * @param $path
     * @param $content
     *
     * @return mixed
     * @throws \ErrorException
     */
    public static function uploadByPath($path, $content)
    {
        $requestPath = self::getRequestPath($path);
        $endpoint = "/me/drive/root{$requestPath}content";
        $body = $content;
        $response = self::request('put', [$endpoint, $body]);

        return $response;
    }

    /**
     * @param $remote
     * @param $url
     *
     * @return array|mixed
     * @throws \ErrorException
     */
    public static function uploadUrl($remote, $url)
    {
        $drive = self::getDrive();
        if ($drive['errno'] === 0) {
            if ($drive['data']['driveType'] == 'business') {
                return self::response(
                    ['driveType' => $drive['data']['driveType']],
                    400,
                    'Account Not Support'
                );
            } else {
                $path = self::getAbsolutePath(dirname($remote));
                // $pathId = $this->pathToItemId($path);
                // $endpoint = "/me/drive/items/{$pathId}/children"; // by id
                $handledPath = self::getEncodeUrl(trim($path, '/'));
                $graphPath = empty($handledPath) ? '/' : ":/{$handledPath}:/";
                $endpoint = "/me/drive/root{$graphPath}children";
                $headers = ['Prefer' => 'respond-async'];
                $body = '{"@microsoft.graph.sourceUrl":"' . $url . '","name":"'
                    . pathinfo($remote, PATHINFO_BASENAME) . '","file":{}}';
                $response = self::request('post', [$endpoint, $body, $headers]);
                if ($response['errno'] === 0) {
                    $data = [
                        'redirect' => $response['headers']['Location'],
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
     * @param $remote
     *
     * @return mixed
     * @throws \ErrorException
     */
    public static function createUploadSession($remote)
    {
        $graphPath = self::getRequestPath($remote);
        $endpoint = "/me/drive/root{$graphPath}createUploadSession";
        $body = json_encode([
            'item' => [
                '@microsoft.graph.conflictBehavior' => 'fail',
            ],
        ]);
        $response = self::request('post', [$endpoint, $body]);

        return $response;
    }

    /**
     * @param     $url
     * @param     $file
     * @param     $offset
     * @param int $length
     *
     * @return mixed
     * @throws \ErrorException
     */
    public static function uploadToSession(
        $url,
        $file,
        $offset,
        $length = 5242880
    ) {
        $file_size = self::readFileSize($file);
        $content_length = (($offset + $length) > $file_size) ? ($file_size
            - $offset) : $length;
        $end = (($offset + $length) > $file_size) ? ($file_size - 1)
            : $offset + $content_length - 1;
        $content = self::readFileContent($file, $offset, $length);
        $headers = [
            'Content-Length' => $content_length,
            'Content-Range'  => "bytes {$offset}-{$end}/{$file_size}",
        ];
        $requestBody = $content;
        $response = self::request(
            'put',
            [$url, $requestBody, $headers, 360]
        );

        return $response;
    }

    /**
     * @param $url
     *
     * @return mixed
     * @throws \ErrorException
     */
    public static function uploadSessionStatus($url)
    {
        $response = self::request('get', $url, false);

        return $response;
    }

    /**
     * @param $url
     *
     * @return mixed
     * @throws \ErrorException
     */
    public static function deleteUploadSession($url)
    {
        $response = self::request('delete', $url, false);

        return $response;
    }

    /**
     * @param      $itemId
     * @param bool $start
     *
     * @return array|mixed
     * @throws \ErrorException
     */
    public static function itemIdToPath($itemId, $start = false)
    {
        $response = self::getItem($itemId);
        if ($response['errno'] === 0) {
            $item = $response['data'];
            if (!array_key_exists('path', $item['parentReference'])
                && $item['name'] == 'root'
            ) {
                return self::response([
                    'path' => '/',
                ]);
            }
            $path = $item['parentReference']['path'];
            if (starts_with($path, '/drive/root:')) {
                $path = Str::after($path, '/drive/root:');
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
                'path' => $path,
            ]);
        } else {
            return $response;
        }
    }

    /**
     * @param $path
     *
     * @return array|mixed
     * @throws \ErrorException
     */
    public static function pathToItemId($path)
    {
        $requestPath = self::getRequestPath($path);
        $endpoint = $requestPath === '/' ? '/me/drive/root'
            : '/me/drive/root' . $requestPath;
        $response = self::request('get', $endpoint);
        if ($response['errno'] === 0) {
            $data = $response['data'];

            return self::response(['id' => $data['id']]);
        } else {
            return $response;
        }
    }

    /**
     * Format Response Data
     *
     * @param      $response
     * @param bool $isList
     *
     * @return array
     */
    public static function formatArray($response, $isList = true)
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
        } else {
            $response['ext'] = strtolower(
                pathinfo(
                    $response['name'],
                    PATHINFO_EXTENSION
                )
            );

            return $response;
        }
    }

    /**
     * Handle Request Path
     *
     * @param      $path
     * @param bool $isFile
     *
     * @return string
     */
    public static function getRequestPath($path, $isFile = false)
    {
        $origin_path = self::getAbsolutePath($path);
        $query_path = trim($origin_path, '/');
        $query_path = self::getEncodeUrl(rawurldecode($query_path));
        $request_path = empty($query_path) ? '/' : ":/{$query_path}:/";
        if ($isFile) {
            return rtrim($request_path, ':/');
        }

        return $request_path;
    }

    /**
     * Transfer Path
     *
     * @param $path
     *
     * @return mixed
     */
    public static function getAbsolutePath($path)
    {
        $path = str_replace(['/', '\\', '//'], '/', $path);

        $parts = array_filter(explode('/', $path), 'strlen');
        $absolutes = [];
        foreach ($parts as $part) {
            if ('.' == $part) {
                continue;
            }
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
     *
     * @param $path
     *
     * @return string
     */
    public static function getEncodeUrl($path)
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
     *
     * @param $path
     *
     * @return bool|int|string
     */
    public static function readFileSize($path)
    {
        if (!file_exists($path)) {
            return false;
        }
        $size = filesize($path);
        if (!($file = fopen($path, 'rb'))) {
            return false;
        }
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

    public static function compressedFile($path, $archive)
    {
        if (!file_exists($path)) {
            return false;
        }

        $pathInfo = pathinfo($path);

        if (isset($pathInfo['extension']) && in_array($pathInfo['extension'], Arr::get(Constants::FILE_ICON, 'zip.2'))) {
            return $path;
        }

        if (in_array(Arr::get($pathInfo, 'extension'), Constants::ARCHIVE_EXTENSION) || $archive) {
            $temp = self::getTempFile($pathInfo);
            $old_memory_limit = ini_get('memory_limit');
            ini_set('memory_limit', '-1');
            $zipper = new Zipper();
            $zipper->make($temp)->add($path)->close();
            ini_set('memory_limit', $old_memory_limit);

            return $temp;
        }

        return $path;
    }

    public static function getTempFile($pathInfo)
    {
        $tempPath = sys_get_temp_dir() . '/';

        $tempPath .= Arr::get($pathInfo, 'filename', Carbon::now()->format('YmdHis') . Str::random(4));

        if (!empty($extension = Arr::get($pathInfo, 'extension'))) {
            $tempPath .= '.' . $extension;
        }

        return $tempPath . '.zip';
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
    public static function readFileContent($file, $offset, $length)
    {
        $handler = fopen($file, 'rb') ?? die('Failed Get Content');
        fseek($handler, $offset);

        return fread($handler, $length);
    }

    public static function response($data, $errno = 0, $msg = 'ok')
    {
        return [
            'errno' => $errno,
            'msg'   => $msg,
            'data'  => $data,
        ];
    }
}
