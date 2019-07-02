<?php

namespace App\Service;

use Curl\Curl;
use Illuminate\Support\Arr;
use ErrorException;
use Log;

class GraphRequest
{
    /**
     * @var $accessToken
     */
    protected $accessToken;
    /**
     * @var $baseUrl
     */
    protected $baseUrl;
    /**
     * @var $apiVersion
     */
    protected $apiVersion;
    /**
     * The endpoint to call
     *
     * @var string
     */
    protected $endpoint;
    /**
     * An array of headers to send with the request
     *
     * @var array(string => string)
     */
    protected $headers;
    /**
     * The body of the request (optional)
     *
     * @var string
     */
    protected $requestBody;
    /**
     * The type of request to make ("GET", "POST", etc.)
     *
     * @var object
     */
    protected $requestType;
    /**
     * The timeout, in seconds
     *
     * @var string
     */
    protected $timeout;
    /**
     * @var $response
     */
    protected $response;
    /**
     * @var $responseHeaders
     */
    protected $responseHeaders;
    /**
     * @var $responseError
     */
    protected $responseError;

    /**
     * @var bool
     */
    public $error = false;

    /**
     * 构造 microsoft graph 请求
     * @param      $method
     * @param      $param
     * @param null $token
     *
     * @return $this
     * @throws ErrorException
     */
    public function request($method, $param, $token = null): self
    {
        if (is_array($param)) {
            @list($endpoint, $requestBody, $requestHeaders, $timeout) = $param;
            $this->requestBody = $requestBody ?? '';
            $this->headers = $requestHeaders ?? [];
            $this->timeout = $timeout ?? CoreConstants::DEFAULT_TIMEOUT;
            $this->endpoint = $endpoint;
        } else {
            $this->endpoint = $param;
            $this->headers = [];
            $this->timeout = CoreConstants::DEFAULT_TIMEOUT;
        }
        if (!$token) {
            $this->headers = array_merge([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->accessToken,
            ], $this->headers);
            if (stripos($this->endpoint, 'http') !== 0) {
                $this->endpoint = $this->apiVersion . $this->endpoint;
            }
        }
        $this->requestType = strtoupper($method);
        $options = [
            CURLOPT_CUSTOMREQUEST => $this->requestType,
            CURLOPT_AUTOREFERER => true,
            CURLOPT_FAILONERROR => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_ENCODING => 'gzip,deflate',

        ];
        if ($this->requestBody) {
            $options = Arr::add($options, CURLOPT_POST, true);
            $options = Arr::add(
                $options,
                CURLOPT_POSTFIELDS,
                $this->requestBody
            );
        }
        if ($this->baseUrl) {
            $curl = new Curl($this->baseUrl);
        } else {
            $curl = new Curl();
        }
        $curl->setUserAgent('ISV|OLAINDEX|OLAINDEX/4.0');
        $curl->setHeaders($this->headers);
        $curl->setRetry(CoreConstants::DEFAULT_RETRY);
        $curl->setConnectTimeout(CoreConstants::DEFAULT_CONNECT_TIMEOUT);
        $curl->setTimeout((int)$this->timeout);
        $curl->setUrl($this->endpoint);
        $curl->setOpts($options);
        $curl->exec();
        $curl->close();
        if ($curl->error) {
            Log::error(
                'Get OneDrive source content error.',
                [
                    'errno' => $curl->errorCode,
                    'message' => $curl->errorMessage,
                    'headers' => $curl->responseHeaders
                ]
            );
            $this->responseError = collect([
                'errno' => $curl->errorCode,
                'msg' => $curl->errorMessage,
                'headers' => $curl->responseHeaders
            ])->toJson();
            $this->error = true;
        }
        $this->responseHeaders = collect($curl->responseHeaders)->toJson();
        $this->response = collect($curl->response)->toJson();
        return $this;
    }

    /**
     * @param $accessToken
     *
     * @return $this
     */
    public function setAccessToken($accessToken): self
    {
        $this->accessToken = $accessToken;
        $this->headers['Authorization'] = 'Bearer ' . $this->accessToken;
        return $this;
    }

    /**
     * @param $baseUrl
     *
     * @return $this
     */
    public function setBaseUrl($baseUrl): self
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }

    /**
     * @param $version
     *
     * @return $this
     */
    public function setApiVersion($version): self
    {
        $this->apiVersion = $version;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @return mixed
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @return mixed
     */
    public function getApiVersion()
    {
        return $this->apiVersion;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return mixed
     */
    public function getResponseHeaders()
    {
        return $this->responseHeaders;
    }

    /**
     * @return mixed
     */
    public function getResponseError()
    {
        return $this->responseError;
    }
}
