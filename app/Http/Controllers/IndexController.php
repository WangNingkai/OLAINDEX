<?php

namespace App\Http\Controllers;

use App\Helpers\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

/**
 * OneDrive 索引
 * Class IndexController
 * @package App\Http\Controllers
 */
class IndexController extends Controller
{
    /**
     * @var OneDriveController
     */
    public $od;

    /**
     * 缓存超时时间 建议10分钟以下，否则会导致资源失效
     * @var int|mixed|string
     */
    public $expires = 10;

    /**
     * 根目录
     * @var mixed|string
     */
    public $root = '/';

    /**
     * 展示文件数组
     * @var array
     */
    public $show = [];

    /**
     * IndexController constructor.
     */
    public function __construct()
    {
        $this->middleware('checkToken');
        $this->middleware('handleIllegalFile');
        $od = new OneDriveController();
        $this->od = $od;
        $this->expires = Tool::config('expires', 10);
        $this->root = Tool::config('root', '/');
        $this->show = [
            'stream' => explode(' ', Tool::config('stream')),
            'image' => explode(' ', Tool::config('image')),
            'video' => explode(' ', Tool::config('video')),
            'dash' => explode(' ', Tool::config('dash')),
            'audio' => explode(' ', Tool::config('audio')),
            'code' => explode(' ', Tool::config('code')),
            'doc' => explode(' ', Tool::config('doc')),
        ];
    }

