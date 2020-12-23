<?php
/**
 * This file is part of the wangningkai/OLAINDEX.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Stream;
use Microsoft\Graph\Core\GraphConstants;
use Microsoft\Graph\Exception\GraphException;

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
     * True if the response should be returned as
     * a stream
     *
     * @var bool
     */
    protected $returnsStream;
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
     * Request options to decide if Guzzle Client should throw exceptions when http code is 4xx or 5xx
     *
     * @var bool
     */
    protected $http_errors;

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
        $this->http_errors = true;

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
     * Sets a http errors option
     *
     * @param string $http_errors A bool option to the Graph call
     *
     * @return GraphRequest
     */
    public function setHttpErrors($http_errors)
    {
        $this->http_errors = $http_errors;
        return $this;
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
     * Sets the return type of the response object
     *
     * @param mixed $returnClass The object class to use
     *
     * @return GraphRequest object
     */
    public function setReturnType($returnClass)
    {
        $this->returnType = $returnClass;
        if ($this->returnType === "GuzzleHttp\Psr7\Stream") {
            $this->returnsStream = true;
        } else {
            $this->returnsStream = false;
        }
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
        if (is_string($obj) || $obj instanceof Stream) {
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
     * Executes the HTTP request using Guzzle
     *
     * @param mixed $client The client to use in the request
     *
     * @return mixed object or array of objects
     *         of class $returnType
     * @throws GraphException if response is invalid
     *
     */
    public function execute($client = null)
    {
        if (is_null($client)) {
            $client = $this->createGuzzleClient();
        }
        try {
            $result = $client->request(
                $this->requestType,
                $this->_getRequestUrl(),
                [
                    'body' => $this->requestBody,
                    'timeout' => $this->timeout
                ]
            );
        } catch (RequestException $e) {
            $result = $e->getResponse();
        }

        if (!$result) {
            throw new GraphException(GraphConstants::UNABLE_TO_PARSE_RESPONSE);
        }


        // Check to see if returnType is a stream, if so return it immediately
        if ($this->returnsStream) {
            return $result->getBody();
        }

        // Wrap response in GraphResponse layer
        $response = new GraphResponse(
            $this,
            $result->getBody(),
            $result->getStatusCode(),
            $result->getHeaders()
        );

        // If no return type is specified, return GraphResponse
        $returnObj = $response;

        if ($this->returnType) {
            $returnObj = $response->getResponseAsObject($this->returnType);
        }
        return $returnObj;
    }

    /**
     * Executes the HTTP request asynchronously using Guzzle
     *
     * @param mixed $client The client to use in the request
     *
     * @return mixed object or array of objects
     *         of class $returnType
     */
    public function executeAsync($client = null)
    {
        if (is_null($client)) {
            $client = $this->createGuzzleClient();
        }

        return $client->requestAsync(
            $this->requestType,
            $this->_getRequestUrl(),
            [
                'body' => $this->requestBody,
                'timeout' => $this->timeout
            ]
        )->then(
        // On success, return the result/response
            function ($result) {

                // Check to see if returnType is a stream, if so return it immediately
                if ($this->returnsStream) {
                    return $result->getBody();
                }

                $response = new GraphResponse(
                    $this,
                    $result->getBody(),
                    $result->getStatusCode(),
                    $result->getHeaders()
                );
                $returnObject = $response;
                if ($this->returnType) {
                    $returnObject = $response->getResponseAsObject(
                        $this->returnType
                    );
                }
                return $returnObject;
            },
            // On fail, log the error and return null
            static function ($reason) {
                trigger_error("Async call failed: " . $reason->getMessage());
                return null;
            }
        );
    }

    /**
     * Download a file from OneDrive to a given location
     *
     * @param string $path The path to download the file to
     * @param mixed $client The client to use in the request
     *
     * @return null
     * @throws GraphException if file path is invalid
     *
     */
    public function download($path, $client = null)
    {
        if (is_null($client)) {
            $client = $this->createGuzzleClient();
        }
        try {
            $file = fopen($path, 'wb');
            if (!$file) {
                throw new GraphException(GraphConstants::INVALID_FILE);
            }

            $client->request(
                $this->requestType,
                $this->_getRequestUrl(),
                [
                    'body' => $this->requestBody,
                    'sink' => $file
                ]
            );
            if (is_resource($file)) {
                fclose($file);
            }
        } catch (GraphException $e) {
            throw new GraphException(GraphConstants::INVALID_FILE);
        }

        return null;
    }

    /**
     * Upload a file to OneDrive from a given location
     *
     * @param string $path The path of the file to upload
     * @param mixed $client The client to use in the request
     *
     * @return mixed DriveItem or array of DriveItems
     * @throws GraphException if file is invalid
     *
     */
    public function upload($path, $client = null)
    {
        if (is_null($client)) {
            $client = $this->createGuzzleClient();
        }
        try {
            if (file_exists($path) && is_readable($path)) {
                $file = fopen($path, 'r');
                $stream = \GuzzleHttp\Psr7\stream_for($file);
                $this->requestBody = $stream;
                return $this->execute($client);
            }

            throw new GraphException(GraphConstants::INVALID_FILE);
        } catch (GraphException $e) {
            throw new GraphException(GraphConstants::INVALID_FILE);
        }
    }

    /**
     * Get a list of headers for the request
     *
     * @return array The headers for the request
     */
    private function _getDefaultHeaders()
    {
        return [
            'Host' => $this->baseUrl,
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

    /**
     * Create a new Guzzle client
     * To allow for user flexibility, the
     * client is not reused. This allows the user
     * to set and change headers on a per-request
     * basis
     *
     * If a proxyPort was passed in the constructor, all
     * requests will be forwared through this proxy.
     *
     * @return \GuzzleHttp\Client The new client
     */
    protected function createGuzzleClient()
    {
        $clientSettings = [
            'base_uri' => $this->baseUrl,
            'http_errors' => $this->http_errors,
            'headers' => $this->headers
        ];
        if ($this->proxyPort !== null) {
            $clientSettings['verify'] = false;
            $clientSettings['proxy'] = $this->proxyPort;
        }
        return new Client($clientSettings);
    }

    public function getBaseUrl()
    {
        return $this->baseUrl . $this->apiVersion;
    }
}
