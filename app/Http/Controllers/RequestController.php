<?php

namespace App\Http\Controllers;

use App\Helpers\Tool;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

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
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestGraph($method, $param, $toArray = true)
    {
        $allowMethod = ['get', 'post', 'put', 'patch', 'delete'];
        if (!in_array(strtolower($method), $allowMethod)) exit('请求参数异常');
        if (is_array($param)) {
            list($endpoint, $requestBody, $requestHeaders) = $param;
            $requestHeaders = $requestHeaders ?? [];
            $requestBody = $requestBody ?? '';
            $headers = $requestHeaders ?? [];
        } else {
            $endpoint = $param;
            $requestBody = '';
            $headers = [];
        }
        if (!is_string($requestBody) || !is_a($requestBody, 'GuzzleHttp\Psr7\Stream')) {
            $requestBody = json_encode($requestBody);
        }
        $baseUrl = 'https://graph.microsoft.com/';
        $apiVersion = 'v1.0';
        // Send request with opaque URL
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
            return $toArray ? json_decode($response->getBody()->getContents(), true) : $response->getBody()->getContents();
        } catch (ClientException $e) {
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
            $response = $response->getBody()->getContents();
        } catch (ClientException $e) {
            abort($e->getCode());
        }
        return $response ?? null;

    }

    /**
     * 发送请求
     * @param $method
     * @param $param
     * @return false|mixed|\Psr\Http\Message\ResponseInterface|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request($method, $param)
    {
        $allowMethod = ['get', 'post', 'put', 'patch', 'delete'];
        if (!in_array(strtolower($method), $allowMethod)) exit('请求参数异常');
        if (is_array($param)) {
            list($endpoint, $requestBody, $requestHeaders, $timeout) = $param;
            $requestHeaders = $requestHeaders ?? [];
            $requestBody = $requestBody ?? '';
            $headers = $requestHeaders ?? [];
            $timeout = $timeout ?? 5;
        } else {
            $endpoint = $param;
            $requestBody = '';
            $headers = [];
            $timeout = 5;
        }
        if (!is_string($requestBody) || !is_a($requestBody, 'GuzzleHttp\\Psr7\\Stream')) {
            $requestBody = json_encode($requestBody);
        }
        $baseUrl = 'https://graph.microsoft.com/';
        $apiVersion = 'v1.0';
        // Send request with opaque URL
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
                'timeout' => $timeout
            ]);
        } catch (ClientException $e) {
            $response = json_encode(['code' => $e->getCode(), 'msg' => $e->getMessage()]);
        }
        return $response;
    }

}
