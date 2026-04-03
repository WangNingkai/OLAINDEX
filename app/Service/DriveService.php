<?php

namespace App\Service;

use App\Helpers\HashidsHelper;
use App\Models\Account;
use App\Service\GraphErrorEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Cache;
use Cookie;
use Illuminate\Support\Str;

/**
 * DriveService - OneDrive 资源业务逻辑服务
 *
 * 负责资源路径解析、过滤、排序、分页和缓存策略
 */
class DriveService
{
    /**
     * @var Account
     */
    private $account;

    /**
     * @var int
     */
    private $accountId;

    /**
     * @var string
     */
    private $hash;

    /**
     * @var OneDrive|null
     */
    private $oneDrive = null;

    /**
     * DriveService constructor.
     *
     * @param Account $account
     */
    public function __construct(Account $account)
    {
        $this->account = $account;
        $this->accountId = $account->id;
        $this->hash = HashidsHelper::encode($account->id);
    }

    /**
     * 获取 OneDrive 服务实例（延迟初始化）
     *
     * @return OneDrive
     */
    private function getOneDrive(): OneDrive
    {
        if ($this->oneDrive === null) {
            $this->oneDrive = $this->account->getOneDriveService();
        }
        return $this->oneDrive;
    }

    /**
     * 获取账号 ID
     *
     * @return int
     */
    public function getAccountId(): int
    {
        return $this->accountId;
    }

    /**
     * 获取 hash
     *
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * 解析查询路径
     *
     * @param string $query
     * @return array{query: string, path: array, root: string}
     */
    public function parseQueryPath(string $query = ''): array
    {
        $config = $this->account->config;
        $root = strtolower($config['root'] ?? '/');

        $rawQuery = rawurldecode($query);
        $rawQuery = trim($rawQuery, '/');
        $normalizedQuery = strtolower($rawQuery);

        $path = explode('/', $rawQuery);
        $path = array_filter($path);

        $absoluteQuery = trim("{$root}/{$normalizedQuery}", '/');
        $absoluteQuery = trans_absolute_path($absoluteQuery);

        return [
            'query' => $absoluteQuery,
            'path' => array_values($path),
            'root' => $root,
            'raw_query' => $rawQuery,
        ];
    }

    /**
     * 检查路径是否被隐藏
     *
     * @param string $query
     * @return bool
     */
    public function isHiddenPath(string $query): bool
    {
        $config = $this->account->config;
        $hidePath = $config['hide_path'] ?? '';

        if (blank($hidePath)) {
            return false;
        }

        $hidePaths = array_filter(explode('|', $hidePath));
        $normalizedQuery = trim(trans_absolute_path(rawurldecode($query)), '/');

        foreach ($hidePaths as $hideItem) {
            $hideItem = strtolower(trim(trans_absolute_path($hideItem), '/'));
            if ($normalizedQuery === $hideItem || Str::startsWith($normalizedQuery, $hideItem . '/')) {
                return true;
            }
        }

        return false;
    }

    /**
     * 检查路径是否需要密码
     *
     * @param string $query
     * @return array{need_password: bool, password: string, encrypt_path: string}
     */
    public function checkEncryptedPath(string $query): array
    {
        $config = $this->account->config;
        $encryptPath = $config['encrypt_path'] ?? '';

        if (blank($encryptPath)) {
            return ['need_password' => false, 'password' => '', 'encrypt_path' => ''];
        }

        $encryptPaths = array_filter(explode('|', $encryptPath));
        $normalizedQuery = trim(trans_absolute_path(rawurldecode($query)), '/');

        foreach ($encryptPaths as $encryptItem) {
            $parts = explode(':', $encryptItem, 2);
            if (count($parts) !== 2) {
                continue;
            }

            [$pathPart, $password] = $parts;
            $pathPart = strtolower(trim(trans_absolute_path($pathPart), '/'));

            if ($normalizedQuery === $pathPart || Str::startsWith($normalizedQuery, $pathPart . '/')) {
                return [
                    'need_password' => true,
                    'password' => $password,
                    'encrypt_path' => $pathPart,
                ];
            }
        }

        return ['need_password' => false, 'password' => '', 'encrypt_path' => ''];
    }

    /**
     * 验证加密路径密码
     *
     * @param string $encryptPath
     * @param string $password
     * @return bool
     */
    public function verifyEncryptedPassword(string $encryptPath, string $password): bool
    {
        $cookieKey = "e:{$this->hash}:{$encryptPath}";

        if (!Cookie::has($cookieKey)) {
            return false;
        }

        $data = json_decode(Cookie::get($cookieKey), true);

        if (!isset($data['password'])) {
            return false;
        }

        return strcmp($password, decrypt($data['password'])) === 0;
    }

