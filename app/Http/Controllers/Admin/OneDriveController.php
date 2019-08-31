<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\OneDrive;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;

class OneDriveController extends Controller
{
    public function __construct(OneDrive $model)
    {
        $this->model = $model;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $oneDrives = $this->model->where('admin_id', $this->user()->id)->exclude('settings')->get();

        return themeView('admin.onedrive.index', compact('oneDrives'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return themeView('admin.onedrive.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'root'     => 'required|string|max:255',
            'cover_id' => 'required|exists:images,id',
        ]);

        $this->user()->oneDrives()->create($data);

        return redirect()->route('admin.onedrive.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $oneDrive = $this->model->where('admin_id', $this->user()->id)->with('cover')->findOrFail($id);
        getDefaultOneDriveAccount($id);

        return themeView('admin.onedrive.edit', compact('oneDrive'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name'                        => 'sometimes|string|max:255',
            'root'                        => 'sometimes|string|max:255',
            'is_default'                  => 'sometimes|boolean',
            'expires'                     => 'sometimes|integer|min:1',
            'cover_id'                    => 'sometimes|exists:images,id',
            'settings'                    => 'array',
            'settings.image_hosting'      => 'in:enabled,disabled,admin_enabled',
            'settings.image_home'         => 'boolean',
            'settings.image_view'         => 'boolean',
            'settings.image_hosting_path' => 'string|max:255',
            'settings.image'              => 'string|max:255',
            'settings.video'              => 'string|max:255',
            'settings.dash'               => 'string|max:255',
            'settings.audio'              => 'string|max:255',
            'settings.doc'                => 'string|max:255',
            'settings.code'               => 'string|max:255',
            'settings.stream'             => 'string|max:255',
            'settings.encrypt_path'       => 'string|max:255',
            'settings.encrypt_option'     => 'array',
            'settings.encrypt_option.*'   => 'string|in:list,show,download,view',
        ]);

        $oneDrive = $this->model->where('admin_id', $this->user()->id)->with('cover')->findOrFail($id);

        if ($oneDrive->is_default && !Arr::get($data, 'is_default')) {
            return redirect()->back()->withErrors(['要更换默认OneDrive，请选择除当前之外的OneDrive设置为默认']);
        }

        $data['settings'] = array_merge(config('onedrive'), Arr::get($data, 'settings', config(config('onedrive'))));
        $oneDrive->update($data);

        return success();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $oneDrive = $this->model->where('admin_id', $this->user()->id)->findOrFail($id);
        $oneDrive->delete();
    }

    public function showBind($id)
    {
        $oneDrive = $this->model->where('admin_id', $this->user()->id)->findOrFail($id);

        if ($oneDrive->is_binded) {
            return redirect()->route('admin.onedrive.index')->withErrors(["{$oneDrive->name} 已经绑定"]);
        }

        return themeView('admin.onedrive.bind', compact('oneDrive'));
    }

    public function apply(Request $request, $id)
    {
        $data = $request->validate([
            'redirect_uri' => 'required|url'
        ]);

        $oneDrive = $this->model->where('admin_id', $this->user()->id)->findOrFail($id);

        if ($oneDrive->is_binded) {
            return redirect()->route('admin.onedrive.index')->withErrors(["{$oneDrive->name} 已经绑定"]);
        }

        $ru = 'https://developer.microsoft.com/en-us/graph/quick-start?appID=_appId_&appName=_appName_&redirectUrl='
            . $data['redirect_uri'] . '&platform=option-php';
        $deepLink = '/quickstart/graphIO?publicClientSupport=false&appName=OLAINDEX&redirectUrl='
            . $data['redirect_uri'] . '&allowImplicitFlow=false&ru='
            . urlencode($ru);
        $app_url = 'https://apps.dev.microsoft.com/?deepLink='
            . urlencode($deepLink);

        $oneDrive->update($data);

        return redirect()->away($app_url);
    }

    public function bind(Request $request, $id)
    {
        $data = $request->validate([
            'redirect_uri'  => 'required|url',
            'client_id'     => 'required|string',
            'client_secret' => 'required|string',
            'account_type'  => 'required|in:com,cn',
        ]);

        $oneDrive = $this->model->where('admin_id', $this->user()->id)->findOrFail($id);

        if ($oneDrive->is_binded) {
            return redirect()->route('admin.onedrive.index')->withErrors(["{$oneDrive->name} 已经绑定"]);
        }

        $data['is_configuraed'] = 1;
        $oneDrive->update($data);

        return redirect()->route('oauth', ['onedrive' => $oneDrive->id]);
    }

    public function unbind($id)
    {
        $oneDrive = $this->model->where('admin_id', $this->user()->id)->findOrFail($id);

        if (!$oneDrive->is_binded) {
            return redirect()->route('admin.onedrive.index')->withErrors(["{$oneDrive->name} 请先绑定"]);
        }

        $oneDrive->update([
            'is_binded' => 0
        ]);

        return success();
    }

    /**
     * 缓存清理
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clear($id)
    {
        $oneDrive = $this->model->where('admin_id', $this->user()->id)->findOrFail($id);

        if (!$oneDrive->is_binded) {
            return redirect()->route('admin.onedrive.index')->withErrors(["{$oneDrive->name} 请先绑定"]);
        }

        clearOnedriveCache($oneDrive->id);

        return success();
    }

    /**
     * 刷新缓存
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function refresh($id)
    {
        $oneDrive = $this->model->where('admin_id', $this->user()->id)->findOrFail($id);

        if (!$oneDrive->is_binded) {
            return redirect()->route('admin.onedrive.index')->withErrors(["{$oneDrive->name} 请先绑定"]);
        }

        Artisan::call('od:cache', [
            '--one_drive_id' => $oneDrive->id
        ]);

        return success();
    }
}
