<?php
/**
 * This file is part of the wangningkai/OLAINDEX.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Http\Controllers;


use App\Http\Traits\ApiResponseTrait;
use App\Models\Account;
use App\Service\GraphErrorEnum;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Cache;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use App\Helpers\Tool;

class ManageController extends BaseController
{
    use ApiResponseTrait;

    /**
     * 管理列表
     * @param Request $request
     * @param $account_id
     * @param string $query
     * @return mixed
     */
    public function query(Request $request, $account_id, $query = '')
    {
        $view = '';
        if (!$account_id) {
            abort(404, '未知账号！');
        }
        $account = Account::find($account_id);
        if (!$account) {
            abort(404, '未知账号！');
        }
        // 资源处理
        $root = $account->config['root'] ?? '/';
        $rawQuery = rawurldecode($query);
        $rawQuery = trim($rawQuery, '/');
        $query = strtolower($rawQuery);
        $path = explode('/', $rawQuery);
        $path = array_filter($path);
        $query = trans_absolute_path(trim("{$root}/$query", '/'));

        $service = $account->getOneDriveService();
        // 缓存处理
        $item = Cache::remember("d:item:{$account_id}:{$query}", setting('cache_expires'), function () use ($service, $query) {
            return $service->fetchItem($query);
        });
        if (array_key_exists('code', $item)) {
            $msg = array_get($item, 'message', '404NotFound');
            $msg = GraphErrorEnum::get($item['code']) ?? $msg;
            Cache::forget("d:item:{$account_id}:{$query}");
            abort(500, $msg);
        }

        // 处理文件
        $isFile = false;
        if (array_key_exists('file', $item)) {
            $isFile = true;
        }

        if ($isFile) {
            return redirect()->back();
        }

        $list = Cache::remember("d:list:{$account_id}:{$query}", setting('cache_expires'), function () use ($service, $query) {
            return $service->fetchList($query);
        });
        if (array_key_exists('code', $list)) {
            $msg = array_get($list, 'message', '404NotFound');
            $msg = GraphErrorEnum::get($list['code']) ?? $msg;
            Cache::forget("d:list:{$account_id}:{$query}");
            abort(500, $msg);
        }
        $list = collect($list)->lazy();
        // 资源处理
        $list = $this->formatItem($list);
        //搜索处理
        $keywords = $request->get('keywords');
        if ($keywords) {
            $list = $this->search($list, $keywords);
        }
        $readme = $list->filter(function ($item) {
            $name = strtoupper(trim(array_get($item, 'name', '')));
            return $name === 'README.MD';
        });
        $readme = $readme->first();
        if (!blank($readme)) {
            $readme_id = $readme['id'];
            Cache::add("d:item:{$account_id}:$readme_id", $readme, setting('cache_expires'));
        }

        // 资源排序
        $sortBy = $request->get('sortBy', 'name');
        $direction = 'asc';
        $column = 'name';
        if (str_contains($sortBy, ',')) {
            [$column, $direction] = explode(',', $sortBy);
        }
        $descending = $direction === 'desc';
        $list = $this->sort($list, $column, $descending);

        $list = $this->paginate($list, 20, false);

        return view('admin.file-manage' . $view, compact('account_id', 'account', 'readme', 'path', 'query', 'item', 'list', 'keywords'));
    }

    /**
     * 刷新页面
     * @param Request $request
     * @return mixed
     */
    public function refresh(Request $request)
    {
        $account_id = $request->get('account_id');
        $query = $request->get('query');
        $account = Account::find($account_id);
        if (!$account) {
            $this->showMessage('未知账号！', true);
            return redirect()->back();
        }
        Cache::forget("d:item:{$account_id}:{$query}");
        Cache::forget("d:list:{$account_id}:{$query}");
        return $this->success();
    }

    /**
     * 删除项目
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function delete(Request $request)
    {
        $request->validate([
            'file_id' => 'required',
            'account_id' => 'required',
        ]);
        $file_id = $request->get('file_id');
        $account_id = $request->get('account_id');
        $account = Account::find($account_id);
        if (!$account) {
            return $this->fail('未知账号');
        }
        $service = $account->getOneDriveService();
        $resp = $service->delete($file_id);

        $query = $request->get('query');
        Cache::forget("d:item:{$account_id}:{$query}");
        Cache::forget("d:list:{$account_id}:{$query}");
        return $this->success($resp);
    }

    /**
     * 创建文件夹
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function mkdir(Request $request)
    {
        $request->validate([
            'account_id' => 'required',
            'parent_id' => 'required',
            'filename' => 'required'
        ]);
        $account_id = $request->get('account_id');
        $id = $request->get('parent_id');
        $fileName = $request->get('filename');
        $account = Account::find($account_id);
        if (!$account) {
            return $this->fail('未知账号');
        }
        $service = $account->getOneDriveService();
        $resp = $service->mkdir($fileName, $id);
        $query = $request->get('query');
        Cache::forget("d:item:{$account_id}:{$query}");
        Cache::forget("d:list:{$account_id}:{$query}");
        return $this->success($resp);
    }

    /**
     * 创建上传Session
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function createUploadSession(Request $request)
    {
        $fileName = $request->get('filename');
        $path = $request->get('path');
        $account_id = $request->get('account_id');
        $account = Account::find($account_id);
        if (!$account) {
            return $this->fail('未知账号');
        }
        $service = $account->getOneDriveService();
        $resp = $service->createUploadSession($path, $fileName);
        if (array_key_exists('code', $resp)) {
            $msg = array_get($resp, 'message', '404NotFound');
            $msg = GraphErrorEnum::get($resp['code']) ?? $msg;
            return $this->fail($msg, []);
        }
        $uploadUrl = array_get($resp, 'uploadUrl', '');
        $expired = array_get($resp, 'expirationDateTime', '');
        return $this->success([
            'uploadUrl' => $uploadUrl,
            'expired_at' => Carbon::parse($expired, 'Asia/Shanghai')->toIso8601String(),
        ]);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function createOrUpdateReadme(Request $request)
    {
        $redirect = $request->get('redirect');
        $file_id = $request->get('file_id');
        $parent_id = $request->get('parent_id');
        $account_id = $request->get('account_id');
        $content = $request->get('content');
        $account = Account::find($account_id);
        if (!$account) {
            $this->showMessage('未知账号！', true);
            return redirect()->back();
        }
        $service = $account->getOneDriveService();
        if ($request->method() === 'GET') {
            $content = '';
            if ($file_id) {
                $readme = Cache::remember("d:item:{$account_id}:$file_id", setting('cache_expires'), function () use ($service, $file_id) {
                    return $service->fetchItemById($file_id);
                });
                if (array_key_exists('code', $readme)) {
                    /*$msg = array_get($readme, 'message', '404NotFound');
                    $msg = GraphErrorEnum::get($readme['code']) ?? $msg;*/
                    Cache::forget("d:item:{$account_id}:$file_id");
                    $content = '';
                } else {
                    $url = $readme['@microsoft.graph.downloadUrl'];
                    $content = Cache::remember("d:content:{$account_id}:{$file_id}", setting('cache_expires'), function () use ($url) {
                        return Tool::fetchContent($url);
                    });
                }
            }
            return view('admin.file-edit', compact('content', 'file_id', 'parent_id', 'account_id', 'redirect'));
        }
        if ($file_id) {
            Cache::forget("d:content:{$account_id}:{$file_id}");
            $resp = $service->uploadById($file_id, $content);
        } else {
            $resp = $service->uploadByParentId($parent_id, 'README.md', $content);
        }
        if (array_key_exists('code', $resp)) {
            $msg = array_get($resp, 'message', '404NotFound');
            $msg = GraphErrorEnum::get($resp['code']) ?? $msg;
            $this->showMessage($msg, true);
            return redirect()->back();
        }

        $this->showMessage('提交成功！');
        return redirect()->away($redirect);

    }

    /**
     * 搜素
     * @param mixed|LazyCollection|Collection $list
     * @param string $keywords
     * @return mixed
     */
    private function search($list = [], $keywords = '')
    {
        return $list->filter(function ($item) use ($keywords) {
            $name = trim(array_get($item, 'name', ''));
            return str_contains($name, $keywords);
        });
    }

    /**
     * 格式化
     * @param mixed|LazyCollection|Collection data
     * @param bool $isFile
     * @return mixed|LazyCollection|Collection
     */
    private function formatItem($data = [], $isFile = false)
    {
        if ($isFile) {
            $data['ext'] = strtolower(
                pathinfo(
                    $data['name'],
                    PATHINFO_EXTENSION
                )
            );
            return $data;
        }
        return $data->map(function ($item) {
            if (array_has($item, 'file')) {
                $item['ext'] = strtolower(
                    pathinfo(
                        $item['name'],
                        PATHINFO_EXTENSION
                    )
                );
            } else {
                $item['ext'] = 'folder';
            }
            return $item;
        });
    }

    /**
     * 排序(支持 name\size\lastModifiedDateTime)
     * @param mixed|LazyCollection|Collection $list
     * @param string $field
     * @param bool $descending
     * @return array
     */
    private function sort($list = [], $field = 'name', $descending = false)
    {
        // 筛选文件夹/文件夹
        $folders = $list->filter(function ($item) {
            return array_has($item, 'folder');
        });
        $files = $list->filter(function ($item) {
            return !array_has($item, 'folder');
        });
        // 执行文件夹/文件夹 排序
        if (!$descending) {
            $folders = $folders->sortBy($field, $field === 'name' ? SORT_NATURAL : SORT_REGULAR);
            $files = $files->sortBy($field, $field === 'name' ? SORT_NATURAL : SORT_REGULAR);
        } else {
            $folders = $folders->sortByDesc($field, $field === 'name' ? SORT_NATURAL : SORT_REGULAR);
            $files = $files->sortByDesc($field, $field === 'name' ? SORT_NATURAL : SORT_REGULAR);
        }
        return $folders->merge($files)->all();
    }
}
