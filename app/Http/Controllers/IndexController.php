<?php

namespace App\Http\Controllers;

use App\Utils\Extension;
use Cache;
use Session;
use Auth;
use ErrorException;
use App\Service\OneDrive;
use App\Utils\Tool;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * OneDrive 目录索引
 * Class IndexController
 *
 * @package App\Http\Controllers
 */
class IndexController extends Controller
{

    /**
     * 缓存超时时间(秒) 建议10分钟以下，否则会导致资源失效
     *
     * @var int|mixed|string
     */
    public $expires = 1200;

    /**
     * 根目录
     *
     * @var mixed|string
     */
    public $root = '/';

    /**
     * 展示文件数组
     *
     * @var array
     */
    public $show = [];

    /**
     * IndexController constructor.
     */
    public function __construct()
    {
        $this->middleware(['verify.installation', 'verify.token', 'handle.forbid', 'handle.hide']);
        $this->middleware('handle.encrypt')->only(setting('encrypt_option', ['list']));
        $this->middleware('hotlink.protection')->only(['show', 'download', 'thumb', 'thumbCrop']);
        $this->middleware('throttle:' . setting('search_throttle'))->only(['search', 'searchShow']);


        $this->expires = setting('expires', 1200);
        $this->root = setting('root', '/');
        $this->show = [
            'stream' => explode(' ', setting('stream')),
            'image' => explode(' ', setting('image')),
            'video' => explode(' ', setting('video')),
            'dash' => explode(' ', setting('dash')),
            'audio' => explode(' ', setting('audio')),
            'code' => explode(' ', setting('code')),
            'doc' => explode(' ', setting('doc')),
        ];
    }

    /**
     * 首页
     * @return Factory|RedirectResponse|View
     */
    public function home()
    {
        if (setting('image_home', 0)) {
            if ((int)setting('image_hosting', 0) === 0 || ((int)setting('image_hosting', 0) === 2 && Auth::guest())) {
                return redirect()->route('home');
            }
            return view(config('olaindex.theme') . 'image');
        }
        return redirect()->route('home');
    }

