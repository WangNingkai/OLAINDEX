<?php

namespace App\Http\Controllers;

use App\Helpers\Tool;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Stream;
use Microsoft\Graph\Exception\GraphException;
use Microsoft\Graph\Graph;

/**
 * 处理请求操作
 * Class RequestController
 * @package App\Http\Controllers
 */
class RequestController extends Controller
{
    /**
     * 构造graph请求
     * @param $method
     * @param $param
     * @param bool $toArray
     * @return mixed|null
     */
    public function requestGraph($method, $param, $toArray = true)
    {
        if (is_array($param)) {
            list($endpoint, $requestBody, $requestHeaders) = $param;
            $requestHeaders = $requestHeaders ?? [];
            $requestBody = $requestBody ?? '';
            $headers = array_merge($requestHeaders, ["Content-Type" => "application/json"]);
        } else {
            $endpoint = $param;
            $requestBody = '';
            $headers = ["Content-Type" => "application/json"];
        }
        try {
            $graph = new Graph();
            $graph->setBaseUrl("https://graph.microsoft.com/")
                ->setApiVersion("v1.0")
                ->setAccessToken(Tool::config('access_token'));
            $response = $graph->createRequest($method, $endpoint)
                ->addHeaders($headers)
                ->attachBody($requestBody)
                ->setReturnType(Stream::class)
                ->execute();
            return $toArray ? json_decode($response->getContents(), true) : $response->getContents();
        } catch (GraphException $e) {
            Tool::showMessage($e->getCode() . ': 请检查地址是否正确', false);
            return null;
        }
    }

    /**
     * 发送http请求
     * @param $method
     * @param $url
     * @return null|string
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
            Tool::showMessage($e->getCode() . ': 请检查链接是否正确', false);
            return null;
        }

    }
}
