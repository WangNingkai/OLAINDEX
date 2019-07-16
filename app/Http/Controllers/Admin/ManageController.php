<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Tool;
use App\Helpers\OneDrive;
use Illuminate\Http\Request;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;

/**
 * 管理员 OneDriveGraph 操作
 *
 * @package App\Http\Controllers
 */
class ManageController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     * @throws \ErrorException
     */
    public function uploadImage(Request $request)
    {
        if (!$request->isMethod('post')) {
            return view(config('olaindex.theme') . 'image');
        }

        $field = 'olaindex_img';

        if (!$request->hasFile($field)) {
            $data = ['errno' => 400, 'message' => '上传文件为空'];

            return response()->json($data, $data['errno']);
        }

        $file = $request->file($field);
        $rule = [$field => 'required|max:4096|image'];
        $validator = $request->validate($rule);

        if ($validator->fails()) {
            return response($validator->errors()->first(), 400);
        }

        if (!$file->isValid()) {
            return response('文件上传出错', 400);
        }

        $path = $file->getRealPath();

        if (file_exists($path) && is_readable($path)) {
            $content = file_get_contents($path);
            $hostingPath = Tool::getEncodeUrl(Tool::config('image_hosting_path'));
            $middleName = '/' . date('Y') . '/' . date('m') . '/'
                . date('d') . '/' . Str::random(8) . '/';
            $filePath = trim($hostingPath . $middleName
                . $file->getClientOriginalName(), '/');
            $remoteFilePath = Tool::getOriginPath($filePath); // 远程图片保存地址
            $response = OneDrive::uploadByPath($remoteFilePath, $content);

            if ($response['errno'] === 0) {
                $sign = $response['data']['id'] . '.'
                    . encrypt($response['data']['eTag']);
                $fileIdentifier = encrypt($sign);
                $data = [
                    'errno' => 200,
                    'data'  => [
                        'id'       => $response['data']['id'],
                        'filename' => $response['data']['name'],
                        'size'     => $response['data']['size'],
                        'time'     => $response['data']['lastModifiedDateTime'],
                        'url'      => route('view', $filePath),
                        'delete'   => route('delete', $fileIdentifier),
                    ],
                ];
                @unlink($path);

                return response()->json($data, $data['errno']);
            } else {
                return $response;
            }
        } else {
            return response('无法获取文件内容', 400);
        }
    }

    public function showFile()
    {
        return themeView('admin.onedrive.file');
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View|mixed
     * @throws \ErrorException
     */
    public function uploadFile(Request $request)
    {
        $file = $request->file('olaindex_file');
        $validator = $request->validate([
            'olaindex_file' => 'required|file|max:4096',
            'root'          => 'nullable|string',
        ]);

        $validator['root'] = Arr::get($validator, 'root', '/');
        $path = $validator['olaindex_file']->path();

        if (file_exists($path) && is_readable($path)) {
            $content = file_get_contents($path);
            $storeFilePath = trim(Tool::getEncodeUrl($validator['root']), '/')
                . '/' . $file->getClientOriginalName(); // 远程保存地址
            $remoteFilePath = Tool::getOriginPath($storeFilePath); // 远程文件保存地址
            $response = OneDrive::uploadByPath($remoteFilePath, $content);
            if ($response['errno'] === 0) {
                return $this->success([
                    'id'       => Arr::get($response, 'data.id'),
                    'filename' => Arr::get($response, 'data.name'),
                    'size'     => Arr::get($response, 'data.size'),
                    'time'     => Arr::get($response, 'data.lastModifiedDateTime'),
                ]);
            } else {
                return $response;
            }
        } else {
            return response('无法获取文件内容', 400);
        }
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws \ErrorException
     */
    public function lockFolder(Request $request)
    {
        try {
            $path = decrypt($request->get('path'));
        } catch (DecryptException $e) {
            Tool::showMessage($e->getMessage(), false);

            return view(config('olaindex.theme') . 'message');
        }
        $password = $request->get('password', '');
        $storeFilePath = trim($path, '/') . '/.password';
        $remoteFilePath = Tool::getOriginPath($storeFilePath); // 远程password保存地址
        $response = OneDrive::uploadByPath($remoteFilePath, $password);
        $response['errno'] === 0 ? Tool::showMessage('操作成功！')
            : Tool::showMessage('操作失败，请重试！', false);
        Cache::forget('one' . app('onedrive')->id . ':list:' . Tool::getAbsolutePath($path));

        return redirect()->back();
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws \ErrorException
     */
    public function createFile(Request $request)
    {
        if (!$request->isMethod('post')) {
            return themeView('admin.onedrive.add');
        }

        $name = $request->get('name');
        try {
            $path = decrypt($request->get('path'));
        } catch (DecryptException $e) {
            Tool::showMessage($e->getMessage(), false);

            return view(config('olaindex.theme') . 'message');
        }
        $content = $request->get('content');
        $storeFilePath = trim($path, '/') . '/' . $name . '.md';
        $remoteFilePath = Tool::getOriginPath($storeFilePath); // 远程md保存地址
        $response = OneDrive::uploadByPath($remoteFilePath, $content);
        $response['errno'] === 0 ? Tool::showMessage('添加成功！') : Tool::showMessage('添加失败！', false);
        Cache::forget('one' . app('onedrive')->id . ':list:' . Tool::getAbsolutePath($path));

        return redirect()->route('home', Tool::getEncodeUrl($path));
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws \ErrorException
     */
    public function updateFile(Request $request, $id)
    {
        if (!$request->isMethod('post')) {
            $response = OneDrive::getItem($id);
            if ($response['errno'] === 0) {
                $file = $response['data'];
                $file['content'] = getFileContent($file['@microsoft.graph.downloadUrl']);
            } else {
                Tool::showMessage('获取文件失败', false);
                $file = '';
            }

            return themeView('admin.onedrive.edit', compact('file'));
        }
        $content = $request->get('content');
        $response = OneDrive::upload($id, $content);
        $response['errno'] === 0 ? Tool::showMessage('修改成功！') : Tool::showMessage('修改失败！', false);
        Artisan::call('cache:clear');
        return redirect()->back();
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws \ErrorException
     */
    public function createFolder(Request $request)
    {
        try {
            $path = decrypt($request->get('path'));
        } catch (DecryptException $e) {
            Tool::showMessage($e->getMessage(), false);

            return view(config('olaindex.theme') . 'message');
        }
        $name = $request->get('name');
        $graphPath = Tool::getOriginPath($path);
        $response = OneDrive::mkdirByPath($name, $graphPath);
        $response['errno'] === 0 ? Tool::showMessage('新建目录成功！')
            : Tool::showMessage('新建目录失败！', false);
        Cache::forget('one' . app('onedrive')->id . ':list:' . Tool::getAbsolutePath($path));

        return redirect()->back();
    }

    /**
     * @param $sign
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \ErrorException
     */
    public function deleteItem($sign)
    {
        try {
            $deCode = decrypt($sign);
        } catch (DecryptException $e) {
            Tool::showMessage($e->getMessage(), false);

            return view(config('olaindex.theme') . 'message');
        }
        $reCode = explode('.', $deCode);
        $id = $reCode[0];
        try {
            $eTag = decrypt($reCode[1]);
        } catch (DecryptException $e) {
            Tool::showMessage($e->getMessage(), false);

            return view(config('olaindex.theme') . 'message');
        }
        $response = OneDrive::delete($id, $eTag);
        $response['errno'] === 0 ? Tool::showMessage('文件已删除')
            : Tool::showMessage('文件删除失败', false);
        Artisan::call('cache:clear');

        return view(config('olaindex.theme') . 'message');
    }

    /**
     * @param Request $request
     *
     * @return mixed
     * @throws \ErrorException
     */
    public function copyItem(Request $request)
    {
        $itemId = $request->get('source_id');
        $parentItemId = $request->get('target_id');
        $response = OneDrive::copy($itemId, $parentItemId);
        if ($response['errno'] === 0) {
            return $this->success($response['data']);
        } else {
            return $response;
        } // 返回复制进度链接
    }

    /**
     * @param Request $request
     *
     * @return mixed
     * @throws \ErrorException
     */
    public function moveItem(Request $request)
    {
        $itemId = $request->get('source_id');
        $parentItemId = $request->get('target_id');
        $response = OneDrive::move($itemId, $parentItemId);

        if ($response['errno'] === 0) {
            return $this->success($response['data']);
        } else {
            return $response;
        }
    }

    /**
     * @param Request $request
     *
     * @return array|mixed
     * @throws \ErrorException
     */
    public function uploadUrl(Request $request)
    {
        $remote = $request->get('path');
        $url = $request->get('url');
        $response = OneDrive::uploadUrl($remote, $url);

        if ($response['errno'] === 0) {
            return $this->success($response['data']);
        } else {
            return $response;
        }
    }

    /**
     * @param Request $request
     *
     * @return array|mixed
     * @throws \ErrorException
     */
    public function createShareLink(Request $request)
    {
        $itemId = $request->get('id');
        $response = OneDrive::createShareLink($itemId);

        if ($response['errno'] === 0) {
            return $this->success($response['data']);
        } else {
            return $response;
        }
    }

    /**
     * @param Request $request
     *
     * @return array|mixed
     * @throws \ErrorException
     */
    public function deleteShareLink(Request $request)
    {
        $itemId = $request->get('id');
        $response = OneDrive::deleteShareLink($itemId);

        if ($response['errno'] === 0) {
            return response()->json(
                [
                    'code' => 200,
                    'data' => $response['data'],
                    'msg'  => 'OK',
                ]
            );
        } else {
            return $response;
        }
    }

    /**
     * @param Request $request
     *
     * @return array|mixed
     * @throws \ErrorException
     */
    public function pathToItemId(Request $request)
    {
        $graphPath = Tool::getOriginPath($request->get('path'));

        $response = OneDrive::pathToItemId($graphPath);

        if ($response['errno'] === 0) {
            return $this->success($response['data']);
        } else {
            return $response;
        }
    }
}
