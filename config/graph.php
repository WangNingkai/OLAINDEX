<?php

return [
    'clientId'                => env('GRAPH_CLIENT_ID'),
    'clientSecret'            => env('GRAPH_CLIENT_SECRET'),
    'redirectUri'             => env('GRAPH_REDIRECT_URI'),
    'urlAuthorize'            => env('GRAPH_AUTHORITY_URL') . env('GRAPH_AUTHORIZE_ENDPOINT'),
    'urlAccessToken'          => env('GRAPH_AUTHORITY_URL') . env('GRAPH_TOKEN_ENDPOINT'),
    'urlResourceOwnerDetails' => '',
    'scopes'                  => env('GRAPH_SCOPES')
];
