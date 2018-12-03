<?php

namespace App\Http\Controllers;

use App\Helpers\Tool;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use App\Helpers\OneDrive;

/**
 * OneDrive 索引
 * Class IndexController
 * @package App\Http\Controllers
 */
class IndexController extends Controller
{

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
        $this->middleware(['checkInstall', 'checkToken', 'handleIllegalFile']);
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
        $realPath = $request->route()->parameter('query') ?? '/';
        $graphPath = Tool::getRequestPath($realPath);
        $origin_path = rawurldecode(Tool::getRequestPath($realPath, false));
        $path_array = $origin_path ? explode('/', $origin_path) : [];
        $item = Cache::remember('one:file:' . $graphPath, $this->expires, function () use ($graphPath) {
            $result = OneDrive::getItemByPath($graphPath);
            $response = OneDrive::responseToArray($result);
            if ($response['code'] === 200) {
                return $response['data'];
            } else {
                return null;
            }
        });
        if (array_has($item, 'file')) {
            return redirect()->away($item['@microsoft.graph.downloadUrl']);
        }
        // 获取列表
        $origin_items = Cache::remember('one:list:' . $graphPath, $this->expires, function () use ($graphPath) {
            $result = OneDrive::getChildrenByPath($graphPath, '?select=id,name,size,lastModifiedDateTime,eTag,file,image,folder,@microsoft.graph.downloadUrl');
            $response = OneDrive::responseToArray($result);
            if ($response['code'] === 200) {
                return $response['data'];
            } else {
                Tool::showMessage($response['msg'], false);
                return [];
            }
        });
        $hasImage = Tool::hasImages($origin_items);
        // 过滤微软OneNote文件
        $origin_items = array_where($origin_items, function ($value) {
            return !array_has($value, 'package.type');
        });
        // 处理加密目录
        if (!empty($origin_items['.password'])) {
            $pass_id = $origin_items['.password']['id'];
            $pass_url = $origin_items['.password']['@microsoft.graph.downloadUrl'];
            $key = 'password:' . $origin_path;
            if (Session::has($key)) {
                $data = Session::get($key);
                $password = Tool::getFileContent($pass_url);
                if (strcmp($password, decrypt($data['password'])) !== 0 || time() > $data['expires']) {
                    Session::forget($key);
                    Tool::showMessage('密码已过期', false);
                    return view('password', compact('origin_path', 'pass_id'));
                }
            } else {
                return view('password', compact('origin_path', 'pass_id'));
            }
        }
        // 过滤受限隐藏目录
        if (!empty($origin_items['.deny'])) {
            if (!Session::has('LogInfo')) {
                Tool::showMessage('目录访问受限，仅管理员可以访问！', false);
                abort(403);
            }
        }
        // 处理 head/readme
        $head = array_key_exists('HEAD.md', $origin_items) ? Tool::markdown2Html(Tool::getFileContent($origin_items['HEAD.md']['@microsoft.graph.downloadUrl'])) : '';
        $readme = array_key_exists('README.md', $origin_items) ? Tool::markdown2Html(Tool::getFileContent($origin_items['README.md']['@microsoft.graph.downloadUrl'])) : '';
        if (!session()->has('LogInfo')) {
            $origin_items = array_except($origin_items, ['README.md', 'HEAD.md', '.password', '.deny']);
        }
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
        $realPath = $request->route()->parameter('query') ?? '/';
        $graphPath = Tool::getRequestPath($realPath, true, true);
        $origin_path = rawurldecode(Tool::getRequestPath($realPath, false));
        $path_array = $origin_path ? explode('/', $origin_path) : [];
        // 获取文件
        $file = Cache::remember('one:file:' . $graphPath, $this->expires, function () use ($graphPath) {
            $result = OneDrive::getItemByPath($graphPath);
            $response = OneDrive::responseToArray($result);
            if ($response['code'] === 200) {
                return $response['data'];
            } else {
                return null;
            }
        });
        if (!$file) {
            abort(404);
        }
        // 过滤文件夹
        if (array_has($file, 'folder')) {
            abort(403);
        }
        $file['download'] = $file['@microsoft.graph.downloadUrl'];
        foreach ($this->show as $key => $suffix) {
            if (in_array($file['ext'], $suffix)) {
                $view = 'show.' . $key;
                // 处理文本文件
                if (in_array($key, ['stream', 'code'])) {
                    if ($file['size'] > 5 * 1024 * 1024) {
                        Tool::showMessage('文件过大，请下载查看', false);
                        return redirect()->back();
                    } else {
                        $file['content'] = Tool::getFileContent($file['@microsoft.graph.downloadUrl']);
                    }
                }
                // 处理缩略图
                if (in_array($key, ['image', 'dash', 'video'])) {
                    $result = OneDrive::thumbnails($file['id'], 'large');
                    $response = OneDrive::responseToArray($result);
                    if ($response['code'] === 200) {
                        $file['thumb'] = $response['data']['url'];
                    } else {
                        $file['thumb'] = 'https://i.loli.net/2018/11/27/5bfcdf9f16a6c.jpg';
                    }
                }
                // dash视频流
                if ($key === 'dash') {
                    if (!strpos($file['@microsoft.graph.downloadUrl'], "sharepoint.com")) {
                        return redirect()->away($file['download']);
                    }
                    $file['dash'] = str_replace("thumbnail", "videomanifest", $file['thumb']) . "&part=index&format=dash&useScf=True&pretranscode=0&transcodeahead=0";
                }
                // 处理微软文档
                if ($key === 'doc') {
                    $url = "https://view.officeapps.live.com/op/view.aspx?src=" . urlencode($file['@microsoft.graph.downloadUrl']);
                    return redirect()->away($url);
                }
                return view($view, compact('file', 'path_array', 'origin_path'));
            } else {
                $last = end($this->show);
                if ($last === $suffix) {
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
        $realPath = $request->route()->parameter('query') ?? '/';
        $graphPath = Tool::getRequestPath($realPath, true, true);
        $file = Cache::remember('one:file:' . $graphPath, $this->expires, function () use ($graphPath) {
            $result = OneDrive::getItemByPath($graphPath);
            $response = OneDrive::responseToArray($result);
            if ($response['code'] === 200) {
                return $response['data'];
            } else {
                return null;
            }
        });
        if (array_has($file, 'folder')) {
            abort(403);
        }
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
        $result = OneDrive::thumbnails($id, $size);
        $response = OneDrive::responseToArray($result);
        if ($response['code'] === 200) {
            $url = $response['data']['url'];
        } else {
            $url = 'https://i.loli.net/2018/11/27/5bfcdf9f16a6c.jpg';
        }
        return redirect()->away($url);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function view(Request $request)
    {
        $realPath = $request->route()->parameter('query') ?? '/';
        $graphPath = Tool::getRequestPath($realPath, true, true);
        $file = Cache::remember('one:file:' . $graphPath, $this->expires, function () use ($graphPath) {
            $result = OneDrive::getItemByPath($graphPath);
            $response = OneDrive::responseToArray($result);
            if ($response['code'] === 200) {
                return $response['data'];
            } else {
                return null;
            }
        });
        if (array_has($file, 'folder')) {
            abort(403);
        }
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
            $path = Tool::getEncodeUrl($this->root);
            $result = OneDrive::search(empty($path) ? '/' : ":/{$path}:/", $keywords);
            $response = OneDrive::responseToArray($result);
            if ($response['code'] === 200) {
                // 过滤结果中的文件夹\过滤微软OneNote文件
                $items = array_where($response['data'], function ($value) {
                    return !array_has($value, 'folder') && !array_has($value, 'package.type');
                });
            } else {
                Tool::showMessage('搜索失败', true);
                $items = [];
            }
        } else {
            $items = [];
        }
        $items = Tool::paginate($items, 20);
        return view('search', compact('items'));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function searchShow($id)
    {
        $result = OneDrive::itemIdToPath($id, Tool::config('root'));
        /* @var $result JsonResponse */
        $response = OneDrive::responseToArray($result);
        if ($response['code'] === 200) {
            $originPath = $response['data']['path'];
            if (trim($this->root, '/') != '') {
                $path = str_after($originPath, $this->root);
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
            'expires' => time() + (int)$this->expires * 60, // 目录密码过期时间
        ];
        Session::put('password:' . $origin_path, $data);
        $result = OneDrive::getItem($pass_id);
        $response = OneDrive::responseToArray($result);
        if ($response['code'] === 200) {
            $directory_password = Tool::getFileContent($response['data']['@microsoft.graph.downloadUrl']);
        } else {
            Tool::showMessage('获取文件夹密码失败', false);
            $directory_password = '';
        }
        if (strcmp($password, $directory_password) === 0) {
            return redirect()->route('home', Tool::getEncodeUrl($origin_path));
        } else {
            Tool::showMessage('密码错误', false);
            return view('password', compact('origin_path', 'pass_id'));
        }
    }
}