    /**
     * 获取资源项（带缓存）
     *
     * @param string $query
     * @return array
     */
    public function fetchItem(string $query): array
    {
        $cacheKey = $this->buildCacheKey('item', $query);

        $item = Cache::remember($cacheKey, setting('cache_expires'), function () use ($query) {
            return $this->getOneDrive()->fetchItem($query);
        });

        if (isset($item['code'])) {
            Cache::forget($cacheKey);
            return $item;
        }

        // 缓存 ID 映射
        $idCacheKey = $this->buildCacheKey('item', $item['id'] ?? '');
        Cache::add($idCacheKey, $item, setting('cache_expires'));

        return $item;
    }

    /**
     * 获取资源列表（带缓存）
     *
     * @param string $query
     * @return array
     */
    public function fetchList(string $query): array
    {
        $cacheKey = $this->buildCacheKey('list', $query);

        $list = Cache::remember($cacheKey, setting('cache_expires'), function () use ($query) {
            return $this->getOneDrive()->fetchList($query);
        });

        if (isset($list['code'])) {
            Cache::forget($cacheKey);
        }

        return $list;
    }

    /**
     * 过滤隐藏文件
     *
     * @param LazyCollection|Collection $list
     * @param string $hidePath
     * @param array $pathSegments
     * @return LazyCollection|Collection
     */
    public function filterHiddenItems($list, string $hidePath, array $pathSegments)
    {
        if (blank($hidePath)) {
            return $list;
        }

        $hidePaths = array_filter(explode('|', $hidePath));

        return $list->filter(function ($item) use ($hidePaths, $pathSegments) {
            $query = implode('/', Arr::add($pathSegments, key(array_slice($pathSegments, -1, 1, true)) + 1, $item['name']));
            $query = strtolower($query);

            foreach ($hidePaths as $hideItem) {
                $hideItem = strtolower(trim(trans_absolute_path($hideItem), '/'));
                $normalizedQuery = trim(trans_absolute_path(rawurldecode($query)), '/');

                if ($normalizedQuery === $hideItem || Str::startsWith($normalizedQuery, $hideItem . '/')) {
                    return false;
                }
            }

            return true;
        });
    }

    /**
     * 过滤系统文件
     *
     * @param LazyCollection|Collection $list
     * @return LazyCollection|Collection
     */
    public function filterSystemItems($list)
    {
        return $list->filter(function ($item) {
            // 过滤微软内置无法读取的文件
            if (isset($item['package']['type'])) {
                return false;
            }

            // 过滤预留文件
            $name = strtoupper(trim($item['name'] ?? ''));
            return !in_array($name, ['README.MD', 'HEAD.MD', '.PASSWORD', '.DENY'], true);
        });
    }

    /**
     * 过滤非法预览文件
     *
     * @param array $item
     * @return array
     */
    public function filterPreviewItem(array $item): array
    {
        $illegalFiles = ['README.md', 'HEAD.md', '.password', '.deny'];
        $pattern = '/^README\.md|HEAD\.md|\.password|\.deny/';

        if (in_array($item['name'], $illegalFiles, true) || preg_match($pattern, $item['name']) > 0) {
            abort(403, '非法请求');
        }

        // 处理隐藏文件
        $storeHideKey = "h:{$this->hash}";
        $hiddenIds = setting($storeHideKey, []);

        if (in_array($item['id'], $hiddenIds, true)) {
            abort(404, '文件不存在');
        }

        return $item;
    }

    /**
     * 格式化文件项
     *
     * @param LazyCollection|Collection|array $data
     * @param bool $isFile
     * @return LazyCollection|Collection|array
     */
    public function formatItems($data, bool $isFile = false)
    {
        if ($isFile) {
            $data['ext'] = strtolower(pathinfo($data['name'], PATHINFO_EXTENSION));
            return $data;
        }

        return $data->map(function ($item) {
            if (isset($item['file'])) {
                $item['ext'] = strtolower(pathinfo($item['name'], PATHINFO_EXTENSION));
            } else {
                $item['ext'] = 'folder';
            }
            return $item;
        });
    }

    /**
     * 搜索过滤
     *
     * @param LazyCollection|Collection $list
     * @param string $keywords
     * @return LazyCollection|Collection
     */
    public function searchItems($list, string $keywords)
    {
        return $list->filter(function ($item) use ($keywords) {
            $name = trim($item['name'] ?? '');
            return Str::contains($name, $keywords);
        });
    }

    /**
     * 排序列表
     *
     * @param LazyCollection|Collection $list
     * @param string $field
     * @param bool $descending
     * @return array
     */
    public function sortItems($list, string $field = 'name', bool $descending = false): array
    {
        $folders = $list->filter(function ($item) {
            return isset($item['folder']);
        });

        $files = $list->filter(function ($item) {
            return !isset($item['folder']);
        });

        $sortFlags = $field === 'name' ? SORT_NATURAL : SORT_REGULAR;

        if (!$descending) {
            $folders = $folders->sortBy($field, $sortFlags);
            $files = $files->sortBy($field, $sortFlags);
        } else {
            $folders = $folders->sortByDesc($field, $sortFlags);
            $files = $files->sortByDesc($field, $sortFlags);
        }

        return $folders->merge($files)->all();
    }