    /**
     * 列表
     * @param Request $request
     *
     * @return Factory|RedirectResponse|View
     * @throws ErrorException
     */
    public function list(Request $request)
    {
        // 处理路径
        $requestPath = $request->route()->parameter('query', '/');
        $graphPath = Tool::getOriginPath($requestPath);
        $queryPath = trim(Tool::getAbsolutePath($requestPath), '/');
        $originPath = rawurldecode($queryPath);
        $pathArray = $originPath ? explode('/', $originPath) : [];

        // 获取资源缓存
        $pathKey = 'one:path:' . $graphPath;
        if (Cache::has($pathKey)) {
            $item = Cache::get($pathKey);
        } else {
            $response = OneDrive::getInstance(one_account())->getItemByPath($graphPath);
            if ($response['errno'] === 0) {
                $item = $response['data'];
                if (!Arr::has($item, 'folder')) {
                    return $this->show($request);
                }
                Cache::put($pathKey, $item, $this->expires);
            } else {
                Tool::showMessage($response['msg'], false);

                return view(config('olaindex.theme') . 'message');
            }
        }
        if (Arr::has($item, '@microsoft.graph.downloadUrl')) {
            return redirect()->away($item['@microsoft.graph.downloadUrl']);
        }
        // 获取列表资源
        $key = 'one:list:' . $graphPath;
        if (Cache::has($key)) {
            $originItems = Cache::get($key);
        } else {
            $response = OneDrive::getInstance(one_account())->getItemListByPath(
                $graphPath,
                '?select=id,eTag,name,size,lastModifiedDateTime,file,image,folder,'
                . 'parentReference,@microsoft.graph.downloadUrl&expand=thumbnails'
            );

            if ($response['errno'] === 0) {
                $originItems = $response['data'];
                Cache::put($key, $originItems, $this->expires);
            } else {
                Tool::showMessage($response['msg'], false);

                return view(config('olaindex.theme') . 'message');
            }
        }

        // 处理 head/readme
        $head = array_key_exists('HEAD.md', $originItems)
            ? Tool::getFileContent($originItems['HEAD.md']['@microsoft.graph.downloadUrl'], $graphPath . ':head')
            : '';
        $readme = array_key_exists('README.md', $originItems)
            ? Tool::getFileContent($originItems['README.md']['@microsoft.graph.downloadUrl'], $graphPath . ':readme')
            : '';
        // 过滤微软OneNote文件
        $originItems = Arr::where($originItems, static function ($value) {
            return !Arr::has($value, 'package.type');
        });

        if (Auth::guest()) {
            // 过滤隐藏文件
            $hideDir = Tool::handleHideItem(setting('hide_path'));
            $originItems = Arr::where($originItems, static function ($value) use ($hideDir) {
                $parentPath = Arr::get($value, 'parentReference.path');
                $filePath = Str::after(
                    $parentPath . '/' . $value['name'],
                    '/drive/root:/' . trim(setting('root'), '/')
                );
                return !in_array(trim($filePath, '/'), $hideDir, false);
            });

            // 过滤预留文件
            $originItems = Arr::except(
                $originItems,
                ['README.md', 'HEAD.md', '.password', '.deny']
            );
        }

        $order = $request->get('orderBy');
        @list($field, $sortBy) = explode(',', $order);
        $itemsBase = collect($originItems);

        // 默认排序字段
        if ($field === '' || $sortBy === null) {
            $field = 'name';
            $sortBy = 'asc';
        }

        // 筛选文件夹/文件夹
        $folders = $itemsBase->filter(static function ($value) {
            return Arr::has($value, 'folder');
        });
        $files = $itemsBase->filter(static function ($value) {
            return !Arr::has($value, 'folder');
        });

        // 执行文件夹/文件夹 排序
        if (strtolower($sortBy) !== 'desc') {
            $folders = $folders->sortBy($field, $field === 'name' ? SORT_NATURAL : SORT_REGULAR)->toArray();
            $files = $files->sortBy($field, $field === 'name' ? SORT_NATURAL : SORT_REGULAR)->toArray();
        } else {
            $folders = $folders->sortByDesc($field, $field === 'name' ? SORT_NATURAL : SORT_REGULAR)->toArray();
            $files = $files->sortByDesc($field, $field === 'name' ? SORT_NATURAL : SORT_REGULAR)->toArray();
        }
        // 合并
        $originItems = collect($folders)->merge($files)->toArray();

        $limit = $request->get('limit', 20);
        $items = Tool::paginate($originItems, $limit);
        $parent_item = $item;

        $hasImage = Tool::hasImages($originItems);

        $size = collect($originItems)->sum('size');

        $data = compact(
            'size',
            'parent_item',
            'items',
            'originItems',
            'originPath',
            'pathArray',
            'head',
            'readme',
            'hasImage'
        );

        return view(config('olaindex.theme') . 'one', $data);
    }

    /**
     * 获取文件信息或缓存
     *
     * @param $realPath
     *
     * @return mixed
     */
    public function getFileOrCache($realPath)
    {
        $absolutePath = Tool::getAbsolutePath($realPath);
        $absolutePathArr = explode('/', $absolutePath);
        $absolutePathArr = Arr::where($absolutePathArr, static function ($value) {
            return $value !== '';
        });
        $name = array_pop($absolutePathArr);
        $absolutePath = implode('/', $absolutePathArr);
        $listPath = Tool::getOriginPath($absolutePath);
        $list = Cache::get('one:list:' . $listPath, '');
        if ($list && array_key_exists($name, $list)) {
            return $list[$name];
        }
        $graphPath = Tool::getOriginPath($realPath);
        // 获取文件
        return Cache::remember(
            'one:file:' . $graphPath,
            $this->expires,
            static function () use ($graphPath) {
                $response = OneDrive::getInstance(one_account())->getItemByPath(
                    $graphPath,
                    '?select=id,eTag,name,size,lastModifiedDateTime,file,image,@microsoft.graph.downloadUrl'
                    . '&expand=thumbnails'
                );
                if ($response['errno'] === 0) {
                    return $response['data'];
                }
                return null;
            }
        );
    }

