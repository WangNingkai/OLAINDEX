<?php

namespace App\Helpers;

/**
 * Class OneDriveExt
 *
 * @package App\Helpers
 */
class OneDriveExt
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
     * OneDrive constructor.
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
    protected static function request(
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
            $response_data = array_get($response, 'data');
            $data = self::getNextLinkList($response_data);

            return self::formatArray($data);
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
        $endpoint = $path === '/' ? "/me/drive/root/children{$query}"
            : "/me/drive/root{$path}children{$query}";
        $response = self::request('get', $endpoint);
        if ($response['errno'] === 0) {
            $response_data = array_get($response, 'data');
            $data = self::getNextLinkList($response_data);

            return self::formatArray($data);
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
        if (array_has($list, '@odata.nextLink')) {
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
            $data = array_get($response, 'data');

            return self::formatArray($data, false);
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
        $endpoint = "/me/drive/root{$path}{$query}";
        $response = self::request('get', $endpoint);
        if ($response['errno'] === 0) {
            $data = array_get($response, 'data');

            return self::formatArray($data, false);
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
            $driveId = array_get($drive, 'data.id');
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
                    'redirect' => array_get($response, 'headers.Location'),
                ];

                return $data;
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
            $content = array_add($content, 'name', $itemName);
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
        $body = '{"name":"'.$itemName
            .'","folder":{},"@microsoft.graph.conflictBehavior":"rename"}';
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
        $endpoint = $path === '/' ? "/me/drive/root/children"
            : "/me/drive/root{$path}children";
        $body = '{"name":"'.$itemName
            .'","folder":{},"@microsoft.graph.conflictBehavior":"rename"}';
        $response = self::request('post', [$endpoint, $body]);

        return $response;
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
                if (array_has($item, 'file')) {
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
}
