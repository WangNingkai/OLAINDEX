<?php

namespace App\Http\Controllers;

use App\Helpers\Tool;
use App\Helpers\OneDrive;
use Illuminate\Http\Request;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;

/**
 * 管理员 OneDrive 操作
 * Class ManageController
 *
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
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function uploadImage(Request $request)
    {
        if (!$request->isMethod('post')) {
            return view('image');
        }
        $field = 'olaindex_img';
        if (!$request->hasFile($field)) {
            $data = ['code' => 500, 'message' => '上传文件为空'];

            return response()->json($data);
        }
        $file = $request->file($field);
        $rule = [$field => 'required|max:4096|image'];
        $validator = Validator::make(
            request()->all(),
            $rule
        );
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
            $content = file_get_contents($path);
            $hostingPath
                = Tool::getEncodeUrl(Tool::config('image_hosting_path'));
            $middleName = '/'.date('Y').'/'.date('m').'/'
                .date('d').'/'.str_random(8).'/';
            $filePath = $hostingPath.$middleName.$file->getClientOriginalName();
            $remoteFilePath = Tool::getRequestPath($filePath); // 远程图片保存地址
            $result = OneDrive::uploadByPath($remoteFilePath, $content);
            $response = OneDrive::responseToArray($result);
            if ($response['code'] === 200) {
                $sign = $response['data']['id'].'.'
                    .encrypt($response['data']['eTag']);
                $fileIdentifier = encrypt($sign);
                $data = [
                    'code' => 200,
                    'data' => [
                        'id'       => $response['data']['id'],
                        'filename' => $response['data']['name'],
                        'size'     => $response['data']['size'],
                        'time'     => $response['data']['lastModifiedDateTime'],
                        'url'      => route('view', $filePath),
                        'delete'   => route('delete', $fileIdentifier),
                    ],
                ];
                @unlink($path);

                return response()->json($data);
            } else {
                return $result;
            }
        } else {
            $data = ['code' => 500, 'message' => '无法获取文件内容'];

            return response()->json($data);
        }
    }


    /**
     * 文件上传
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function uploadFile(Request $request)
    {
        if (!$request->isMethod('post')) {
            return view('admin.file');
        }
        $field = 'olaindex_file';
        $target_directory = $request->get('root', '/');
        if (!$request->hasFile($field)) {
            $data = ['code' => 500, 'message' => '上传文件或目录为空'];

            return response()->json($data);
        }
        $file = $request->file($field);
        $rule = [$field => 'required|max:4096']; // 上传文件规则，单文件指定大小4M
        $validator = Validator::make(
            request()->all(),
            $rule
        );
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
            $content = file_get_contents($path);
            $storeFilePath = trim(Tool::getEncodeUrl($target_directory), '/')
                .'/'.$file->getClientOriginalName(); // 远程保存地址
            $remoteFilePath = Tool::getRequestPath($storeFilePath); // 远程文件保存地址
            $result = OneDrive::uploadByPath($remoteFilePath, $content);
            $response = OneDrive::responseToArray($result);
            if ($response['code'] = 200) {
                $data = [
                    'code' => 200,
                    'data' => [
                        'id'       => $response['data']['id'],
                        'filename' => $response['data']['name'],
                        'size'     => $response['data']['size'],
                        'time'     => $response['data']['lastModifiedDateTime'],
                    ],
                ];
                @unlink($path);

                return response()->json($data);
            } else {
                return $result;
            }
        } else {
            $data = ['code' => 500, 'message' => '无法获取文件内容'];

            return response()->json($data);
        }
    }

    /**
     * 加密目录
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function lockFolder(Request $request)
    {
        try {
            $path = decrypt($request->get('path'));
        } catch (DecryptException $e) {
            Tool::showMessage($e->getMessage(), false);

            return view('message');
        }
        $password = $request->get('password', '12345678');
        $storeFilePath = trim($path, '/').'/.password';
        $remoteFilePath
            = Tool::getRequestPath($storeFilePath); // 远程password保存地址
        $result = OneDrive::uploadByPath($remoteFilePath, $password);
        $response = OneDrive::responseToArray($result);
        $response['code'] === 200 ? Tool::showMessage('操作成功，请牢记密码！')
            : Tool::showMessage('加密失败！', false);
        Artisan::call('cache:clear');

        return redirect()->back();
    }

    /**
     * 新建 head/readme.md 文件
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createFile(Request $request)
    {
        if (!$request->isMethod('post')) {
            return view('admin.add');
        }
        $name = $request->get('name');
        try {
            $path = decrypt($request->get('path'));
        } catch (DecryptException $e) {
            Tool::showMessage($e->getMessage(), false);

            return view('message');
        }
        $content = $request->get('content');
        $storeFilePath = trim($path, '/').'/'.$name.'.md';
        $remoteFilePath = Tool::getRequestPath($storeFilePath); // 远程md保存地址
        $result = OneDrive::uploadByPath($remoteFilePath, $content);
        $response = OneDrive::responseToArray($result);
        $response['code'] === 200 ? Tool::showMessage('添加成功！')
            : Tool::showMessage('添加失败！', false);
        Artisan::call('cache:clear');

        return redirect()->route('home', Tool::getEncodeUrl($path));
    }

    /**
     * 编辑文本文件
     *
     * @param Request $request
     * @param         $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws \ErrorException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateFile(Request $request, $id)
    {
        if (!$request->isMethod('post')) {
            $result = OneDrive::getItem($id);
            $response = OneDrive::responseToArray($result);
            if ($response['code'] === 200) {
                $file = $response['data'];
                $file['content']
                    = Tool::getFileContent($file['@microsoft.graph.downloadUrl']);
            } else {
                Tool::showMessage('获取文件失败', false);
                $file = '';
            }

            return view('admin.edit', compact('file'));
        }
        $content = $request->get('content');
        $result = OneDrive::upload($id, $content);
        $response = OneDrive::responseToArray($result);
        $response['code'] === 200 ? Tool::showMessage('修改成功！')
            : Tool::showMessage('修改失败！', false);
        Artisan::call('cache:clear');

        return redirect()->back();
    }

    /**
     * 创建目录
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createFolder(Request $request)
    {
        try {
            $path = decrypt($request->get('path'));
        } catch (DecryptException $e) {
            Tool::showMessage($e->getMessage(), false);

            return view('message');
        }
        $name = $request->get('name');
        $graphPath = Tool::getRequestPath($path);
        $result = OneDrive::mkdirByPath($name, $graphPath);
        $response = OneDrive::responseToArray($result);
        $response['code'] === 200 ? Tool::showMessage('新建目录成功！')
            : Tool::showMessage('新建目录失败！', false);
        Artisan::call('cache:clear');

        return redirect()->back();
    }

    /**
     * 删除文件
     *
     * @param $sign
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \GuzzleHttp\Exception\GuzzleException
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
        $result = OneDrive::delete($id, $eTag);
        $response = OneDrive::responseToArray($result);
        $response['code'] === 200 ? Tool::showMessage('文件已删除')
            : Tool::showMessage('文件删除失败', false);
        Artisan::call('cache:clear');

        return view('message');
    }

    /**
     * 复制文件
     *
     * @param Request $request
     *
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function copyItem(Request $request)
    {
        $itemId = $request->get('source_id');
        $parentItemId = $request->get('target_id');
        $response = OneDrive::copy($itemId, $parentItemId);

        return $response; // 返回复制进度链接
    }

    /**
     * 移动文件
     *
     * @param Request $request
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function moveItem(Request $request)
    {
        $itemId = $request->get('source_id');
        $parentItemId = $request->get('target_id');
        $response = OneDrive::move($itemId, $parentItemId);

        return $response;
    }

    /**
     * 离线下载（个人版）
     *
     * @param Request $request
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function uploadUrl(Request $request)
    {
        $remote = $request->get('path');
        $url = $request->get('url');
        $response = OneDrive::uploadUrl($remote, $url);

        return $response;
    }


    /**
     * 创建分享链接
     *
     * @param Request $request
     *
     * @return false|mixed|\Psr\Http\Message\ResponseInterface|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createShareLink(Request $request)
    {
        $itemId = $request->get('id');
        $response = OneDrive::createShareLink($itemId);

        return $response; // 返回分享链接
    }

    /**
     * 删除分享链接
     *
     * @param Request $request
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteShareLink(Request $request)
    {
        $itemId = $request->get('id');
        $response = OneDrive::deleteShareLink($itemId);

        return $response;
    }

    /**
     * 路径转id
     *
     * @param Request $request
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function pathToItemId(Request $request)
    {
        $graphPath = Tool::getRequestPath($request->get('path'));

        return OneDrive::pathToItemId($graphPath);
    }
}
