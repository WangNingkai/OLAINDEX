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
use App\Models\Account;
use App\Service\OneDrive;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Cache;

class HomeController extends BaseController
{
    public function __invoke(Request $request)
    {
        // 账号处理
        /* @var $accounts Collection */
        $accounts = \Cache::remember('ac:list', 600, static function () {
            return Account::query()
                ->select(['id', 'remark'])
                ->where('status', 1)->get();
        });
        if (blank($accounts)) {
            abort(404, '请先登录绑定账号！');
        }
        $account_id = 0;
        $hash = '';
        if ($accounts) {
            $account_id = setting('primary_account', 0);
            if (!$account_id) {
                $account_id = array_get(array_first((array)$accounts), 'id');
            }
            $account = $accounts->where('id', $account_id)->first();
            $hash = array_get($account, 'hash_id');
        }
        if (!$account_id) {
            abort(404, '账号不存在');
        }
        // 资源处理
        $root = array_get(setting($hash), 'root', '/');
        $root = trim($root, '/');
        $query = '/';
        $path = explode('/', $query);
        $path = array_where($path, static function ($value) {
            return !blank($value);
        });
        $query = trans_absolute_path(trim("{$root}/$query", '/'));
        $service = (new OneDrive($account_id));
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
        // 读取预设资源
        $doc = $this->filterDoc($list);
        // 资源过滤
        $list = $this->filter($list);
        // 格式化处理
        $list = $this->formatItem($list);
        //排序
        $list = $this->sort($list);
        // 分页
        $list = $this->paginate($list, 10, false);

        return view(config('olaindex.theme') . 'one', compact('accounts', 'hash', 'path', 'item', 'list', 'doc'));
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
        // 过滤预留文件
        $list = array_where($list, static function ($value) {
            return !in_array($value['name'], ['README.md', 'HEAD.md', '.password', '.deny'], false);
        });
        // todo:过滤隐藏文件
        return $list;
    }

    /**
     * 排序
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
     * 获取说明文件
     * @param array $list
     * @return array
     */
    private function filterDoc($list = [])
    {
        $readme = array_where($list, static function ($value) {
            return $value['name'] === 'README.md';
        });
        $head = array_where($list, static function ($value) {
            return $value['name'] === 'HEAD.md';
        });

        if (!empty($readme)) {
            $readme = array_first($readme);
            try {
                $readme = Cache::remember('d:content:' . $readme['id'], setting('cache_expires'), static function () use ($readme) {
                    return Tool::fetchContent($readme['@microsoft.graph.downloadUrl']);
                });
            } catch (\Exception $e) {
                $this->showMessage($e->getMessage(), true);
                Cache::forget('d:content:' . $readme['id']);
                $readme = '';
            }
        } else {
            $readme = '';
        }
        if (!empty($head)) {
            $head = array_first($head);
            try {
                $head = Cache::remember('d:content:' . $head['id'], setting('cache_expires'), static function () use ($head) {
                    return Tool::fetchContent($head['@microsoft.graph.downloadUrl']);
                });
            } catch (\Exception $e) {
                $this->showMessage($e->getMessage(), true);
                Cache::forget('d:content:' . $head['id']);
                $head = '';
            }

        } else {
            $head = '';
        }


        return compact('head', 'readme');
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
