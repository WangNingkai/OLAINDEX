<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Service;

use Microsoft\Graph\Exception\GraphException;
use Log;

class GraphClient
{
    protected $graph;

    protected $method;

    protected $query;

    protected $returnStream = false;

    protected $headers = [];

    protected $body = '';

    public function __construct($accessToken, $restEndpoint)
    {
        $graph = new Graph();
        $graph->setAccessToken($accessToken)
            ->setBaseUrl($restEndpoint);
        $this->graph = $graph;
    }

    public function setApiVersion($version = 'v1.0'): GraphClient
    {
        $this->graph->setApiVersion($version);
        return $this;
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

    public function execute()
    {
        try {
            $query = $this->graph->createRequest($this->method, $this->query)
                ->setTimeout(3)
                ->addHeaders($this->headers)
                ->attachBody($this->body);
            $resp = $query->execute();
        } catch (GraphException $e) {
            Log::error('请求MsGraph网络错误 ' . $e->getMessage(), $e->getTrace());
            Log::error('请求参数', [
                'apiVersion' => 'v1.0',
                'method' => $this->method,
                'query' => $this->query,
            ]);
            return null;
        }
        return $resp;
    }
}
