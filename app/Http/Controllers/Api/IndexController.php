<?php
/**
 * This file is part of the wangningkai/OLAINDEX.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Account;
use App\Service\GraphErrorEnum;
use Illuminate\Http\Request;
use Validator;

class IndexController extends BaseController
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware('access_token');
        $api_limit = setting('api_limit', 10);
        $this->middleware("throttle:{$api_limit},1");
    }

    public function index()
    {
        return $this->success();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function imageUpload(Request $request)
    {
        $accounts = Account::fetchlist();
        $account_id = 0;
        $hash = '';
        if ($accounts) {
            $account_id = setting('image_host_account');
            if (!$account_id) {
                $account_id = setting('primary_account', 0);
            }
            if (!$account_id) {
                $account_id = array_get($accounts->first(), 'id');
            }
            $account = $accounts->where('id', $account_id)->first();
            $hash = array_get($account, 'hash_id');
        }
        if (!$account_id) {
            return $this->fail('账号不存在', 404);
        }

        $account = Account::find($account_id);
        if (!$account) {
            return $this->fail('账号不存在', 404);
        }
        $config = $account->config;

        $field = 'olaindex_img';
        if (!$request->hasFile($field)) {
            return $this->fail('上传文件为空', 400);
        }
        $file = $request->file($field);
        $rule = [$field => 'required|max:4096|image'];
        $validator = Validator::make(
            $request->all(),
            $rule
        );
        if ($validator->fails()) {
            return $this->fail($validator->errors()->first(), 400);
        }
        if (!$file->isValid()) {
            return $this->fail('文件上传出错', 400);
        }
        $path = $file->getRealPath();
        if (file_exists($path) && is_readable($path)) {
            $content = file_get_contents($path);
            $hostingPath = url_encode(array_get($config, 'image_path', '/'));
            $middleName = '/' . date('Y') . '/' . date('m') . '/' . date('d') . '/' . str_random(8) . '/';
            $filePath = trim($hostingPath . $middleName . $file->getClientOriginalName(), '/');
            $root = array_get($config, 'root', '/');
            $root = trim($root, '/');
            $query = "{$root}/$filePath";
            $service = $account->getOneDriveService();
            $resp = $service->upload($query, $content);
            if (array_key_exists('code', $resp)) {
                $msg = array_get($resp, 'message', '文件上传出错');
                $msg = GraphErrorEnum::get($resp['code']) ?? $msg;
                return $this->fail($msg, 400);
            }
            $data = [
                'item' => $resp,
                'filename' => $resp['name'],
                'size' => $resp['size'],
                'time' => $resp['lastModifiedDateTime'],
                'url' => shorten_url(route('drive.query', ['hash' => $hash, 'query' => url_encode($filePath), 'download' => 1])),
            ];
            @unlink($path);
            return $this->success($data);
        }
        return $this->fail('无法获取文件内容', 400);
    }
}
