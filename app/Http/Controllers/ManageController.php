<?php

namespace App\Http\Controllers;

use App\Helpers\Tool;
use Illuminate\Http\Request;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Artisan;

/**
 * 管理员 OneDrive 操作
 * Class ManageController
 * @package App\Http\Controllers
 */
class ManageController extends Controller
{
    /**
     * GraphController constructor.
     */
    public function __construct()
    {
        $this->middleware('checkAuth')->except(['uploadImage', 'deleteItem']);
        $this->middleware('checkToken');
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
            $data = ['code' => 500, 'message' => '上传文件为空'];
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
            $data = ['code' => 500, 'message' => '文件上传出错'];
            return response()->json($data);
        }
        $path = $file->getRealPath();
        if (file_exists($path) && is_readable($path)) {
            $content = fopen($path, 'r');
            $stream = \GuzzleHttp\Psr7\stream_for($content);
            $root = trim(Tool::config('root'), '/');
            $image_hosting_path = trim(Tool::config('image_hosting_path'), '/');
            $filePath = trim($image_hosting_path . '/' . date('Y') . '/' . date('m') . '/' . date('d') . '/' . str_random(8) . '/' . $file->getClientOriginalName(), '/');
            $storeFilePath = $root . '/' . $filePath; // 远程图片保存地址
            $remoteFilePath = trim($storeFilePath, '/');
            $endpoint = "/me/drive/root:/{$remoteFilePath}:/content";
            $requestBody = $stream;
            $graph = new RequestController();
            $response = $graph->requestGraph('put', [$endpoint, $requestBody, []], true);
            $sign = $response['id'] . '.' . encrypt($response['eTag']);
            $fileIdentifier = encrypt($sign);
            $data = [
                'code' => 200,
                'data' => [
                    'id' => $response['id'],
                    'filename' => $response['name'],
                    'size' => $response['size'],
                    'time' => $response['lastModifiedDateTime'],
                    'url' => route('view', $filePath),
                    'delete' => route('delete', $fileIdentifier)
                ]
            ];
            @unlink($path);
            return response()->json($data);
        } else {
            $data = ['code' => 500, 'message' => '无法获取文件内容'];
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
        if (!$request->isMethod('post')) return view('admin.file');
        $field = 'olaindex_file';
        $target_directory = $request->get('root', '/');
        if (!$request->hasFile($field)) {
            $data = ['code' => 500, 'message' => '上传文件或目录为空'];
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
            $data = ['code' => 500, 'message' => '文件上传出错'];
            return response()->json($data);
        }
        $path = $file->getRealPath();
        if (file_exists($path) && is_readable($path)) {
            $content = fopen($path, 'r');
            $stream = \GuzzleHttp\Psr7\stream_for($content);
            $storeFilePath = trim($target_directory, '/') . '/' . $file->getClientOriginalName(); // 远程保存地址
            $remoteFilePath = trim($storeFilePath, '/');
            $endpoint = "/me/drive/root:/{$remoteFilePath}:/content";
            $requestBody = $stream;
            $graph = new RequestController();
            $response = $graph->requestGraph('put', [$endpoint, $requestBody, []], true);
            $data = [
                'code' => 200,
                'data' => [
                    'id' => $response['id'],
                    'filename' => $response['name'],
                    'size' => $response['size'],
                    'time' => $response['lastModifiedDateTime'],
                ]
            ];
            @unlink($path);
            return response()->json($data);
        } else {
            $data = ['code' => 500, 'message' => '无法获取文件内容'];
            return response()->json($data);
        }
    }

