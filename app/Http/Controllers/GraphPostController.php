<?php

namespace App\Http\Controllers;

use App\Helpers\Tool;
use Illuminate\Http\Request;
use GuzzleHttp\Psr7\Stream;
use Microsoft\Graph\Exception\GraphException;
use Microsoft\Graph\Graph;

class GraphPostController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkToken');
    }

    public function makeRequest($method,$param, $toArray = true)
    {
        list($endpoint,$requestBody) = $param;
        try {
            $graph = new Graph();
            $graph->setBaseUrl("https://graph.microsoft.com/")
                ->setApiVersion("v1.0")
                ->setAccessToken(Tool::config('access_token'));
            $response = $graph->createRequest($method, $endpoint)
                ->addHeaders(["Content-Type" => "application/json"])
                ->attachBody($requestBody)
                ->setReturnType(Stream::class)
                ->execute();
            return $toArray ? json_decode($response->getContents(), true) : $response->getContents();
        } catch (GraphException $e) {
            Tool::showMessage($e->getCode().': 请检查地址是否正确', false);
            return null;
        }
    }

    public function uploadImage(Request $request)
    {

        if (!$request->isMethod('post'))
            return view('image');
        $field = 'olaindex_img';
        if (!$request->hasFile($field)) {
            $data =  ['code' => 500, 'message' => '上传文件为空'];
            return response()->json($data);
        }
        $file = $request->file($field);
        $rule = [$field => 'required|max:5096|image'];
        $validator = \Illuminate\Support\Facades\Validator::make(request()->all(), $rule);
        if ($validator->fails()) {
            $data = ['code' => 500, 'message' => $validator->errors()->first()];
            return response()->json($data);
        }
        if (!$file->isValid()) {
            $data =  ['code' => 500, 'message' => '文件上传出错'];
            return response()->json($data);
        }
        $path = $file->getRealPath();
        if (file_exists($path) && is_readable($path)) {
            $content = fopen($path,'r');
            $stream = \GuzzleHttp\Psr7\stream_for($content);
            $storeFilePath = 'share/Images/Cache/' . date('Y'). '/' . date('m'). '/'.$file->getClientOriginalName(); // 远程图片保存地址
            $remoteFilePath = trim($storeFilePath,'/');
            $endpoint = "/me/drive/root:/{$remoteFilePath}:/content";
            $requestBody = $stream;
            $response = $this->makeRequest('put',[$endpoint,$requestBody]);
            $data = [
                'code' => 200,
                'data' => [
                    'id' => $response['id'],
                    'filename'=> $response['name'],
                    'size' => $response['size'],
                    'time' => $response['lastModifiedDateTime'],
                    'url'=> route('view',$response['id']),
                    'delete' => route('delete',$response['id'])
                ]
            ];
            return response()->json($data);
        } else {
            $data =  ['code' => 500, 'message' => '无法获取文件内容'];
            return response()->json($data);
        }
    }

    public function deleteItem($itemId)
    {

    }
}
