<?php

namespace App\Http\Controllers;

use App\Helpers\Tool;
use Illuminate\Http\Request;
use GuzzleHttp\Psr7\Stream;
use Microsoft\Graph\Core\GraphConstants;
use Microsoft\Graph\Exception\GraphException;
use Microsoft\Graph\Graph;

class GraphPostController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkToken');
    }

    public function upload(Request $request)
    {
        if (!$request->isMethod('post'))
            return view('dev');
        $field = 'olaindex_img';
        if (!$request->hasFile($field)) {
            $data =  ['status_code' => 500, 'message' => '上传文件为空'];
            return response()->json($data);
        }
        $file = $request->file($field);
        $rule = [$field => 'required|max:5096|image'];
        $validator = \Illuminate\Support\Facades\Validator::make(request()->all(), $rule);
        if ($validator->fails()) {
            $data = ['status_code' => 500, 'message' => $validator->errors()->first()];
            return response()->json($data);
        }
        if (!$file->isValid()) {
            $data =  ['status_code' => 500, 'message' => '文件上传出错'];
            return response()->json($data);
        }
        $path = $file->getRealPath();
        try {
            if (file_exists($path) && is_readable($path)) {
                $content = fopen($path,'r');
                $stream = \GuzzleHttp\Psr7\stream_for($content);
                $storeFilePath = 'share/Images/Cache/' . date('Y'). '/' . date('m'). '/'.$file->getClientOriginalName(); // 远程图片保存地址
                $remoteFilePath = trim($storeFilePath,'/');
                $graph = new Graph();
                $graph->setBaseUrl("https://graph.microsoft.com/")
                    ->setApiVersion("v1.0")
                    ->setAccessToken(Tool::config('access_token'));
                $response = $graph->createRequest("PUT", "/me/drive/root:/{$remoteFilePath}:/content")
                    ->attachBody($stream)
                    ->addHeaders(["Content-Type" => "application/json"])
                    ->setReturnType(Stream::class)
                    ->execute();
                $data = json_decode($response->getContents(),true);
                return response()->json($data);
            } else {
                throw new GraphException(GraphConstants::INVALID_FILE);
            }
        } catch (GraphException $e) {
            throw new $e(GraphConstants::INVALID_FILE);
        }
    }
}
