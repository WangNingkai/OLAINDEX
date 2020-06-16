<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\HashidsHelper;
use App\Helpers\Tool;
use App\Models\Account;
use App\Service\OneDrive;
use Cache;

class DiskController extends BaseController
{
    public function __invoke(Request $request, $hash, $query = '/')
    {
        // 账号处理
        $accounts = Cache::remember('ac:list', 600, static function () {
            return Account::query()
                ->select(['id', 'remark'])
                ->where('status', 1)->get();
        });
        $account_id = HashidsHelper::decode($hash);
        if (!$account_id) {
            abort(404, '账号不存在');
        }
        // 资源处理
        $root = array_get(setting($hash), 'root', '/');
        $root = trim($root, '/');
        $query = trim($query, '/');
        $path = explode('/', $query);
        $path = array_where($path, static function ($value) {
            return !blank($value);
        });
        $query = "{$root}/$query";
        $service = (new OneDrive($account_id));

        // 缓存处理
        $item = Cache::remember('d:item:' . $query, setting('cache_expires'), static function () use ($service, $query) {
            return $service->fetchItem($query);
        });
        if (array_key_exists('code', $item)) {
            $this->showMessage(array_get($item, 'message', '404NotFound'), true);
            Cache::forget('d:item:' . $query);
            return redirect()->route('message');
        }
        // 处理文件
        $isFile = false;
        if (array_key_exists('file', $item)) {
            $isFile = true;
        }
        if ($isFile) {
            $item = $this->filterItem($item);
            $file = $this->formatItem($item, true);
            $download = $file['@microsoft.graph.downloadUrl'];
            if ($request->get('download')) {
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
            foreach ($showList as $key => $suffix) {
                if (in_array($file['ext'] ?? '', $suffix, false)) {
                    $show = $key;
                    // 处理文本
                    if (in_array($key, ['stream', 'code'])) {
                        // 文件>5m 无法预览
                        if ($file['size'] > 5 * 1024 * 1024) {
                            $this->showMessage('文件过大，请下载查看', false);

                            return redirect()->back();
                        }
                        try {
                            $content = Cache::remember('d:content:' . $file['id'], setting('cache_expires'), static function () use ($download) {
                                return Tool::fetchContent($download);
                            });
                        } catch (\Exception $e) {
                            $this->showMessage($e->getMessage(), true);
                            Cache::forget('d:content:' . $file['id']);
                            $content = '';
                        }

                        $file['content'] = $content;
                        if ($key === 'stream') {
                            $fileType = Tool::fetchFileType($file['ext']);
                            return response($content, 200, ['Content-type' => $fileType,]);
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
                            return redirect()->away($download);
                        }
                        $replace = str_replace('thumbnail', 'videomanifest', $file['thumb']);
                        $dash = $replace . '&part=index&format=dash&useScf=True&pretranscode=0&transcodeahead=0';
                        $file['dash'] = $dash;
                    }
                    // 处理微软文档
                    if ($key === 'doc') {
                        $url = 'https://view.officeapps.live.com/op/view.aspx?src='
                            . urlencode($download);

                        return redirect()->away($url);
                    }
                    return view(config('olaindex.theme') . 'preview', compact('accounts', 'hash', 'path', 'show', 'file'));
                }
            }
            return redirect()->away($download);
        }

        $list = Cache::remember('d:list:' . $query, setting('cache_expires'), static function () use ($service, $query) {
            return $service->fetchList($query);
        });
        if (array_key_exists('code', $list)) {
            $this->showMessage(array_get($list, 'message', '404NotFound'), true);
            Cache::forget('d:list:' . $query);
            return redirect()->route('message');
        }
        // 处理列表
        // 读取预设资源
        $doc = $this->filterDoc($list);
        // 资源过滤
        $list = $this->filter($list);
        // 格式化处理
        $list = $this->formatItem($list);
        // 排序
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
     * 过滤非法预览
     * @param array $item
     * @return mixed
     */
    private function filterItem($item)
    {
        $illegalFile = ['README.md', 'HEAD.md', '.password', '.deny'];
        $pattern = '/^README\.md|HEAD\.md|\.password|\.deny/';
        if (in_array($item['name'], $illegalFile, false) || preg_match($pattern, $item['name'], $arr) > 0) {
            abort(403, '非法请求');
        }
        // todo:处理隐藏文件
        return $item;
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
