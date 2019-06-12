<?php

namespace App\Entities;

class ClientConfigEntity extends BaseEntity
{
    /**
     * @var string
     */
    public $client_id;
    /**
     * @var string
     */
    public $client_secret;
    /**
     * @var string
     */
    public $redirect_uri;
    /**
     * @var string
     */
    public $authorize_url;
    /**
     * @var string
     */
    public $authorize_endpoint;
    /**
     * @var string
     */
    public $token_endpoint;
    /**
     * @var string
     */
    public $graph_endpoint;
    /**
     * @var string
     */
    public $api_version;
    /**
     * @var string
     */
    public $scopes;
}
