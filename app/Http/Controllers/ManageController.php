<?php

namespace App\Http\Controllers;

use App\Utils\Tool;
use App\Service\OneDrive;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Artisan;
use Cache;
use Validator;
use ErrorException;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * 管理员 OneDriveGraph 操作
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
        $this->middleware('auth')->except(['uploadImage', 'deleteItem']);
        $this->middleware(['verify.installation', 'verify.token',]);
    }


    /**
     * @param Request $request
     * @return ResponseFactory|Factory|JsonResponse|Response|View|mixed
     * @throws ErrorException
     */
    public function uploadFile(Request $request)
    {
        if (!$request->isMethod('post')) {
            return view(config('olaindex.theme') . 'admin.file');
        }
        $field = 'olaindex_file';
        $target_directory = $request->get('root', '/');
        if (!$request->hasFile($field)) {
            return response('上传文件或目录为空', 400);
        }
        $file = $request->file($field);
        $rule = [$field => 'required|max:4096']; // 上传文件规则，单文件指定大小4M
        $validator = Validator::make(
            request()->all(),
            $rule
        );
        if ($validator->fails()) {
            return response($validator->errors()->first(), 400);
        }
        if (!$file->isValid()) {
            return response('文件上传出错', 400);
        }
        $path = $file->getRealPath();
        if (file_exists($path) && is_readable($path)) {
            $content = file_get_contents($path);
            $storeFilePath = trim(Tool::encodeUrl($target_directory), '/') . '/' . $file->getClientOriginalName(); // 远程保存地址
            $remoteFilePath = Tool::getOriginPath($storeFilePath); // 远程文件保存地址
            $response = OneDrive::getInstance(one_account())->uploadByPath($remoteFilePath, $content);
            if ($response['errno'] === 0) {
                $data = [
                    'errno' => 200,
                    'data' => [
                        'id' => $response['data']['id'],
                        'filename' => $response['data']['name'],
                        'size' => $response['data']['size'],
                        'time' => $response['data']['lastModifiedDateTime'],
                    ],
                ];
                @unlink($path);

                return response()->json($data, $data['errno']);
            }
            return $response;
        }
        return response('无法获取文件内容', 400);
    }

    /**
     * @param Request $request
     *
     * @return Factory|RedirectResponse|View
     * @throws ErrorException
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
        $remoteFilePath
            = Tool::getOriginPath($storeFilePath); // 远程password保存地址
        $response = OneDrive::getInstance(one_account())->uploadByPath($remoteFilePath, $password);
        $response['errno'] === 0 ? Tool::showMessage('操作成功！')
            : Tool::showMessage('操作失败，请重试！', false);
        Cache::forget('one:list:' . Tool::getAbsolutePath($path));

        return redirect()->back();
    }

    /**
     * @param Request $request
     *
     * @return Factory|RedirectResponse|View
     * @throws ErrorException
     */
    public function createFile(Request $request)
    {
        if (!$request->isMethod('post')) {
            return view(config('olaindex.theme') . 'admin.add');
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
        $response = OneDrive::getInstance(one_account())->uploadByPath($remoteFilePath, $content);
        $response['errno'] === 0 ? Tool::showMessage('添加成功！')
            : Tool::showMessage('添加失败！', false);
        Cache::forget('one:list:' . Tool::getAbsolutePath($path));

        return redirect()->route('home', Tool::encodeUrl($path));
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return Factory|RedirectResponse|View
     * @throws ErrorException
     */
    public function updateFile(Request $request, $id)
    {
        if (!$request->isMethod('post')) {
            $response = OneDrive::getInstance(one_account())->getItem($id);
            if ($response['errno'] === 0) {
                $file = $response['data'];
                $file['content'] = Tool::getFileContent($file['@microsoft.graph.downloadUrl'], false);
            } else {
                Tool::showMessage('获取文件失败', false);
                $file = '';
            }

            return view(config('olaindex.theme') . 'admin.edit', compact('file'));
        }
        $content = $request->get('content');
        $response = OneDrive::getInstance(one_account())->upload($id, $content);
        $response['errno'] === 0
            ? Tool::showMessage('修改成功！')
            : Tool::showMessage('修改失败！', false);
        Artisan::call('cache:clear');
        return redirect()->back();
    }

    /**
     * @param Request $request
     *
     * @return Factory|RedirectResponse|View
     * @throws ErrorException
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
        $response = OneDrive::getInstance(one_account())->mkdirByPath($name, $graphPath);
        $response['errno'] === 0 ? Tool::showMessage('新建目录成功！')
            : Tool::showMessage('新建目录失败！', false);
        Cache::forget('one:list:' . Tool::getAbsolutePath($path));

        return redirect()->back();
    }

    /**
     * @param $sign
     *
     * @return Factory|View
     * @throws ErrorException
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
        $response = OneDrive::getInstance(one_account())->delete($id, $eTag);
        $response['errno'] === 0
            ? Tool::showMessage('文件已删除')
            : Tool::showMessage('文件删除失败', false);
        Artisan::call('cache:clear');

        return view(config('olaindex.theme') . 'message');
    }

    /**
     * @param Request $request
     *
     * @return mixed
     * @throws ErrorException
     */
    public function copyItem(Request $request)
    {
        $itemId = $request->get('source_id');
        $parentItemId = $request->get('target_id');
        $response = OneDrive::getInstance(one_account())->copy($itemId, $parentItemId);
        if ($response['errno'] === 0) {
            return response()->json(
                [
                    'code' => 200,
                    'data' => $response['data'],
                    'msg' => 'OK',
                ]
            );
        }
        return $response;
        // 返回复制进度链接
    }

    /**
     * @param Request $request
     *
     * @return mixed
     * @throws ErrorException
     */
    public function moveItem(Request $request)
    {
        $itemId = $request->get('source_id');
        $parentItemId = $request->get('target_id');
        $response = OneDrive::getInstance(one_account())->move($itemId, $parentItemId);

        if ($response['errno'] === 0) {
            return response()->json(
                [
                    'code' => 200,
                    'data' => $response['data'],
                    'msg' => 'OK',
                ]
            );
        }
        return $response;
    }

    /**
     * @param Request $request
     *
     * @return array|mixed
     * @throws ErrorException
     */
    public function uploadUrl(Request $request)
    {
        $remote = $request->get('path');
        $url = $request->get('url');
        $response = OneDrive::getInstance(one_account())->uploadUrl($remote, $url);

        if ($response['errno'] === 0) {
            return response()->json(
                [
                    'code' => 200,
                    'data' => $response['data'],
                    'msg' => 'OK',
                ]
            );
        }
        return $response;
    }


    /**
     * @param Request $request
     *
     * @return array|mixed
     * @throws ErrorException
     */
    public function createShareLink(Request $request)
    {
        $itemId = $request->get('id');
        $response = OneDrive::getInstance(one_account())->createShareLink($itemId);

        if ($response['errno'] === 0) {
            return response()->json(
                [
                    'code' => 200,
                    'data' => $response['data'],
                    'msg' => 'OK',
                ]
            );
        }
        return $response;
    }

    /**
     * @param Request $request
     *
     * @return array|mixed
     * @throws ErrorException
     */
    public function deleteShareLink(Request $request)
    {
        $itemId = $request->get('id');
        $response = OneDrive::getInstance(one_account())->deleteShareLink($itemId);

        if ($response['errno'] === 0) {
            return response()->json(
                [
                    'code' => 200,
                    'data' => $response['data'],
                    'msg' => 'OK',
                ]
            );
        }
        return $response;
    }

    /**
     * @param Request $request
     *
     * @return array|mixed
     * @throws ErrorException
     */
    public function pathToItemId(Request $request)
    {
        $graphPath = Tool::getOriginPath($request->get('path'));

        $response = OneDrive::getInstance(one_account())->pathToItemId($graphPath);

        if ($response['errno'] === 0) {
            return response()->json(
                [
                    'code' => 200,
                    'data' => $response['data'],
                    'msg' => 'OK',
                ]
            );
        }
        return $response;
    }
}
