<?php


namespace App\Http\Controllers;

use App\Service\OneDrive;
use App\Utils\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Validator;
use ErrorException;

class ImageController extends Controller
{
    public function __construct()
    {
        $this->middleware(['verify.installation', 'verify.token', 'verify.image']);
        $this->middleware('throttle:' . setting('image_upload_throttle', 5))->only('upload');
    }

    /**
     * 图床
     * @return Factory|View
     */
    public function index()
    {
        return view(config('olaindex.theme') . 'image');
    }

    /**
     * 图床上传图片
     * @param Request $request
     * @return ResponseFactory|JsonResponse|Response|mixed
     * @throws ErrorException
     */
    public function upload(Request $request)
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
            return response($validator->errors()->first(), 400);
        }
        if (!$file->isValid()) {
            return response('文件上传出错', 400);
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
        return response('无法获取文件内容', 400);
    }
}
