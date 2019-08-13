<?php

namespace App\Http\Controllers\Index;

use App\Helpers\Tool;
use App\Helpers\OneDrive;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use App\Http\Resources\ItemResource;
use App\Services\CacheService;
use App\Http\Controllers\Controller;
use App\Http\Resources\ShowResource;

/**
 * OneDriveGraph 索引
 * Class IndexController
 *
 * @package App\Http\Controllers
 */
class IndexController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws \ErrorException
     */
    public function list(Request $request)
    {
        $realPath = $request->route()->parameter('query') ?? '/';
        $data = $request->validate([
            'by'    => 'string|in:name,lastModifiedDateTime,size',
            'sort'  => 'string|in:asc,desc',
            'limit' => 'integer'
        ]);

        $graphPath = Tool::getOriginPath($realPath);
        $queryPath = trim(Tool::getAbsolutePath($realPath), '/');
        $origin_path = rawurldecode($queryPath);
        $path_array = $origin_path ? explode('/', $origin_path) : [];
        $pathKey = 'one_' . app('onedrive')->id . ':path:' . $graphPath;
        $item = (new CacheService('getItemByPath', $graphPath))->get($pathKey);

        if (Arr::has($item, '@microsoft.graph.downloadUrl')) {
            return redirect()->away($item['@microsoft.graph.downloadUrl']);
        }

        // 获取列表
        $key = 'one_' . app('onedrive')->id . ':list:' . $graphPath;
        $origin_items = (new CacheService('getChildrenByPath', $graphPath))->get($key, [
            '?select=id,eTag,name,size,lastModifiedDateTime,file,image,folder,@microsoft.graph.downloadUrl&expand=thumbnails'
        ]);

        // 处理排序
        $origin_items = collect($origin_items);

        if (strtolower(Arr::get($data, 'sort', 'desc')) !== 'desc') {
            $origin_items = $origin_items->sortBy(Arr::get($data, 'by', 'name'));
        } else {
            $origin_items = $origin_items->sortByDesc(Arr::get($data, 'by', 'name'));
        }

        $origin_items = $origin_items->toArray();

        $hasImage = Tool::hasImages($origin_items);
        // 过滤微软OneNote文件
        $origin_items = Arr::where($origin_items, function ($value) {
            return !Arr::has($value, 'package.type');
        });

        // 处理 head/readme
        $head = array_key_exists('HEAD.md', $origin_items)
            ? markdown2Html(getFileContent($origin_items['HEAD.md']['@microsoft.graph.downloadUrl']))
            : '';
        $readme = array_key_exists('README.md', $origin_items)
            ? markdown2Html(getFileContent($origin_items['README.md']['@microsoft.graph.downloadUrl']))
            : '';
        if (!auth()->guard('admin')->check()) {
            $origin_items = Arr::except(
                $origin_items,
                ['README.md', 'HEAD.md', '.password', '.deny']
            );
        }

        $origin_items = ItemResource::collection(collect($origin_items));
        $items = Tool::paginate($origin_items->toArray($request), Arr::get($data, 'limit', 20));
        $parent_item = $item;
        $data = compact(
            'parent_item',
            'items',
            'origin_items',
            'origin_path',
            'path_array',
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
        $absolutePathArr = Arr::where($absolutePathArr, function ($value) {
            return $value !== '';
        });
        $name = array_pop($absolutePathArr);
        $absolutePath = implode('/', $absolutePathArr);
        $listPath = Tool::getOriginPath($absolutePath);
        $list = Cache::get('one_' . app('onedrive')->id . ':list:' . $listPath, '');
        $graphPath = Tool::getOriginPath($realPath);

        if ($list && array_key_exists($name, $list)) {
            return $list[$name];
        } else {
            return (new CacheService('getItemByPath', $graphPath))->get('one_' . app('onedrive')->id . ':file:' . $graphPath, [
                '?select=id,eTag,name,size,lastModifiedDateTime,file,image,folder,@microsoft.graph.downloadUrl&expand=thumbnails'
            ]);
        }
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws \ErrorException
     */
    public function show(Request $request)
    {
        $realPath = $request->route()->parameter('query') ?? '/';
        if ($realPath === '/') {
            return redirect()->route('home');
        }

        $file = $this->getFileOrCache($realPath);
        if (is_null($file) || Arr::has($file, 'folder')) {
            Tool::showMessage('获取文件失败，请检查路径或稍后重试', false);

            return view(config('olaindex.theme') . 'message');
        }

        $file['realPath'] = $realPath;
        $file['download'] = $file['@microsoft.graph.downloadUrl'];

        return (new ShowResource($file))->toArray($request);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function download(Request $request)
    {
        $realPath = $request->route()->parameter('query') ?? '/';
        if ($realPath === '/') {
            Tool::showMessage('下载失败，请检查路径或稍后重试', false);

            return view(config('olaindex.theme') . 'message');
        }

        $file = $this->getFileOrCache($realPath);
        if (is_null($file) || Arr::has($file, 'folder')) {
            Tool::showMessage('下载失败，请检查路径或稍后重试', false);

            return view(config('olaindex.theme') . 'message');
        }

        $url = $file['@microsoft.graph.downloadUrl'];

        return redirect()->away($url);
    }

    /**
     * @param $id
     * @param $size
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \ErrorException
     */
    public function thumb($id, $size)
    {
        $response = OneDrive::thumbnails($id, $size);
        $url = 'https://i.loli.net/2018/12/04/5c05cd3086425.png';

        if ($response['errno'] === 0) {
            $url = $response['data']['url'];
        }

        return redirect()->away($url);
    }

    /**
     * @param $id
     * @param $width
     * @param $height
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \ErrorException
     */
    public function thumbCrop($id, $width, $height)
    {
        $response = OneDrive::thumbnails($id, 'large');
        $thumb = 'https://i.loli.net/2018/12/04/5c05cd3086425.png';

        if ($response['errno'] === 0) {
            $url = $response['data']['url'];
            @list($url, $tmp) = explode('&width=', $url);
            $url .= strpos($url, '?') ? '&' : '?';
            $thumb = $url . "width={$width}&height={$height}";
        }

        return redirect()->away($thumb);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function view(Request $request)
    {
        $realPath = $request->route()->parameter('query') ?? '/';
        if ($realPath === '/') {
            Tool::showMessage('获取失败，请检查路径或稍后重试', false);

            return view(config('olaindex.theme') . 'message');
        }
        $file = $this->getFileOrCache($realPath);
        if (is_null($file) || Arr::has($file, 'folder')) {
            Tool::showMessage('获取失败，请检查路径或稍后重试', false);

            return view(config('olaindex.theme') . 'message');
        }
        $download = $file['@microsoft.graph.downloadUrl'];

        return redirect()->away($download);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \ErrorException
     */
    public function search(Request $request)
    {
        $data = $request->validate([
            'keywords' => 'required|string',
            'limit'    => 'integer'
        ]);

        $limit = Arr::get($data, 'limit', 20);
        $items = [];
        $path = Tool::getEncodeUrl(app('onedrive')->root);
        $response = OneDrive::search($path, $data['keywords']);
        $path_array = [];

        if ($response['errno'] === 0) {
            // 过滤结果中的文件夹\过滤微软OneNote文件
            $items = Arr::where($response['data'], function ($value) {
                return !Arr::has($value, 'folder') && !Arr::has($value, 'package.type');
            });
        } else {
            Tool::showMessage('搜索失败', true);
        }

        $items = Tool::paginate($items, $limit);

        return view(config('olaindex.theme') . 'search', compact('items', 'path_array'));
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \ErrorException
     */
    public function searchShow($id)
    {
        $response = OneDrive::itemIdToPath($id, Tool::config('root'));

        if ($response['errno'] !== 0) {
            Tool::showMessage('获取连接失败', false);
            return redirect()->route('show', '/');
        }

        $originPath = $response['data']['path'];
        $path = $originPath;

        if (trim(app('onedrive')->root, '/') != '') {
            $path = Str::after($originPath, app('onedrive')->root);
        }

        return redirect()->route('show', $path);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function handlePassword()
    {
        $password = request()->get('password');
        $route = decrypt(request()->get('route'));
        $realPath = decrypt(request()->get('realPath'));
        $encryptKey = decrypt(request()->get('encryptKey'));
        $data = [
            'password'   => encrypt($password),
            'encryptKey' => $encryptKey,
            'expires'    => time() + (int) app('onedrive')->expires * 60, // 目录密码过期时间
        ];
        Session::put('password:' . $encryptKey, $data);
        $arr = Tool::handleEncryptDir(Tool::config('encrypt_path'));
        $directory_password = $arr[$encryptKey];
        if (strcmp($password, $directory_password) === 0) {
            return redirect()->route($route, Tool::getEncodeUrl($realPath));
        } else {
            Tool::showMessage('密码错误', false);

            return view(
                config('olaindex.theme') . 'password',
                compact('route', 'realPath', 'encryptKey')
            );
        }
    }
}
