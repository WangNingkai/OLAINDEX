<?php

namespace App\Http\Controllers;

use App\Helpers\Tool;
use Illuminate\Http\Request;
use GuzzleHttp\Psr7\Stream;
use Microsoft\Graph\Graph;

class GraphPostController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkToken');
    }

    public function upload(Request $request)
    {
        /*try {
            if (file_exists($path) && is_readable($path)) {
                $file = fopen($path, 'r');
                $stream = \GuzzleHttp\Psr7\stream_for($file);
                $this->requestBody = $stream;
                return $this->execute($client);
            } else {
                throw new GraphException(GraphConstants::INVALID_FILE);
            }
        } catch(GraphException $e) {
            throw new GraphException(GraphConstants::INVALID_FILE);
        }*/
        $file = $request->file('img');
        $remoteFilePath = ''; // 远程图片保存地址
        $graph = new Graph();
        $graph->setBaseUrl("https://graph.microsoft.com/")
                ->setApiVersion("v1.0")
                ->setAccessToken(Tool::config('access_token'));
        $response = $graph->createRequest("PUT", "/me/drive/root/children/{$remoteFilePath}/content")
            ->attachBody($file)
            ->addHeaders(["Content-Type" => "application/json"])
            ->setReturnType(Stream::class)
            ->execute();
        return $response->getContents();
    }
}
