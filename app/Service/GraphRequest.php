<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Service;

use Curl\Curl;
use Microsoft\Graph\Core\GraphConstants;
use Microsoft\Graph\Exception\GraphException;
use Log;

class GraphRequest
{
    /**
     * A valid access token
     *
     * @var string
     */
    protected $accessToken;
    /**
     * The API version to use ("v1.0", "beta")
     *
     * @var string
     */
    protected $apiVersion;
    /**
     * The base url to call
     *
     * @var string
     */
    protected $baseUrl;
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
     * The return type to cast the response as
     *
     * @var object
     */
    protected $returnType;
    /**
     * The timeout, in seconds
     *
     * @var string
     */
    protected $timeout;
    /**
     * The proxy port to use. Null to disable
     *
     * @var string
     */
    protected $proxyPort;

    /**
     * Constructs a new Graph Request object
     *
     * @param string $requestType The HTTP method to use, e.g. "GET" or "POST"
     * @param string $endpoint The Graph endpoint to call
     * @param string $accessToken A valid access token to validate the Graph call
     * @param string $baseUrl The base URL to call
     * @param string $apiVersion The API version to use
     * @param string $proxyPort The url where to proxy through
     * @throws GraphException when no access token is provided
     */
    public function __construct($requestType, $endpoint, $accessToken, $baseUrl, $apiVersion, $proxyPort = null)
    {
        $this->requestType = $requestType;
        $this->endpoint = $endpoint;
        $this->accessToken = $accessToken;

        if (!$this->accessToken) {
            throw new GraphException(GraphConstants::NO_ACCESS_TOKEN);
        }

        $this->baseUrl = $baseUrl;
        $this->apiVersion = $apiVersion;
        $this->timeout = 0;
        $this->headers = $this->_getDefaultHeaders();
        $this->proxyPort = $proxyPort;
    }

    /**
     * Sets a new accessToken
     *
     * @param string $accessToken A valid access token to validate the Graph call
     *
     * @return GraphRequest object
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
        $this->headers['Authorization'] = 'Bearer ' . $this->accessToken;
        return $this;
    }

    /**
     * Adds custom headers to the request
     *
     * @param array $headers An array of custom headers
     *
     * @return GraphRequest object
     */
    public function addHeaders($headers)
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    /**
     * Get the request headers
     *
     * @return array of headers
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Attach a body to the request. Will JSON encode
     * any Microsoft\Graph\Model objects as well as arrays
     *
     * @param mixed $obj The object to include in the request
     *
     * @return GraphRequest object
     */
    public function attachBody($obj)
    {
        // Attach streams & JSON automatically
        if (is_string($obj)) {
            $this->requestBody = $obj;
        } // By default, JSON-encode
        else {
            $this->requestBody = json_encode($obj);
        }
        return $this;
    }

    /**
     * Get the body of the request
     *
     * @return mixed request body of any type
     */
    public function getBody()
    {
        return $this->requestBody;
    }

    /**
     * Sets the timeout limit of the cURL request
     *
     * @param string $timeout The timeout in ms
     *
     * @return GraphRequest object
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * Executes the HTTP request
     *
     *
     * @return mixed object or array of objects
     *         of class $returnType
     *
     */
    public function execute()
    {
        $this->requestType = strtoupper($this->requestType);
        $options = [
            CURLOPT_CUSTOMREQUEST => $this->requestType,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
        ];
        if ($this->requestBody) {
            $options = array_add($options, CURLOPT_POST, true);
            $options = array_add(
                $options,
                CURLOPT_POSTFIELDS,
                $this->requestBody
            );
        }
        $curl = new Curl();
        $curl->verbose();
        $curl->setUserAgent('ISV|OLAINDEX|OLAINDEX/6.0');
        $curl->setHeaders($this->headers);
        $curl->setRetry(2);
        $curl->setConnectTimeout(5);
        $curl->setTimeout((int)$this->timeout);
        $curl->setUrl($this->baseUrl . $this->_getRequestUrl());
        $curl->setOpts($options);
        $curl->exec();
        $curl->close();
        if ($curl->error) {
            Log::error(
                'Request Graph Error.',
                [
                    'errorCode' => $curl->getErrorCode(),
                    'errorMessage' => $curl->getErrorMessage(),
                    'httpStatusCode' => $curl->getHttpStatusCode(),
                    'isHttpError' => $curl->isHttpError(),
                    'httpErrorMessage' => $curl->getHttpErrorMessage(),
                    'headers' => $curl->getResponseHeaders(),
                    'body' => $curl->getResponse()
                ]
            );
//            throw new GraphException(GraphConstants::UNABLE_TO_PARSE_RESPONSE);
        }
        return new GraphResponse(
            $this,
            collect($curl->response)->toJson(),
            $curl->getHttpStatusCode(),
            collect($curl->responseHeaders)->toJson()
        );
    }

    /**
     * Get a list of headers for the request
     *
     * @return array The headers for the request
     */
    private function _getDefaultHeaders()
    {
        return [
            'Content-Type' => 'application/json',
            'SdkVersion' => 'Graph-php-' . GraphConstants::SDK_VERSION,
            'Authorization' => 'Bearer ' . $this->accessToken
        ];
    }

    /**
     * Get the concatenated request URL
     *
     * @return string request URL
     */
    private function _getRequestUrl()
    {
        //Send request with opaque URL
        if (stripos($this->endpoint, "http") === 0) {
            return $this->endpoint;
        }

        return $this->apiVersion . $this->endpoint;
    }

    /**
     * Checks whether the endpoint currently contains query
     * parameters and returns the relevant concatenator for
     * the new query string
     *
     * @return string "?" or "&"
     */
    protected function getConcatenator()
    {
        if (strpos($this->endpoint, "?") === false) {
            return "?";
        }
        return "&";
    }

    public function getBaseUrl()
    {
        return $this->baseUrl . $this->apiVersion;
    }
}