    /**
     * 展示文件
     * @param Request $request
     *
     * @return Factory|RedirectResponse|View
     * @throws ErrorException
     */
    public function show(Request $request)
    {
        $requestPath = $request->route()->parameter('query', '/');
        if ($requestPath === '/') {
            return redirect()->route('home');
        }
        $file = $this->getFileOrCache($requestPath);
        if ($file === null || Arr::has($file, 'folder')) {
            Tool::showMessage('获取文件失败，请检查路径或稍后重试', false);

            return view(config('olaindex.theme') . 'message');
        }
        $file['download'] = $file['@microsoft.graph.downloadUrl'];
        foreach ($this->show as $key => $suffix) {
            if (in_array($file['ext'] ?? '', $suffix, false)) {
                $view = 'show.' . $key;
                // 处理文本文件
                if (in_array($key, ['stream', 'code'])) {
                    if ($file['size'] > 5 * 1024 * 1024) { // 文件>5m
                        Tool::showMessage('文件过大，请下载查看', false);

                        return redirect()->back();
                    }
                    $file['content'] = Tool::getFileContent($file['@microsoft.graph.downloadUrl'], false);
                    if ($key === 'stream') {
                        $fileType
                            = empty(Extension::FILE_STREAM[$file['ext'] ?? 'file'])
                            ? 'application/octet-stream'
                            : Extension::FILE_STREAM[$file['ext'] ?? 'file'];

                        return response($file['content'], 200, ['Content-type' => $fileType,]);
                    }
                }

                // 处理缩略图
                if (in_array($key, ['image', 'dash', 'video'])) {
                    $file['thumb'] = Arr::get($file, 'thumbnails.0.large.url');
                }

                // dash视频流
                if ($key === 'dash') {
                    if (!strpos($file['@microsoft.graph.downloadUrl'], 'sharepoint.com')) {
                        return redirect()->away($file['download']);
                    }
                    $replace = str_replace('thumbnail', 'videomanifest', $file['thumb']);
                    $file['dash'] = $replace . '&part=index&format=dash&useScf=True&pretranscode=0&transcodeahead=0';
                }

                // 处理微软文档
                if ($key === 'doc') {
                    $url = 'https://view.officeapps.live.com/op/view.aspx?src='
                        . urlencode($file['@microsoft.graph.downloadUrl']);

                    return redirect()->away($url);
                }
                $originPath = rawurldecode(trim(Tool::getAbsolutePath($requestPath), '/'));
                $pathArray = $originPath ? explode('/', $originPath) : [];
                $data = compact('file', 'pathArray', 'originPath');

                return view(config('olaindex.theme') . $view, $data);
            }
        }

        return redirect()->away($file['download']);
    }

    /**
     * 下载
     * @param Request $request
     *
     * @return mixed
     */
    public function download(Request $request)
    {
        $requestPath = $request->route()->parameter('query', '/');
        if ($requestPath === '/') {
            Tool::showMessage('下载失败，请检查路径或稍后重试', false);

            return view(config('olaindex.theme') . 'message');
        }
        $file = $this->getFileOrCache($requestPath);
        if ($file === null || Arr::has($file, 'folder')) {
            Tool::showMessage('下载失败，请检查路径或稍后重试', false);

            return view(config('olaindex.theme') . 'message');
        }
        $url = $file['@microsoft.graph.downloadUrl'];

        return redirect()->away($url);
    }

