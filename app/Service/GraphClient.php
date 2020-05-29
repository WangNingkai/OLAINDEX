<?php
/**
 * This file is part of the wangningkai/OLAINDEX.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Service;


use App\Models\AccessToken;
use App\Models\Client;
use Curl\Curl;
use GuzzleHttp\Psr7\Stream;
use Log;

class GraphClient
{
    protected $graph;

    protected $method;

    protected $query;

    protected $returnStream = false;

    protected $headers = [];

    protected $body = '';

    public function __construct($id)
    {
        $token = new AccessToken($id);
        if (!$token) {
            throw new \RuntimeException('Not Found AccessToken.');
        }
        $accountType = $token->getAccountType();
        $clientConfig = (new Client())
            ->setAccountType($accountType);
        $accessToken = $token->getAccessToken();
        $graph = new Graph();
        $graph->setAccessToken($accessToken)
            ->setApiVersion('v1.0')
            ->setBaseUrl($clientConfig->getRestEndpoint());
        $this->graph = $graph;
    }

    public function setProxy($proxy): GraphClient
    {
        $this->graph->setProxyPort($proxy);
        return $this;
    }

    public function setMethod($method): GraphClient
    {
        $this->method = $method;
        return $this;
    }

    public function setQuery($query): GraphClient
    {
        $this->query = $query;
        return $this;
    }

    public function addHeaders($headers): GraphClient
    {
        $this->headers = $headers;
        return $this;
    }

    public function attachBody($body): GraphClient
    {
        $this->body = $body;
        return $this;
    }

    public function setReturnStream($returnStream): GraphClient
    {
        $this->returnStream = $returnStream;
        return $this;
    }

    public function execute()
    {
        try {
            $query = $this->graph->createRequest($this->method, $this->query)
                ->setHttpErrors(true)
                ->setReturnType($this->returnStream)
                ->setTimeout(3000)
                ->addHeaders($this->headers)
                ->attachBody($this->body);
            $resp = $query->execute();
        } catch (\Microsoft\Graph\Exception\GraphException $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return null;
        }
        if ($resp instanceof Stream) {

            $data = $resp->getContents();

            return is_json($data) ? json_decode($data, true) : $data;
        }
        return $resp;

    }

}
