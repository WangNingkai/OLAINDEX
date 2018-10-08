<?php

namespace App\Http\Controllers;

use App\Helpers\Tool;
use Illuminate\Http\Request;
use GuzzleHttp\Psr7\Stream;
use Microsoft\Graph\Exception\GraphException;
use Microsoft\Graph\Graph;
use Illuminate\Contracts\Encryption\DecryptException;

class GraphPostController extends Controller
{
    /**
     * GraphPostController constructor.
     */
    public function __construct()
    {
        $this->middleware('checkToken');
    }

    /**
     * 构造请求
     * @param $method
     * @param $param
     * @param bool $toArray
     * @return mixed|null
     */
    public function makeRequest($method,$param, $toArray = true)
    {
        list($endpoint, $requestBody, $requestHeaders) = $param;
        $requestHeaders = $requestHeaders ?? [];
        $requestBody = $requestBody ?? '';
        $headers = array_merge($requestHeaders,["Content-Type" => "application/json"]);
        try {
            $graph = new Graph();
            $graph->setBaseUrl("https://graph.microsoft.com/")
                ->setApiVersion("v1.0")
                ->setAccessToken(Tool::config('access_token'));
            $response = $graph->createRequest($method, $endpoint)
                ->addHeaders($headers)
                ->attachBody($requestBody)
                ->setReturnType(Stream::class)
                ->execute();
            return $toArray ? json_decode($response->getContents(), true) : $response->getContents();
        } catch (GraphException $e) {
            Tool::showMessage($e->getCode().': 请检查地址是否正确', false);
            return null;
        }
    }

    /**
     * 图片上传
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
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
        $rule = [$field => 'required|max:4096|image'];
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
            $root = trim(Tool::config('root'),'/');
            $image_hosting_path = trim(Tool::config('image_hosting_path'),'/');
            $storeFilePath = $root. '/' . $image_hosting_path . '/' . date('Y'). '/' . date('m'). '/'.str_random(8).'/'.$file->getClientOriginalName(); // 远程图片保存地址
            $remoteFilePath = trim($storeFilePath,'/');
            $endpoint = "/me/drive/root:/{$remoteFilePath}:/content";
            $requestBody = $stream;
            $response = $this->makeRequest('put',[$endpoint,$requestBody,[]]);
            $sign = $response['id'] . '.' . encrypt($response['eTag']);
            $fileIdentifier = encrypt($sign);
            $data = [
                'code' => 200,
                'data' => [
                    'id' => $response['id'],
                    'filename'=> $response['name'],
                    'size' => $response['size'],
                    'time' => $response['lastModifiedDateTime'],
                    'url'=> route('origin.view',$response['id']),
                    'delete' => route('delete',$fileIdentifier)
                ]
            ];
            @unlink($path);
            return response()->json($data);
        } else {
            $data =  ['code' => 500, 'message' => '无法获取文件内容'];
            return response()->json($data);
        }
    }

    /**
     * 文件上传
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function uploadFile(Request $request)
    {
        if (!$request->isMethod('post'))
            return view('file');
        $field = 'olaindex_file';
        $target_directory = $request->get('root','/');
        if (!$request->hasFile($field)) {
            $data =  ['code' => 500, 'message' => '上传文件或目录为空'];
            return response()->json($data);
        }
        $file = $request->file($field);
        $rule = [$field => 'required|max:4096']; // 上传文件规则，单文件指定大小4M
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
            $storeFilePath = trim($target_directory,'/'). '/' .$file->getClientOriginalName(); // 远程保存地址
            $remoteFilePath = trim($storeFilePath,'/');
            $endpoint = "/me/drive/root:/{$remoteFilePath}:/content";
            $requestBody = $stream;
            $response = $this->makeRequest('put',[$endpoint,$requestBody,[]]);
            $data = [
                'code' => 200,
                'data' => [
                    'id' => $response['id'],
                    'filename'=> $response['name'],
                    'size' => $response['size'],
                    'time' => $response['lastModifiedDateTime'],
                    'url'=> route('origin.view',$response['id']),
                ]
            ];
            @unlink($path);
            return response()->json($data);
        } else {
            $data =  ['code' => 500, 'message' => '无法获取文件内容'];
            return response()->json($data);
        }

    }

    /**
     * 删除元素
     * @param $sign
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function deleteItem($sign)
    {
        try {
            $deCode = decrypt($sign);
        } catch (DecryptException $e) {
            Tool::showMessage($e->getMessage(),false);
            return view('message');
        }
        $reCode = explode('.' ,$deCode);
        $id = $reCode[0];
        try {
            $eTag = decrypt($reCode[1]);
        } catch (DecryptException $e) {
            Tool::showMessage($e->getMessage(),false);
            return view('message');
        }
        $endpoint = '/me/drive/items/' . $id;
        $this->makeRequest('delete',[$endpoint,'',['if-match' => $eTag]]);
        Tool::showMessage('文件已删除');
        return view('message');
    }
}