    /**
     * 查看缩略图
     *
     * @param $id
     * @param $size
     *
     * @return RedirectResponse
     * @throws ErrorException
     */
    public function thumb($id, $size): RedirectResponse
    {
        $response = OneDrive::getInstance(one_account())->thumbnails($id, $size);
        if ($response['errno'] === 0) {
            $url = $response['data']['url'];
        } else {
            $url = 'https://i.loli.net/2018/12/04/5c05cd3086425.png';
        }

        return redirect()->away($url);
    }

    /**
     * 指定缩略图
     *
     * @param $id
     * @param $width
     * @param $height
     *
     * @return RedirectResponse
     * @throws ErrorException
     */
    public function thumbCrop($id, $width, $height): RedirectResponse
    {
        $response = OneDrive::getInstance(one_account())->thumbnails($id, 'large');
        if ($response['errno'] === 0) {
            $url = $response['data']['url'];
            @list($url, $tmp) = explode('&width=', $url);
            unset($tmp);
            $url .= strpos($url, '?') ? '&' : '?';
            $thumb = $url . "width={$width}&height={$height}";
        } else {
            $thumb = 'https://i.loli.net/2018/12/04/5c05cd3086425.png';
        }

        return redirect()->away($thumb);
    }

    /**
     * 搜索
     * @param Request $request
     *
     * @return Factory|View
     * @throws ErrorException
     */
    public function search(Request $request)
    {
        if (!setting('open_search', 0)) {
            Tool::showMessage('搜索暂不可用', false);
            return view(config('olaindex.theme') . 'message');
        }
        $keywords = $request->get('keywords');
        $limit = $request->get('limit', 20);
        if ($keywords) {
            $path = Tool::encodeUrl($this->root);
            $response = OneDrive::getInstance(one_account())->search($path, $keywords);
            if ($response['errno'] === 0) {
                // 过滤结果中的文件夹\过滤微软OneNote文件
                $items = Arr::where($response['data'], static function ($value) {
                    return !Arr::has($value, 'folder') && !Arr::has($value, 'package.type');
                });
            } else {
                Tool::showMessage('搜索失败', false);
                $items = [];
            }
        } else {
            $items = [];
        }
        $items = Tool::paginate($items, $limit);

        return view(config('olaindex.theme') . 'search', compact('items'));
    }

    /**
     * 搜索显示
     * @param $id
     *
     * @return RedirectResponse
     * @throws ErrorException
     */
    public function searchShow($id): RedirectResponse
    {
        if (!setting('open_search', 0)) {
            Tool::showMessage('搜索暂不可用', false);
            return view(config('olaindex.theme') . 'message');
        }
        $response = OneDrive::getInstance(one_account())->itemIdToPath($id, setting('root'));
        if ($response['errno'] === 0) {
            $originPath = $response['data']['path'];
            if (trim($this->root, '/') !== '') {
                $path = Str::after($originPath, $this->root);
            } else {
                $path = $originPath;
            }
        } else {
            Tool::showMessage('获取连接失败', false);
            $path = '/';
        }

        return redirect()->route('show', $path);
    }

    /**
     * 处理加密文档
     * @return Factory|RedirectResponse|View|void
     */
    public function handleEncrypt()
    {
        $password = request()->get('password');
        $route = decrypt(request()->get('route'));
        $requestPath = decrypt(request()->get('requestPath'));
        $encryptKey = decrypt(request()->get('encryptKey'));
        $data = [
            'password' => encrypt($password),
            'encryptKey' => $encryptKey,
            'expires' => time() + (int)$this->expires * 60, // 目录密码过期时间
        ];
        Session::put($encryptKey, $data);

        $encryptDir = Tool::handleEncryptItem(setting('encrypt_path'));
        $encryptPath = Str::after($encryptKey, 'password:');
        $directory_password = $encryptDir['p>' . $encryptPath];
        if (strcmp($password, $directory_password) === 0) {
            return redirect()->route($route, ['query' => Tool::encodeUrl($requestPath)]);
        }
        Tool::showMessage('密码错误', false);

        return view(
            config('olaindex.theme') . 'password',
            compact('route', 'requestPath', 'encryptKey')
        );
    }
}