    /**
     * 首页
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function home(Request $request)
    {
        return $this->list($request);
    }

    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function list(Request $request)
    {
        $graphPath = Tool::convertPath($request->getPathInfo());
        $origin_path = rawurldecode(Tool::convertPath($request->getPathInfo(), false));
        $origin_items = Cache::remember('one:' . $graphPath, $this->expires, function () use ($graphPath) {
            $response = $this->od->listChildrenByPath($graphPath);
            $response['value'] = $this->od->getNextLinkList($response, $response['value']);
            return $this->od->formatArray($response);
        });
        $hasImage = Tool::hasImages($origin_items);
        $origin_items = array_where($origin_items, function ($value) {
            return !array_has($value,'package.type');
        });
        // 处理加密目录
        if (!empty($origin_items['.password'])) {
            $pass_id = $origin_items['.password']['id'];
            $pass_url = $origin_items['.password']['@microsoft.graph.downloadUrl'];
            $key = 'password:' . $origin_path;
            if (Session::has($key)) {
                $data = Session::get($key);
                $expires = $data['expires'];
                $password = Tool::getFileContent($pass_url);
                if ($password != decrypt($data['password']) || time() > $expires) {
                    Session::forget($key);
                    Tool::showMessage('密码已过期', false);
                    return view('password', compact('origin_path', 'pass_id'));
                }
            } else return view('password', compact('origin_path', 'pass_id'));
        }
        // 过滤目录&处理内容
        Tool::hasForbidFolder($origin_items);
        $head = array_key_exists('HEAD.md', $origin_items) ? Tool::markdown2Html(Tool::getFileContent($origin_items['HEAD.md']['@microsoft.graph.downloadUrl'])) : '';
        $readme = array_key_exists('README.md', $origin_items) ? Tool::markdown2Html(Tool::getFileContent($origin_items['README.md']['@microsoft.graph.downloadUrl'])) : '';
        $path_array = $origin_path ? explode('/', $origin_path) : [];
        if (!session()->has('LogInfo')) $origin_items = Tool::filterFiles($origin_items, ['README.md', 'HEAD.md', '.password', '.deny']);
        $items = Tool::paginate($origin_items, 20);
        return view('one', compact('items', 'origin_items', 'origin_path', 'path_array', 'head', 'readme', 'hasImage'));
    }

    /**
     * 展示
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function show(Request $request)
    {
        $graphPath = Tool::convertPath($request->getPathInfo(), true, true);
        $origin_path = urldecode(Tool::convertPath($request->getPathInfo(), false));
        $path_array = $origin_path ? explode('/', $origin_path) : [];
        $file = Cache::remember('one:' . $graphPath, $this->expires, function () use ($graphPath) {
            $response = $this->od->getItemByPath($graphPath);
            return $this->od->formatArray($response, false);
        });
        if (isset($file['folder'])) abort(403);
        $file['download'] = $file['@microsoft.graph.downloadUrl'];
        $patterns = $this->show;
        foreach ($patterns as $key => $suffix) {
            if (in_array($file['ext'], $suffix)) {
                $view = 'show.' . $key;
                if (in_array($key, ['stream', 'code'])) {
                    if ($file['size'] > 5 * 1024 * 1024) {
                        Tool::showMessage('文件过大，请下载查看', false);
                        return redirect()->back();
                    } else $file['content'] = Tool::getFileContent($file['@microsoft.graph.downloadUrl']);
                }
                if (in_array($key, ['image', 'dash', 'video'])) {
                    $file['thumb'] = $this->od->thumbnails($file['id'], 'large');
                }
                if ($key == 'dash') {
                    if (strpos($file['@microsoft.graph.downloadUrl'], "sharepoint.com") == false) return redirect()->away($file['download']);
                    $file['dash'] = str_replace("thumbnail", "videomanifest", $file['thumb']) . "&part=index&format=dash&useScf=True&pretranscode=0&transcodeahead=0";
                }
                if ($key == 'doc') {
                    $url = "https://view.officeapps.live.com/op/view.aspx?src=" . urlencode($file['@microsoft.graph.downloadUrl']);
                    return redirect()->away($url);
                }
                return view($view, compact('file', 'path_array', 'origin_path'));
            } else {
                $last = end($patterns);
                if ($last == $suffix) {
                    break;
                }
            }
        }
        return redirect()->away($file['download']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function download(Request $request)
    {
        $graphPath = Tool::convertPath($request->getPathInfo(), true, true);
        $file = Cache::remember('one:' . $graphPath, $this->expires, function () use ($graphPath) {
            $response = $this->od->getItemByPath($graphPath);
            return $this->od->formatArray($response, false);
        });
        $url = $file['@microsoft.graph.downloadUrl'];
        return redirect()->away($url);
    }

    /**
     * @param $id
     * @param $size
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function thumb($id, $size)
    {
        $url = $this->od->thumbnails($id, $size);
        return redirect()->away($url);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function view(Request $request)
    {
        $graphPath = Tool::convertPath($request->getPathInfo(), true, true);
        $file = Cache::remember('one:' . $graphPath, $this->expires, function () use ($graphPath) {
            $response = $this->od->getItemByPath($graphPath);
            return $this->od->formatArray($response, false);
        });
        $download = $file['@microsoft.graph.downloadUrl'];
        return redirect()->away($download);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function search(Request $request)
    {
        $keywords = $request->get('keywords');
        if ($keywords) {
            $response = $this->od->search($this->root, $keywords);
            $response['value'] = $this->od->getNextLinkList($response, $response['value']);
            $origin_items = $this->od->formatArray($response);
            $items = Tool::filterFolder($origin_items); // 过滤结果中的文件夹
        } else {
            $items = [];
        }
        $list = [];
        foreach ($items as $item) {
            $path = $this->od->itemIdToPath($item['parentReference']['id']) . '/' . $item['name'];
            if (starts_with($path, $this->root)) {
                $path = str_after($path, $this->root);
            }
            $list[$item['name']] = array_add($item, 'path', $path);
        }
        $items = Tool::paginate($list, 20);
        return view('search', compact('items'));
    }

    /**
     * 处理加密目录
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handlePassword()
    {
        $password = request()->get('password');
        $origin_path = decrypt(request()->get('origin_path'));
        $pass_id = decrypt(request()->get('pass_id'));
        $data = [
            'password' => encrypt($password),
            'expires' => time() + $this->expires * 60, // 目录密码过期时间
        ];
        Session::put('password:' . $origin_path, $data);
        $file = $this->od->getItem($pass_id);
        // todo:密码处理
        $directory_password = Tool::getFileContent($file['']);
        if ($password == $directory_password)
            return redirect()->route('home', Tool::handleUrl($origin_path));
        else {
            Tool::showMessage('密码错误', false);
            return view('password', compact('origin_path', 'pass_id'));
        }
    }
}
