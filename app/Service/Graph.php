<?php
/**
 * This file is part of the wangningkai/OLAINDEX.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Service;

use Microsoft\Graph\Core\GraphConstants;
use Microsoft\Graph\Http\GraphCollectionRequest;

class Graph
{
    /**
     * The access_token provided after authenticating
     * with Microsoft Graph (required)
     *
     * @var string
     */
    private $_accessToken;
    /**
     * The api version to use ("v1.0", "beta")
     * Default is "v1.0"
     *
     * @var string
     */
    private $_apiVersion;
    /**
     * The base url to call
     * Default is "https://graph.microsoft.com"
     *
     * @var string
     */
    private $_baseUrl;
    /**
     * The port to use for proxy requests
     * Null disables port forwarding
     *
     * @var string
     */
    private $_proxyPort;

    /**
     * Creates a new Graph object, which is used to call the Graph API
     */
    public function __construct()
    {
        $this->_apiVersion = GraphConstants::API_VERSION;
        $this->_baseUrl = GraphConstants::REST_ENDPOINT;
    }

    /**
     * Sets the Base URL to call (defaults to https://graph.microsoft.com)
     *
     * @param string $baseUrl The URL to call
     *
     * @return \Microsoft\Graph\Graph object
     */
    public function setBaseUrl($baseUrl)
    {
        $this->_baseUrl = $baseUrl;
        return $this;
    }

    /**
     * Sets the API version to use, e.g. "beta" (defaults to v1.0)
     *
     * @param string $apiVersion The API version to use
     *
     * @return Graph object
     */
    public function setApiVersion($apiVersion)
    {
        $this->_apiVersion = $apiVersion;
        return $this;
    }

    /**
     * Sets the access token. A valid access token is required
     * to run queries against Graph
     *
     * @param string $accessToken The user's access token, retrieved from
     *                     MS auth
     *
     * @return Graph object
     */
    public function setAccessToken($accessToken)
    {
        $this->_accessToken = $accessToken;
        return $this;
    }

    /**
     * Sets the proxy port. This allows you
     * to use tools such as Fiddler to view
     * requests and responses made with Guzzle
     *
     * @param string port The port number to use
     *
     * @return Graph object
     */
    public function setProxyPort($port)
    {
        $this->_proxyPort = $port;
        return $this;
    }

    /**
     * Creates a new request object with the given Graph information
     *
     * @param string $requestType The HTTP method to use, e.g. "GET" or "POST"
     * @param string $endpoint    The Graph endpoint to call
     *
     * @return GraphRequest The request object, which can be used to
     *                      make queries against Graph
     * @throws Exception\GraphException
     */
    public function createRequest($requestType, $endpoint)
    {
        return new GraphRequest(
            $requestType,
            $endpoint,
            $this->_accessToken,
            $this->_baseUrl,
            $this->_apiVersion,
            $this->_proxyPort
        );
    }

    /**
     * Creates a new collection request object with the given
     * Graph information
     *
     * @param string $requestType The HTTP method to use, e.g. "GET" or "POST"
     * @param string $endpoint    The Graph endpoint to call
     *
     * @return GraphCollectionRequest The request object, which can be
     *                                used to make queries against Graph
     * @throws Exception\GraphException
     */
    public function createCollectionRequest($requestType, $endpoint)
    {
        return new GraphCollectionRequest(
            $requestType,
            $endpoint,
            $this->_accessToken,
            $this->_baseUrl,
            $this->_apiVersion,
            $this->_proxyPort
        );
    }
}
