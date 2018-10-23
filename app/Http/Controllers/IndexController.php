<?php

namespace App\Http\Controllers;

use App\Helpers\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * OneDrive 索引
 * Class IndexController
 * @package App\Http\Controllers
 */
class IndexController extends Controller
{
    /**
     * @var FetchController
     */
    public $fetch;

    /**
     * IndexController constructor.
     */
    public function __construct()
    {
        $this->middleware('checkToken');
        $fetch = new FetchController();
        $this->fetch = $fetch;
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
        $graphPath = $this->fetch->convertPath($request->getPathInfo());
        $origin_path = $this->fetch->convertPath($request->getPathInfo(), false);
        $query = 'children';
        $endpoint = '/me/drive/root' . $graphPath . $query;
        $response = $this->fetch->requestGraph($endpoint, true);
        $origin_items = $this->fetch->formatArray($response);
        if (!empty($origin_items['.password'])) {
            $pass_id = $origin_items['.password']['id'];
            $pass_url = $origin_items['.password']['@microsoft.graph.downloadUrl'];
            if (Session::has('password:' . $origin_path)) {
                $data = Session::get('password:' . $origin_path);
                $expires = $data['expires'];
                $password = $this->fetch->getContent($pass_url);
                if ($password != decrypt($data['password']) || time() > $expires) {
                    Session::forget('password:' . $origin_path);
                    Tool::showMessage('密码已过期', false);
                    return view('password', compact('origin_path', 'pass_id'));
                }
            } else return view('password', compact('origin_path', 'pass_id'));
        }
        $this->fetch->filterForbidFolder($origin_items);
        $head = Tool::markdown2Html($this->fetch->getContentByName('HEAD.md', $origin_items));
        $readme = Tool::markdown2Html($this->fetch->getContentByName('README.md', $origin_items));
        $path_array = $origin_path ? explode('/', $origin_path) : [];
        if (!session()->has('LogInfo')) $origin_items = $this->fetch->filterFiles($origin_items, ['README.md', 'HEAD.md', '.password', '.deny']);
        $items = Tool::arrayPage($origin_items, '/home/' . $origin_path, 20);
        return view('one', compact('items', 'origin_items', 'origin_path', 'path_array', 'head', 'readme'));
    }

    /**
     * 展示
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function show(Request $request)
    {
        $origin_path = $this->fetch->convertPath($request->getPathInfo(), false);
        $path_array = $origin_path ? explode('/', $origin_path) : [];
        $file = $this->fetch->getFile($request);
        if (isset($file['folder'])) abort(403);
        $file['download'] = $file['@microsoft.graph.downloadUrl'];
        $patterns = $this->fetch->show;
        foreach ($patterns as $key => $suffix) {
            if (in_array($file['ext'], $suffix)) {
                $view = 'show.' . $key;
                if (in_array($key, ['stream', 'code'])) {
                    if ($file['size'] > 5 * 1024 * 1024) {
                        Tool::showMessage('文件过大，请下载查看', false);
                        return redirect()->back();
                    } else $file['content'] = $this->fetch->requestHttp('get', $file['@microsoft.graph.downloadUrl']);
                }
                if (in_array($key, ['image', 'dash', 'video'])) {
                    $file['thumb'] = $this->fetch->getThumbUrl($file['id'], false);
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
     * 下载
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function download(Request $request)
    {
        $file = $this->fetch->getFile($request, true);
        $download = $file['@microsoft.graph.downloadUrl'];
        return redirect()->away($download);
    }

    /**
     * 图片预览
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function view(Request $request)
    {
        $file = $this->fetch->getFile($request, false);
        $download = $file['@microsoft.graph.downloadUrl'];
        return redirect()->away($download);
    }

    /**
     * 搜索
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function search(Request $request)
    {
        $keywords = $request->get('keywords');
        if ($keywords) {
            $query = "search(q='{$keywords}')";
            if ($this->fetch->root == '/')
                $endpoint = '/me/drive/root/' . $query;
            else
                $endpoint = '/me/drive/root:/' . trim($this->fetch->root, '/') . ':/' . $query;
            $response = $this->fetch->requestGraph($endpoint, true, false);
            $response['value'] = $this->fetch->getNextLinkList($response, $response['value']);
            $origin_items = $this->fetch->formatArray($response);
            $items = $this->fetch->filterFolder($origin_items); // 过滤结果中的文件夹
        } else {
            $items = [];
        }
        $items = Tool::arrayPage($items, '/search', 20);
        return view('search', compact('items'));
    }

    /**
     * 校验目录密码
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
            'expires' => time() + $this->fetch->expires * 60, // 目录密码过期时间
        ];
        Session::put('password:' . $origin_path, $data);
        $directory_password = $this->fetch->getContentById($pass_id);
        if ($password == $directory_password)
            return redirect()->route('home', $origin_path);
        else {
            Tool::showMessage('密码错误', false);
            return view('password', compact('origin_path', 'pass_id'));
        }
    }
}
