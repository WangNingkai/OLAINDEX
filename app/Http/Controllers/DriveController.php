<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponseTrait;
use App\Models\Account;
use App\Service\GraphErrorEnum;
use Illuminate\Http\Request;
use App\Helpers\HashidsHelper;
use App\Helpers\Tool;
use Cache;
use Cookie;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

class DriveController extends BaseController
{
    use ApiResponseTrait;

    /**
     * 资源处理
     * @param Request $request
     * @param $hash
     * @param string $query
     * @return mixed
     * @throws \Exception
     */
    public function query(Request $request, $hash = '', $query = '')
    {
        if (setting('single_account_mode', 1)) {
            $query = $hash;
            $hash = $request->get('hash', '');
            if ($hash) {
                $account_id = HashidsHelper::decode($hash);
            } else {
                $account_id = setting('primary_account', 0);
                $hash = HashidsHelper::encode($account_id);
            }
        } else {
            if (!$hash) {
                $hash = $request->get('hash', '');
                if ($hash) {
                    $account_id = HashidsHelper::decode($hash);
                } else {
                    $account_id = setting('primary_account', 0);
                    $hash = HashidsHelper::encode($account_id);
                }

            } else {
                $account_id = HashidsHelper::decode($hash);
                if (null === $account_id) {
                    $query = $hash;
                    $account_id = setting('primary_account', 0);
                    $hash = HashidsHelper::encode($account_id);
                }
            }
        }

        if (!$account_id) {
            abort(404, '尚未设置账号！');
        }
        $account = Account::find($account_id);
        if (!$account) {
            abort(404, '账号不存在！');
        }
        $view = '';
        $accounts = Account::fetchlist();
        // 资源处理
        $config = $account->config;
        $root = strtolower($account->config['root'] ?? '/');
        $rawQuery = rawurldecode($query);
        $rawQuery = trim($rawQuery, '/');
        $query = strtolower($rawQuery);
        $redirectQuery = $query;
        $path = explode('/', $rawQuery);
        $path = array_filter($path);
        $query = trim("{$root}/$query", '/');
        $query = trans_absolute_path($query);

        // 处理隐藏
        $hide_path = array_get($config, 'hide_path');
        if (!blank($hide_path)) {
            $is_hide = false;
            $hide_path_arr = explode('|', $hide_path);
            $hide_path_arr = array_filter($hide_path_arr);
            $redirect = rawurldecode($redirectQuery);
            $redirect = trans_absolute_path($redirect);
            $redirect = trim($redirect, '/');
            foreach ($hide_path_arr as $hide_item) {
                $hide_item = strtolower($hide_item);
                if ($redirect === $hide_item || starts_with($redirect, $hide_item)) {
                    $is_hide = true;
                }
            }
            if ($is_hide) {
                abort(404, '资源不存在！');
            }
        }
        $service = $account->getOneDriveService();

        $item = Cache::remember("d:item:{$account_id}:{$query}", setting('cache_expires'), function () use ($service, $query) {
            return $service->fetchItem($query);
        });

        if (array_key_exists('code', $item)) {
            $msg = array_get($item, 'message', '404NotFound');
            $msg = GraphErrorEnum::get($item['code']) ?? $msg;
            Cache::forget("d:item:{$account_id}:{$query}");
            abort(500, $msg);
        }
        Cache::add("d:item:{$account_id}:{$item['id']}", $item, setting('cache_expires'));

        // 处理文件
        $isFile = false;
        if (array_key_exists('file', $item)) {
            $isFile = true;
        }

        // 处理加密
        $need_pass = false;
        $encrypt_path = array_get($config, 'encrypt_path');
        if (!blank($encrypt_path)) {
            $encrypt_path_arr = explode('|', $encrypt_path);
            $encrypt_path_arr = array_filter($encrypt_path_arr);
            $redirect = trans_absolute_path(rawurldecode($redirectQuery));
            $redirect = trim($redirect, '/');
            $is_encrypt = false;
            $_encrypt_arr = [];
            foreach ($encrypt_path_arr as $encrypt_item) {
                [$_path, $password] = explode(':', $encrypt_item);
                $_path = strtolower($_path);
                $_path = trans_absolute_path($_path);
                $_path = trim($_path, '/');
                if ($redirect === $_path || starts_with($redirect, $_path)) {
                    $is_encrypt = true;
                    $_encrypt_arr[$_path] = $password;
                }
            }
            if ($is_encrypt) {
                $need_pass = false;
                foreach ($_encrypt_arr as $_encrypt_path => $_password) {
                    if (Cookie::has("e:{$hash}:{$_encrypt_path}")) {
                        $data = json_decode(Cookie::get("e:{$hash}:{$_encrypt_path}"), true);
                        if (strcmp($_password, decrypt($data['password'])) !== 0) {
                            Cookie::forget("e:{$hash}:{$_encrypt_path}");
                            $this->showMessage('密码已过期', true);
                            $need_pass = true;
                            break;
                        }
                    } else {
                        $need_pass = true;
                        break;
                    }
                }
                if ($need_pass) {
                    return view(setting('main_theme', 'default') . '.password', compact('hash', 'item', 'redirect', 'need_pass'));
                }
            }
        }

        if ($isFile) {
            $item = $this->filterItem($item, $hash);
            $file = $this->formatItem($item, true);
            $download = $file['@microsoft.graph.downloadUrl'];
            if ($request->get('download')) {
                $this->checkDownloadRate($request, $hash, $query);
                return redirect()->away($download);
            }
            $file['download'] = $download;
            $showList = [
                'stream' => explode(' ', setting('show_stream')),
                'image' => explode(' ', setting('show_image')),
                'video' => explode(' ', setting('show_video')),
                'dash' => explode(' ', setting('show_dash')),
                'audio' => explode(' ', setting('show_audio')),
                'code' => explode(' ', setting('show_code')),
                'doc' => explode(' ', setting('show_doc')),
            ];
            $show = 'other';
            foreach ($showList as $key => $suffix) {
                if (in_array($file['ext'] ?? '', $suffix, false)) {
                    $show = $key;
                    // 处理文本
                    // 文件>5m 无法预览
                    if (in_array($key, ['stream', 'code']) && $file['size'] < 5 * 1024 * 1024) {
                        try {
                            $content = Cache::remember("d:content:{$account_id}:{$file['id']}", setting('cache_expires'), function () use ($download) {
                                return Tool::fetchContent($download);
                            });
                        } catch (\Exception $e) {
                            $this->showMessage($e->getMessage(), true);
                            Cache::forget("d:content:{$account_id}:{$file['id']}");
                            $content = '';
                        }

                        $file['content'] = $content;
                        if ($key === 'stream') {
                            $show = 'code';
                        }
                    }
                    // 处理缩略图
                    if (in_array($key, ['image', 'dash', 'video'])) {
                        $thumb = array_get($file, 'thumbnails.0.large.url');
                        $file['thumb'] = $thumb;
                    }
                    // dash视频流
                    if ($key === 'dash') {
                        if (!strpos($download, 'sharepoint.com')) {
                            $show = 'other';
                        }
                        $replace = str_replace('thumbnail', 'videomanifest', $file['thumb']);
                        $dash = $replace . '&part=index&format=dash&useScf=True&pretranscode=0&transcodeahead=0';
                        $file['dash'] = $dash;
                    }
                    // 处理微软文档
                    if ($key === 'doc') {
                        $preview_url = 'https://view.officeapps.live.com/op/view.aspx?src='
                            . urlencode($download);
                        $file['preview'] = $preview_url;
                    }
                }
            }

            return view(setting('main_theme', 'default') . '.preview' . $view, compact('accounts', 'hash', 'path', 'show', 'file', 'need_pass'));
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
        // 处理列表
        $doc = $this->filterDoc($account_id, $list);
        // 资源过滤
        $list = $this->filter($list, $hash);
        // 过滤隐藏
        $list = $list->filter(function ($item) use ($hide_path, $path) {
            $query = implode('/', array_add($path, key(array_slice($path, -1, 1, true)) + 1, $item['name']));
            $query = strtolower($query);
            if (!blank($hide_path)) {
                $is_hide = false;
                $hide_path_arr = explode('|', $hide_path);
                $hide_path_arr = array_filter($hide_path_arr);
                $redirect = trans_absolute_path(rawurldecode($query));
                $redirect = trim($redirect, '/');
                foreach ($hide_path_arr as $hide_item) {
                    $hide_item = strtolower($hide_item);
                    if ($redirect === $hide_item || starts_with($redirect, $hide_item)) {
                        $is_hide = true;
                    }
                }
                return !$is_hide;
            }
            return true;
        });

        // 资源处理
        $list = $this->formatItem($list);
        //搜索处理
        $keywords = $request->get('keywords');
        if ($keywords) {
            $list = $this->search($list, $keywords);
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
        // 分页
        $perPage = array_get($config, 'list_limit', 10);

        $list = $this->paginate($list, $perPage, false);

        return view(setting('main_theme', 'default') . '.one' . $view, compact('accounts', 'hash', 'path', 'item', 'list', 'doc', 'keywords', 'need_pass'));
    }

    /**
     * 缓存预加载
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function preload(Request $request)
    {
        $_cache = [];
        $hash = $request->get('hash', '');
        $query = $request->get('query', '');
        if (!$hash) {
            $account_id = setting('primary_account', 0);
        } else {
            $account_id = HashidsHelper::decode($hash);
        }
        if (!$account_id) {
            return $this->fail('账号不存在！');
        }
        $account = Account::find($account_id);
        if (!$account) {
            return $this->fail('账号不存在！');
        }
        // 资源处理
        $root = strtolower($account->config['root'] ?? '/');
        $rawQuery = rawurldecode($query);
        $rawQuery = trim($rawQuery, '/');
        $query = strtolower($rawQuery);
        $path = explode('/', $rawQuery);
        $path = array_filter($path);
        $query = trans_absolute_path(trim("{$root}/$query", '/'));

        $service = $account->getOneDriveService();

        $item = Cache::remember("d:item:{$account_id}:{$query}", setting('cache_expires'), function () use ($service, $query) {
            return $service->fetchItem($query);
        });
        if (array_key_exists('code', $item)) {
            $msg = array_get($item, 'message', '404NotFound');
            $msg = GraphErrorEnum::get($item['code']) ?? $msg;
            Cache::forget("d:item:{$account_id}:{$query}");
            return $this->fail($msg);
        }
        Cache::add("d:item:{$account_id}:{$item['id']}", $item, setting('cache_expires'));
        $_cache[] = "d:item:{$account_id}:{$query}";
        if (array_key_exists('file', $item)) {
            return $this->success();
        }

        $list = Cache::remember("d:list:{$account_id}:{$query}", setting('cache_expires'), function () use ($service, $query) {
            return $service->fetchList($query);
        });
        if (array_key_exists('code', $list)) {
            $msg = array_get($list, 'message', '404NotFound');
            $msg = GraphErrorEnum::get($list['code']) ?? $msg;
            Cache::forget("d:list:{$account_id}:{$query}");
            return $this->fail($msg);
        }
        $_cache[] = "d:list:{$account_id}:{$query}";

        foreach ($list as $list_item) {
            $query = implode('/', array_add($path, key(array_slice($path, -1, 1, true)) + 1, $list_item['name']));
            $query = trim($query, '/');
            $query = trans_absolute_path(trim("{$root}/$query", '/'));
            $query = strtolower($query);
            Cache::add("d:item:{$account_id}:{$query}", $list_item, setting('cache_expires'));
            $_cache[] = "d:item:{$account_id}:{$query}";
            Cache::add("d:item:{$account_id}:{$list_item['id']}", $list_item, setting('cache_expires'));
            if (array_key_exists('file', $list_item)) {
                continue;
            }
            $child_list = Cache::remember("d:list:{$account_id}:{$query}", setting('cache_expires'), function () use ($service, $query) {
                return $service->fetchList($query);
            });
            $_cache[] = "d:list:{$account_id}:{$query}";
            if (array_key_exists('code', $child_list)) {
                Cache::forget("d:list:{$account_id}:{$query}");
                continue;
            }
        }
        Cache::add('tmp_cache', $_cache, 1800);
        return $this->success();
    }

    /**
     * 文件下载
     * @param $hash
     * @param $item_id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function download($hash, $item_id)
    {
        if (!$hash) {
            $account_id = setting('primary_account', 0);
        } else {
            $account_id = HashidsHelper::decode($hash);
        }
        if (!$account_id) {
            abort(404, '尚未设置账号！');
        }
        $account = Account::find($account_id);
        if (!$account) {
            abort(404, '账号不存在！');
        }
        $service = $account->getOneDriveService();
        // 缓存处理
        $item = Cache::remember("d:item:{$account_id}:{$item_id}", setting('cache_expires'), function () use ($service, $item_id) {
            return $service->fetchItemById($item_id);
        });
        if (array_key_exists('code', $item)) {
            $msg = array_get($item, 'message', '404NotFound');
            $msg = GraphErrorEnum::get($item['code']) ?? $msg;
            Cache::forget("d:item:{$account_id}:{$item_id}");
            abort(500, $msg);
        }
        // 处理文件
        $isFile = false;
        if (array_key_exists('file', $item)) {
            $isFile = true;
        }

        if (!$isFile) {
            abort(404);
        }
        return redirect()->away($item['@microsoft.graph.downloadUrl']);
    }

    /**
     * 解密资源
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function decrypt(Request $request)
    {
        $input_password = $request->get('password');
        $redirect = $request->get('redirect', '');
        $hash = $request->get('hash');
        $query = $request->get('query', '');
        $query = strtolower($query);
        $data = [
            'password' => encrypt($input_password),
            'hash' => $hash,
            'query' => $query,
        ];
        $data = json_encode($data);
        if (!$hash) {
            $account_id = setting('primary_account', 0);
            $hash = HashidsHelper::encode($account_id);
        } else {
            $account_id = HashidsHelper::decode($hash);
        }
        if (!$account_id) {
            abort(404, '尚未设置账号！');
        }
        $account = Account::find($account_id);
        if (!$account) {
            abort(404, '账号不存在！');
        }
        $config = $account->config;
        $encrypt_path = array_get($config, 'encrypt_path');
        $encrypt_path_arr = explode('|', $encrypt_path);
        $encrypt_path_arr = array_filter($encrypt_path_arr);
        $redirect = trans_absolute_path(rawurldecode($redirect));
        $redirect = trim($redirect, '/');
        $need_pass = false;
        $_password = '';
        $_encrypt_path = '';
        foreach ($encrypt_path_arr as $encrypt_item) {
            [$_path, $password] = explode(':', $encrypt_item);
            $_path = trans_absolute_path($_path);
            $_path = trim($_path, '/');
            $_path = strtolower($_path);
            if ($redirect === $_path || starts_with($redirect, $_path)) {
                $need_pass = true;
                $_password = $password;
                $_encrypt_path = $_path;
            }
        }
        if ($need_pass) {
            if (strcmp($_password, $input_password) === 0) {
                return redirect()->route('drive.query', ['hash' => $hash, 'query' => url_encode($redirect)])->withCookie("e:{$hash}:{$_encrypt_path}", $data, 600);
            }
        }
        return redirect()->back();
    }

    /**
     * 获取说明文件
     * @param $account_id
     * @param mixed|LazyCollection|Collection $list
     * @return array
     */
    private function filterDoc($account_id, $list = [])
    {
        $readme = $list->filter(function ($item) {
            return $item['name'] === 'README.md';
        });
        $head = $list->filter(function ($item) {
            return $item['name'] === 'HEAD.md';
        });

        if ($readme->isNotEmpty()) {
            $readme = $readme->first();
            try {
                $readme = Cache::remember("d:content:{$account_id}:{$readme['id']}", setting('cache_expires'), function () use ($readme) {
                    return Tool::fetchContent($readme['@microsoft.graph.downloadUrl']);
                });
            } catch (\Exception $e) {
                $this->showMessage($e->getMessage(), true);
                Cache::forget("d:content:{$account_id}:{$readme['id']}");
                $readme = '';
            }
        } else {
            $readme = '';
        }
        if ($head->isNotEmpty()) {
            $head = $head->first();
            try {
                $head = Cache::remember("d:content:{$account_id}:{$head['id']}", setting('cache_expires'), function () use ($head) {
                    return Tool::fetchContent($head['@microsoft.graph.downloadUrl']);
                });
            } catch (\Exception $e) {
                $this->showMessage($e->getMessage(), true);
                Cache::forget("d:content:{$account_id}:{$head['id']}");
                $head = '';
            }
        } else {
            $head = '';
        }


        return compact('head', 'readme');
    }

    /**
     * 过滤非法预览
     * @param array $item
     * @param string $hash
     * @return mixed
     */
    private function filterItem($item, $hash)
    {
        $illegalFile = ['README.md', 'HEAD.md', '.password', '.deny'];
        $pattern = '/^README\.md|HEAD\.md|\.password|\.deny/';
        if (in_array($item['name'], $illegalFile, false) || preg_match($pattern, $item['name'], $arr) > 0) {
            abort(403, '非法请求');
        }
        // 处理隐藏文件
        $store_hide_key = "h:{$hash}";
        $hidden_path = setting($store_hide_key, []);
        if (in_array($item['id'], $hidden_path, false)) {
            abort(404, '文件不存在');
        }

        return $item;
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
     * 过滤
     * @param mixed|LazyCollection|Collection $list
     * @param string $hash
     * @return mixed
     * @throws \Exception
     */
    private function filter($list = [], $hash = '')
    {
        // 过滤微软内置无法读取的文件 & 过滤预留文件
        $list = $list->filter(function ($item) {
            return !array_has($item, 'package.type');
        });
        $list = $list->filter(function ($item) {
            $name = strtoupper(trim(array_get($item, 'name', '')));
            return !in_array($name, ['README.MD', 'HEAD.MD', '.PASSWORD', '.DENY'], false);
        });
        // 过滤隐藏文件
        if (!$hash) {
            $account_id = setting('primary_account', 0);
        } else {
            $account_id = HashidsHelper::decode($hash);
        }
        if (!$account_id) {
            abort(404, '尚未设置账号！');
        }
        $account = Account::find($account_id);
        if (!$account) {
            abort(404, '账号不存在！');
        }

        return $list;
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

    /**
     * 下载速率限制
     * @param Request $request
     * @param $hash
     * @param $query
     */
    private function checkDownloadRate(Request $request, $hash, $query)
    {
        if (setting('download_limit', 0) <= 0) {
            return;
        }
        $this->checkUserDownloadRate($request, $hash, $query);
        $rateLimiter = [1, setting('download_limit')];//[单位时间内, 允许操作的次数]
        $key = 's:rate:' . sha1($hash . '|' . $query);
        $decaySeconds = (int)$rateLimiter[0] * 60;
        $maxAttempts = (int)$rateLimiter[1];

        $attempts = Cache::get($key, 0);
        if ($attempts >= $maxAttempts) {
            if (Cache::has($key . ':timer')) {
                abort(429, '资源过于火爆，请稍后再试！');
            }
            Cache::forget($key);
        }

        Cache::add(
            $key . ':timer', Carbon::now()->addRealSeconds($decaySeconds)->getTimestamp(), $decaySeconds
        );

        $added = Cache::add($key, 0, $decaySeconds);

        $hits = (int)Cache::increment($key);

        if (!$added && $hits === 1) {
            Cache::put($key, 1, $decaySeconds);
        }

    }

    /**
     * 用户下载速率限制
     * @param Request $request
     * @param $hash
     * @param $query
     */
    private function checkUserDownloadRate(Request $request, $hash, $query)
    {
        if (setting('user_limit', 0) <= 0) {
            return;
        }
        $rateLimiter = [1, setting('user_limit')];//[单位时间内, 允许操作的次数]
        $key = 's:rate:' . sha1($hash . '|' . $query . '|' . $request->ip());
        $decaySeconds = (int)$rateLimiter[0] * 60;
        $maxAttempts = (int)$rateLimiter[1];

        $attempts = Cache::get($key, 0);
        if ($attempts >= $maxAttempts) {
            if (Cache::has($key . ':timer')) {
                abort(429);
            }
            Cache::forget($key);
        }

        Cache::add(
            $key . ':timer', Carbon::now()->addRealSeconds($decaySeconds)->getTimestamp(), $decaySeconds
        );

        $added = Cache::add($key, 0, $decaySeconds);

        $hits = (int)Cache::increment($key);

        if (!$added && $hits === 1) {
            Cache::put($key, 1, $decaySeconds);
        }

    }

}