    /**
     * @param $path
     * @param $url
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function uploadUrl($path, $url)
    {
        // 仅支持OneDrive个人版
        $path = trim(dirname($path));
        $endpoint = "/me/drive/items/{$path}/children";
        $graph = new RequestController();
        $data = [
            '@microsoft.graph.sourceUrl' => $url,
            'name' => pathinfo($path, PATHINFO_BASENAME),
            'file' => '{}',
        ];
        $requestBody = json_encode($data);
        $response = $graph->request('post', [$endpoint, $requestBody, ['Prefer' => 'respond-async']]);
        dd($response);
    }

    /**
     * 加密目录
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function lockFolder(Request $request)
    {
        $path = decrypt($request->get('path'));
        $password = $request->get('password', '12345678');
        $stream = \GuzzleHttp\Psr7\stream_for($password);
        $root = trim(Tool::config('root'), '/');
        $storeFilePath = trim($path, '/') . '/.password';
        $remoteFilePath = trim($root . '/' . trim($storeFilePath, '/'), '/'); // 远程保存地址
        $endpoint = "/me/drive/root:/{$remoteFilePath}:/content";
        $requestBody = $stream;
        $graph = new RequestController();
        $response = $graph->requestGraph('put', [$endpoint, $requestBody, []], true);
        $response ? Tool::showMessage('操作成功，请牢记密码！') : Tool::showMessage('加密失败！', false);
        Artisan::call('cache:clear');
        return redirect()->back();
    }

    /**
     * 新建文件
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function createFile(Request $request)
    {
        if (!$request->isMethod('post')) return view('admin.add');
        $name = $request->get('name');
        $path = decrypt($request->get('path'));
        $content = $request->get('content');
        $stream = \GuzzleHttp\Psr7\stream_for($content);
        $root = trim(Tool::config('root'), '/');
        $storeFilePath = $root . '/' . trim($path, '/') . '/' . $name . '.md';
        $remoteFilePath = trim($storeFilePath, '/');
        $endpoint = "/me/drive/root:/{$remoteFilePath}:/content";
        $requestBody = $stream;
        $graph = new RequestController();
        $response = $graph->requestGraph('put', [$endpoint, $requestBody, []], true);
        $response ? Tool::showMessage('添加成功！') : Tool::showMessage('添加失败！', false);
        Artisan::call('cache:clear');
        return redirect()->route('home', $path);

    }

    /**
     * 编辑文本文件
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateFile(Request $request, $id)
    {

        if (!$request->isMethod('post')) {
            $fetch = new FetchController();
            $file = $fetch->getFileById($id);
            $file['content'] = $fetch->getContentById($id);
            return view('admin.edit', compact('file'));
        }
        $content = $request->get('content');
        $stream = \GuzzleHttp\Psr7\stream_for($content);
        $endpoint = "/me/drive/items/{$id}/content";
        $requestBody = $stream;
        $graph = new RequestController();
        $response = $graph->requestGraph('put', [$endpoint, $requestBody, []], true);
        $response ? Tool::showMessage('修改成功！') : Tool::showMessage('修改失败！', false);
        Artisan::call('cache:clear');
        return redirect()->back();
    }

    /**
     * 创建目录
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createFolder(Request $request)
    {
        $path = decrypt($request->get('path'));
        $name = $request->get('name');
        $fetch = new FetchController();
        $graphPath = $fetch->convertPath($path);
        $req = new RequestController();
        if ($graphPath == '/')
            $endpoint = "/me/drive/root/children";
        else {
            $params = ['/me/drive/root' . $graphPath, '', []];
            $re = $req->requestGraph('get', $params, true);
            $itemId = $re['id'];
            $endpoint = "/me/drive/items/{$itemId}/children";
        }
        $requestBody = '{"name":"' . $name . '","folder":{},"@microsoft.graph.conflictBehavior":"rename"}';
        $response = $req->requestGraph('post', [$endpoint, $requestBody, []], true);
        $response ? Tool::showMessage('新建目录成功！') : Tool::showMessage('新建目录失败！', false);
        Artisan::call('cache:clear');
        return redirect()->back();
    }

    /**
     * 删除文件
     * @param $sign
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function deleteItem($sign)
    {
        try {
            $deCode = decrypt($sign);
        } catch (DecryptException $e) {
            Tool::showMessage($e->getMessage(), false);
            return view('message');
        }
        $reCode = explode('.', $deCode);
        $id = $reCode[0];
        try {
            $eTag = decrypt($reCode[1]);
        } catch (DecryptException $e) {
            Tool::showMessage($e->getMessage(), false);
            return view('message');
        }
        $endpoint = '/me/drive/items/' . $id;
        $graph = new RequestController();
        $graph->requestGraph('delete', [$endpoint, '', ['if-match' => $eTag]], true);
        Tool::showMessage('文件已删除');
        return view('message');
    }
}
