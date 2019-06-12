<?php

namespace App\Http\Controllers;

use App\Service\OneDrive;
use App\Utils\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Validator;

class ApiController extends Controller
{
    /**
     * ApiController constructor.
     */
    public function __construct()
    {
        $this->middleware(['verify.token', 'verify.third.token']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws \ErrorException
     */
    public function imageUpload(Request $request)
    {
        $field = 'olaindex_img';
        if (!$request->hasFile($field)) {
            $data = ['errno' => 400, 'message' => '上传文件为空'];

            return response()->json($data, $data['errno']);
        }
        $file = $request->file($field);
        $rule = [$field => 'required|max:4096|image'];
        $validator = Validator::make(
            request()->all(),
            $rule
        );
        if ($validator->fails()) {
            $data = ['errno' => 400, 'message' => $validator->errors()->first()];
            return response()->json($data, $data['errno']);
        }
        if (!$file->isValid()) {
            $data = ['errno' => 400, 'message' => '文件上传出错'];
            return response()->json($data, $data['errno']);
        }
        $path = $file->getRealPath();
        if (file_exists($path) && is_readable($path)) {
            $content = file_get_contents($path);
            $hostingPath = Tool::encodeUrl(setting('image_hosting_path'));
            $middleName = '/' . date('Y') . '/' . date('m') . '/' . date('d') . '/' . Str::random(8) . '/';
            $filePath = trim($hostingPath . $middleName . $file->getClientOriginalName(), '/');
            $remoteFilePath = Tool::getOriginPath($filePath); // 远程图片保存地址
            $response = OneDrive::getInstance(one_account())->uploadByPath($remoteFilePath, $content);
            if ($response['errno'] === 0) {
                $sign = $response['data']['id'] . '.' . encrypt($response['data']['eTag']);
                $fileIdentifier = encrypt($sign);
                $data = [
                    'errno' => 200,
                    'data' => [
                        'id' => $response['data']['id'],
                        'filename' => $response['data']['name'],
                        'size' => $response['data']['size'],
                        'time' => $response['data']['lastModifiedDateTime'],
                        'url' => route('view', $filePath),
                        'delete' => route('delete', $fileIdentifier),
                    ],
                ];
                @unlink($path);

                return response()->json($data, $data['errno']);
            }
            return $response;
        }
        $data = ['errno' => 400, 'message' => '无法获取文件内容'];

        return response()->json($data, $data['errno']);
    }

}