    /**
     * 分页处理
     *
     * @param array $items
     * @param int $perPage
     * @return array
     */
    public function paginateItems(array $items, int $perPage = 10): array
    {
        $currentPage = request()->input('page', 1);
        $offset = ($currentPage - 1) * $perPage;

        return array_slice($items, $offset, $perPage);
    }

    /**
     * 获取说明文件内容
     *
     * @param LazyCollection|Collection $list
     * @return array{head: string, readme: string}
     */
    public function fetchDocContent($list): array
    {
        $readme = $list->filter(function ($item) {
            return $item['name'] === 'README.md';
        });

        $head = $list->filter(function ($item) {
            return $item['name'] === 'HEAD.md';
        });

        $result = ['head' => '', 'readme' => ''];

        if ($readme->isNotEmpty()) {
            $readmeItem = $readme->first();
            $result['readme'] = $this->fetchFileContent($readmeItem['id'], $readmeItem['@microsoft.graph.downloadUrl']);
        }

        if ($head->isNotEmpty()) {
            $headItem = $head->first();
            $result['head'] = $this->fetchFileContent($headItem['id'], $headItem['@microsoft.graph.downloadUrl']);
        }

        return $result;
    }

    /**
     * 获取文件内容（带缓存）
     *
     * @param string $itemId
     * @param string $downloadUrl
     * @return string
     */
    private function fetchFileContent(string $itemId, string $downloadUrl): string
    {
        try {
            return Cache::remember(
                "d:content:{$this->accountId}:{$itemId}",
                setting('cache_expires'),
                function () use ($downloadUrl) {
                    return \App\Helpers\Tool::fetchContent($downloadUrl);
                }
            );
        } catch (\Exception $e) {
            Cache::forget("d:content:{$this->accountId}:{$itemId}");
            return '';
        }
    }

    /**
     * 构建缓存键
     *
     * @param string $type
     * @param string $identifier
     * @return string
     */
    public function buildCacheKey(string $type, string $identifier): string
    {
        // 使用 md5 缩短路径，避免特殊字符问题
        return "d:{$type}:{$this->accountId}:" . md5($identifier);
    }

    /**
     * 判断文件类型
     *
     * @param string $ext
     * @return string
     */
    public function determineFileType(string $ext): string
    {
        $showList = [
            'stream' => explode(' ', setting('show_stream')),
            'image' => explode(' ', setting('show_image')),
            'video' => explode(' ', setting('show_video')),
            'dash' => explode(' ', setting('show_dash')),
            'audio' => explode(' ', setting('show_audio')),
            'code' => explode(' ', setting('show_code')),
            'doc' => explode(' ', setting('show_doc')),
        ];

        foreach ($showList as $type => $suffixes) {
            if (in_array($ext, $suffixes, true)) {
                return $type;
            }
        }

        return 'other';
    }

    /**
     * 处理文件预览信息
     *
     * @param array $file
     * @param string $type
     * @return array
     */
    public function processFilePreview(array $file, string $type): array
    {
        $file['download'] = $file['@microsoft.graph.downloadUrl'];

        // 处理文本文件
        if (in_array($type, ['stream', 'code']) && $file['size'] < 5 * 1024 * 1024) {
            try {
                $file['content'] = Cache::remember(
                    "d:content:{$this->accountId}:{$file['id']}",
                    setting('cache_expires'),
                    function () use ($file) {
                        return \App\Helpers\Tool::fetchContent($file['download']);
                    }
                );

                if ($type === 'stream') {
                    $type = 'code';
                }
            } catch (\Exception $e) {
                Cache::forget("d:content:{$this->accountId}:{$file['id']}");
                $file['content'] = '';
            }
        }

        // 处理缩略图
        if (in_array($type, ['image', 'dash', 'video'])) {
            $file['thumb'] = $file['thumbnails'][0]['large']['url'] ?? null;
        }

        // dash视频流
        if ($type === 'dash') {
            if (!strpos($file['download'], 'sharepoint.com')) {
                $type = 'other';
            } elseif (isset($file['thumb'])) {
                $replace = str_replace('thumbnail', 'videomanifest', $file['thumb']);
                $file['dash'] = $replace . '&part=index&format=dash&useScf=True&pretranscode=0&transcodeahead=0';
            }
        }

        // 处理微软文档
        if ($type === 'doc') {
            $file['preview'] = 'https://view.officeapps.live.com/op/view.aspx?src=' . urlencode($file['download']);
        }

        return ['file' => $file, 'show' => $type];
    }
}