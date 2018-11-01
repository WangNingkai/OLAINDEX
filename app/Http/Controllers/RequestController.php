<?php

namespace App\Http\Controllers;

use App\Helpers\Tool;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Stream;
use Microsoft\Graph\Core\GraphConstants;
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
            abort($e->getCode());
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
            abort($e->getCode());
        }

    }

    /**
     * 发送请求
     * @param $method
     * @param $param
     * @return mixed|\Psr\Http\Message\ResponseInterface|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request($method, $param)
    {
        $allowMethod = ['get', 'post', 'put', 'patch', 'delete'];
        if (!in_array(strtolower($method), $allowMethod)) exit('请求参数异常');
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
        if (!is_string($requestBody) || !is_a($requestBody, 'GuzzleHttp\\Psr7\\Stream')) {
            $requestBody = json_encode($requestBody);
        }
        $baseUrl = GraphConstants::REST_ENDPOINT;
        $apiVersion = GraphConstants::API_VERSION;
        //Send request with opaque URL
        if (stripos($endpoint, "http") === 0) {
            $requestUrl = $endpoint;
        } else {
            $requestUrl = $apiVersion . $endpoint;
        }
        try {
            $token = Tool::config('access_token');
            $clientSettings = [
                'base_uri' => $baseUrl,
                'headers' => array_merge($headers, [
                    'Host' => $baseUrl,
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token
                ])
            ];
            $client = new Client($clientSettings);
            $response = $client->request($method, $requestUrl, [
                'body' => $requestBody,
                'stream' => true,
                'timeout' => 5
            ]);
        } catch (ClientException $e) {
            abort($e->getCode(), $e->getMessage());
        }
        return $response ?? null;
    }

}
