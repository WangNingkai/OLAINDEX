<?php
/**
 * This file is part of the wangningkai/OLAINDEX.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Http\Controllers;


use App\Helpers\HashidsHelper;
use App\Helpers\Tool;
use Illuminate\Http\Request;
use App\Models\Account;
use Cache;
use OneDrive;
use Auth;

class ManageController extends BaseController
{
    public function query(Request $request, $hash, $query = '')
    {
        // 账号处理
        $accounts = Cache::remember('ac:list', 600, static function () {
            return Account::query()
                ->select(['id', 'remark'])
                ->where('status', 1)->get();
        });
        if (blank($accounts)) {
            Cache::forget('ac:list');
            abort(404, '请先绑定账号！');
        }
        $account_id = HashidsHelper::decode($hash);
        if (!$account_id) {
            abort(404, '账号不存在');
        }
        // 资源处理
        $config = setting($hash);
        $root = array_get($config, 'root', '/');
        $root = trim($root, '/');
        $query = trim($query, '/');
        $path = explode('/', $query);
        $path = array_where($path, static function ($value) {
            return !blank($value);
        });
        $query = trans_absolute_path(trim("{$root}/$query", '/'));
        $service = OneDrive::account($account_id);
        // 缓存处理
        $item = Cache::remember("d:item:{$account_id}:{$query}", setting('cache_expires'), static function () use ($service, $query) {
            return $service->fetchItem($query);
        });
        if (array_key_exists('code', $item)) {
            $this->showMessage(array_get($item, 'message', '404NotFound'), true);
            Cache::forget("d:item:{$account_id}:{$query}");
            return redirect()->route('message');
        }

        $list = Cache::remember("d:list:{$account_id}:{$query}", setting('cache_expires'), static function () use ($service, $query) {
            return $service->fetchList($query);
        });
        if (array_key_exists('code', $list)) {
            $this->showMessage(array_get($list, 'message', '404NotFound'), true);
            Cache::forget("d:list:{$account_id}:{$query}");
            return redirect()->route('message');
        }
        // 资源过滤
        $list = $this->filter($list);
        // 格式化处理
        $list = $this->formatItem($list);

        $doc = [
            'readme' => [],
            'head' => []
        ];
        $doc['readme'] = array_first(array_where($list, static function ($value) {
            return $value['name'] === 'README.md';
        }));
        $doc['head'] = array_first(array_where($list, static function ($value) {
            return $value['name'] === 'HEAD.md';
        }));
        // 排序
        $sortBy = $request->get('sortBy', 'name');
        $descending = false;
        if (str_contains($sortBy, '-')) {
            $descending = true;
            $sortBy = str_after($sortBy, '-');
        }
        $list = $this->sort($list, $sortBy, $descending);
        // 分页
        $perPage = array_get($config, 'list_limit', 10);

        $list = $this->paginate($list, $perPage, false);

        return view(config('olaindex.theme') . 'admin.file-manage', compact('accounts', 'hash', 'path', 'item', 'list', 'doc'));
    }

    public function edit(Request $request, $hash, $query = '')
    {
        // 账号处理
        $accounts = Cache::remember('ac:list', 600, static function () {
            return Account::query()
                ->select(['id', 'remark'])
                ->where('status', 1)->get();
        });
        if (blank($accounts)) {
            Cache::forget('ac:list');
            abort(404, '请先绑定账号！');
        }
        $account_id = HashidsHelper::decode($hash);
        if (!$account_id) {
            abort(404, '账号不存在');
        }
        // 资源处理
        $cacheKey1 = "d:item:{$account_id}:{$query}";
        $cacheKey2 = "d:content:{$account_id}:{$query}";
        $config = setting($hash);
        $root = array_get($config, 'root', '/');
        $root = trim($root, '/');
        $service = OneDrive::account($account_id);
        if ($request->isMethod('POST')) {
            $resp = $service->uploadById($query, $request->get('content'));
            if (array_key_exists('code', $resp)) {
                $this->showMessage(array_get($resp, 'message', '404NotFound'), true);
                return redirect()->route('message');
            }
            Cache::forget($cacheKey1);
            Cache::forget($cacheKey2);
            $this->showMessage('提交成功');
            return redirect()->route('home');
        }
        // 缓存处理
        $item = Cache::remember($cacheKey1, setting('cache_expires'), static function () use ($service, $query) {
            return $service->fetchItemById($query);
        });
        if (array_key_exists('code', $item)) {
            $this->showMessage(array_get($item, 'message', '404NotFound'), true);
            Cache::forget("d:item:{$account_id}:{$query}");
            return redirect()->route('message');
        }
        $parentPath = array_get($item, 'parentReference.path', '');
        $parentPath = rawurldecode(str_after($parentPath, '/drive/root:'));
        $parentPath = str_after($parentPath, $root);
        $path = explode('/', $parentPath);
        if ($root !== $item['name']) {
            $path[] = $item['name'];
        }
        if ($parentPath === '' && $item['name'] === 'root') {
            $path = [];
        }
        $path = array_where($path, static function ($value) {
            return !blank($value);
        });
        $file = $this->formatItem($item, true);
        $download = $file['@microsoft.graph.downloadUrl'];
        try {
            $content = Cache::remember("d:content:{$account_id}:{$file['id']}", setting('cache_expires'), static function () use ($download) {
                return Tool::fetchContent($download);
            });
        } catch (\Exception $e) {
            $this->showMessage($e->getMessage(), true);
            Cache::forget("d:content:{$account_id}:{$file['id']}");
            $content = '';
        }

        $file['content'] = $content;
        return view(config('olaindex.theme') . 'editor', compact('accounts', 'hash', 'path', 'file'));
    }

    public function create(Request $request, $hash, $query = '')
    {
        // 账号处理
        $accounts = Cache::remember('ac:list', 600, static function () {
            return Account::query()
                ->select(['id', 'remark'])
                ->where('status', 1)->get();
        });
        if (blank($accounts)) {
            Cache::forget('ac:list');
            abort(404, '请先绑定账号！');
        }
        $account_id = HashidsHelper::decode($hash);
        if (!$account_id) {
            abort(404, '账号不存在');
        }
        if ($request->isMethod('GET')) {
            $parentId = $query;
            $fileName = $request->get('fileName');
            return view(config('olaindex.theme') . 'create', compact('parentId', 'fileName'));
        }
        $parentId = $request->get('parentId');
        $fileName = $request->get('fileName');
        $content = $request->get('content');
        $service = OneDrive::account($account_id);
        $resp = $service->uploadByParentId($parentId, $fileName, $content);
        if (array_key_exists('code', $resp)) {
            $this->showMessage(array_get($resp, 'message', '404NotFound'), true);
            return redirect()->route('message');
        }
        return redirect()->route('home');
    }

    /**
     * 过滤
     * @param array $list
     * @return array
     */
    private function filter($list = [])
    {
        // 过滤微软内置无法读取的文件
        $list = array_where($list, static function ($value) {
            return !array_has($value, 'package.type');
        });
        return $list;
    }

    /**
     * 排序(支持 name\size\lastModifiedDateTime)
     * @param array $list
     * @param string $field
     * @param bool $descending
     * @return array
     */
    private function sort($list = [], $field = 'name', $descending = false)
    {
        $collect = collect($list)->lazy();
        // 筛选文件夹/文件夹
        $folders = $collect->filter(static function ($value) {
            return array_has($value, 'folder');
        });
        $files = $collect->filter(static function ($value) {
            return !array_has($value, 'folder');
        });
        // 执行文件夹/文件夹 排序
        if (!$descending) {
            $folders = $folders->sortBy($field, $field === 'name' ? SORT_NATURAL : SORT_REGULAR)->all();
            $files = $files->sortBy($field, $field === 'name' ? SORT_NATURAL : SORT_REGULAR)->all();
        } else {
            $folders = $folders->sortByDesc($field, $field === 'name' ? SORT_NATURAL : SORT_REGULAR)->all();
            $files = $files->sortByDesc($field, $field === 'name' ? SORT_NATURAL : SORT_REGULAR)->all();
        }
        return collect($folders)->merge($files)->all();
    }

    /**
     * 格式化
     * @param array $data
     * @param bool $isFile
     * @return array
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
        $items = [];
        foreach ($data as $item) {
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
            $items[] = $item;
        }
        return $items;
    }


}
