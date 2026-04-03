<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Http\Controllers;

use App\Http\Requests\DecryptRequest;
use App\Http\Requests\PreloadRequest;
use App\Http\Requests\QueryRequest;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Traits\ResolvesAccount;
use App\Models\Account;
use App\Service\DriveService;
use App\Service\GraphErrorEnum;
use App\Helpers\Tool;
use Illuminate\Http\Request;
use Cache;
use Cookie;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class DriveController extends BaseController
{
    use ApiResponseTrait;
    use ResolvesAccount;

    /**
     * 资源处理
     *
     * @param QueryRequest $request
     * @param string $hash
     * @param string $query
     * @return mixed
     * @throws \Exception
     */
    public function query(QueryRequest $request, string $hash = '', string $query = '')
    {
        // 解析账号
        $resolved = $this->resolveAccountWithHash($request, $hash);
        $account = $resolved['account'];
        $hash = $resolved['hash'];

        // 创建服务
        $service = new DriveService($account);
        $config = $account->config;

        // 解析路径
        $pathInfo = $service->parseQueryPath($query);
        $absoluteQuery = $pathInfo['query'];
        $path = $pathInfo['path'];
        $redirectQuery = $pathInfo['raw_query'];

        // 检查隐藏路径
        if ($service->isHiddenPath($redirectQuery)) {
            abort(404, '资源不存在！');
        }

        // 获取资源项
        $item = $service->fetchItem($absoluteQuery);

        if (isset($item['code'])) {
            $msg = $item['message'] ?? '404NotFound';
            $msg = GraphErrorEnum::get($item['code']) ?? $msg;
            abort(500, $msg);
        }

        // 检查是否为文件
        $isFile = isset($item['file']);

        // 检查加密路径
        $encryptCheck = $service->checkEncryptedPath($redirectQuery);
        $needPass = false;

        if ($encryptCheck['need_password']) {
            if (!$service->verifyEncryptedPassword($encryptCheck['encrypt_path'], $encryptCheck['password'])) {
                $needPass = true;
                $accounts = Account::fetchlist();
                return view(setting('main_theme', 'default') . '.password', compact('hash', 'item', 'redirectQuery', 'needPass'));
            }
        }

        // 处理文件
        if ($isFile) {
            return $this->handleFile($service, $item, $hash, $path, $needPass, $request);
        }

        // 处理目录
        return $this->handleDirectory($service, $account, $hash, $path, $absoluteQuery, $item, $needPass, $config, $request);
    }

    /**
     * 处理文件预览
     *
     * @param DriveService $service
     * @param array $item
     * @param string $hash
     * @param array $path
     * @param bool $needPass
     * @param Request $request
     * @return mixed
     */
    private function handleFile(DriveService $service, array $item, string $hash, array $path, bool $needPass, Request $request)
    {
        // 过滤非法预览
        $item = $service->filterPreviewItem($item);
        $item = $service->formatItems($item, true);

        $download = $item['@microsoft.graph.downloadUrl'];

        // 处理下载请求
        if ($request->get('download')) {
            $this->checkDownloadRate($request, $hash, $item['id']);
            return redirect()->away($download);
        }

        // 确定文件类型
        $fileType = $service->determineFileType($item['ext']);

        // 处理预览信息
        $processed = $service->processFilePreview($item, $fileType);
        $file = $processed['file'];
        $show = $processed['show'];

        $accounts = Account::fetchlist();
        return view(setting('main_theme', 'default') . '.preview', compact('accounts', 'hash', 'path', 'show', 'file', 'needPass'));
    }

    /**
     * 处理目录列表
     *
     * @param DriveService $service
     * @param Account $account
     * @param string $hash
     * @param array $path
     * @param string $query
     * @param array $item
     * @param bool $needPass
     * @param array $config
     * @param Request $request
     * @return mixed
     */
    private function handleDirectory(DriveService $service, Account $account, string $hash, array $path, string $query, array $item, bool $needPass, array $config, Request $request)
    {
        // 获取列表
        $list = $service->fetchList($query);

        if (isset($list['code'])) {
            $msg = $list['message'] ?? '404NotFound';
            $msg = GraphErrorEnum::get($list['code']) ?? $msg;
            abort(500, $msg);
        }

        $list = collect($list)->lazy();

        // 获取说明文件
        $doc = $service->fetchDocContent($list);

        // 过滤系统文件
        $list = $service->filterSystemItems($list);

        // 过滤隐藏文件
        $hidePath = $config['hide_path'] ?? '';
        $list = $service->filterHiddenItems($list, $hidePath, $path);

        // 格式化
        $list = $service->formatItems($list);

        // 搜索处理
        $keywords = $request->get('keywords');
        if ($keywords) {
            $list = $service->searchItems($list, $keywords);
        }

        // 排序
        $sortParams = $request->getSortParams();
        $list = $service->sortItems($list, $sortParams['column'], $sortParams['direction'] === 'desc');

        // 分页
        $perPage = $config['list_limit'] ?? 10;
        $list = $service->paginateItems($list, $perPage);

        $accounts = Account::fetchlist();
        return view(setting('main_theme', 'default') . '.one', compact('accounts', 'hash', 'path', 'item', 'list', 'doc', 'keywords', 'needPass'));
    }

    /**
     * 缓存预加载
     *
     * @param PreloadRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function preload(PreloadRequest $request)
    {
        $account = $this->resolveAccountFromRequest($request);
        $service = new DriveService($account);

        $pathInfo = $service->parseQueryPath($request->get('query'));
        $query = $pathInfo['query'];
        $path = $pathInfo['path'];
        $root = $pathInfo['root'];

        // 获取资源项
        $item = $service->fetchItem($query);

        if (isset($item['code'])) {
            $msg = $item['message'] ?? '404NotFound';
            $msg = GraphErrorEnum::get($item['code']) ?? $msg;
            return $this->fail($msg);
        }

        // 是文件直接返回
        if (isset($item['file'])) {
            return $this->success();
        }

        // 获取列表
        $list = $service->fetchList($query);

        if (isset($item['code'])) {
            return $this->fail('获取列表失败');
        }

        // 预缓存子项
        foreach ($list as $listItem) {
            $childQuery = implode('/', Arr::add($path, key(array_slice($path, -1, 1, true)) + 1, $listItem['name']));
            $childQuery = trim("{$root}/{$childQuery}", '/');
            $childQuery = trans_absolute_path($childQuery);
            $childQuery = strtolower($childQuery);

            Cache::add($service->buildCacheKey('item', $childQuery), $listItem, setting('cache_expires'));
            Cache::add($service->buildCacheKey('item', $listItem['id']), $listItem, setting('cache_expires'));

            // 如果是目录，预缓存子列表
            if (!isset($listItem['file'])) {
                $service->fetchList($childQuery);
            }
        }

        return $this->success();
    }

    /**
     * 文件下载
     *
     * @param string $hash
     * @param string $itemId
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function download(string $hash, string $itemId)
    {
        $account = $this->resolveAccount($hash);
        $service = new DriveService($account);

        $item = $service->fetchItem($itemId);

        if (isset($item['code'])) {
            $msg = $item['message'] ?? '404NotFound';
            $msg = GraphErrorEnum::get($item['code']) ?? $msg;
            abort(500, $msg);
        }

        if (!isset($item['file'])) {
            abort(404);
        }

        return redirect()->away($item['@microsoft.graph.downloadUrl']);
    }

    /**
     * 解密资源
     *
     * @param DecryptRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function decrypt(DecryptRequest $request)
    {
        $inputPassword = $request->get('password');
        $redirect = $request->get('redirect', '');
        $hash = $request->get('hash');
        $query = $request->get('query', '');

        $account = $this->resolveAccount($hash);
        $service = new DriveService($account);

        $redirectPath = $request->getRedirectPath();

        // 检查加密路径
        $encryptCheck = $service->checkEncryptedPath($redirect);

        if ($encryptCheck['need_password'] && strcmp($encryptCheck['password'], $inputPassword) === 0) {
            $data = [
                'password' => encrypt($inputPassword),
                'hash' => $hash,
                'query' => $query,
            ];

            return redirect()->route('drive.query', ['hash' => $hash, 'query' => url_encode($redirectPath)])
                ->withCookie("e:{$hash}:{$encryptCheck['encrypt_path']}", json_encode($data), 600);
        }

        return redirect()->back();
    }

    /**
     * 下载速率限制
     *
     * @param Request $request
     * @param string $hash
     * @param string $itemId
     */
    private function checkDownloadRate(Request $request, string $hash, string $itemId): void
    {
        $downloadLimit = setting('download_limit', 0);
        if ($downloadLimit <= 0) {
            return;
        }

        $this->checkUserDownloadRate($request, $hash, $itemId);

        $key = 's:rate:' . sha1($hash . '|' . $itemId);
        $decaySeconds = 60;
        $maxAttempts = (int) $downloadLimit;

        $attempts = Cache::get($key, 0);
        if ($attempts >= $maxAttempts) {
            if (Cache::has($key . ':timer')) {
                abort(429, '资源过于火爆，请稍后再试！');
            }
            Cache::forget($key);
        }

        Cache::add($key . ':timer', Carbon::now()->addRealSeconds($decaySeconds)->getTimestamp(), $decaySeconds);
        Cache::add($key, 0, $decaySeconds);
        $hits = (int) Cache::increment($key);

        if ($hits === 1) {
            Cache::put($key, 1, $decaySeconds);
        }
    }

    /**
     * 用户下载速率限制
     *
     * @param Request $request
     * @param string $hash
     * @param string $itemId
     */
    private function checkUserDownloadRate(Request $request, string $hash, string $itemId): void
    {
        $userLimit = setting('user_limit', 0);
        if ($userLimit <= 0) {
            return;
        }

        $key = 's:rate:' . sha1($hash . '|' . $itemId . '|' . $request->ip());
        $decaySeconds = 60;
        $maxAttempts = (int) $userLimit;

        $attempts = Cache::get($key, 0);
        if ($attempts >= $maxAttempts) {
            if (Cache::has($key . ':timer')) {
                abort(429);
            }
            Cache::forget($key);
        }

        Cache::add($key . ':timer', Carbon::now()->addRealSeconds($decaySeconds)->getTimestamp(), $decaySeconds);
        Cache::add($key, 0, $decaySeconds);
        $hits = (int) Cache::increment($key);

        if ($hits === 1) {
            Cache::put($key, 1, $decaySeconds);
        }
    }
}