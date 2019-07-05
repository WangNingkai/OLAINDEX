<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\OneDrive;

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
            'name' => 'required|string|max:255',
            'root' => 'required|string|max:255',
        ]);

        $data['admin_id'] = $this->user()->id;
        $this->model->create($data);

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
        $oneDrive = $this->model->where('admin_id', $this->user()->id)->findOrFail($id);

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

        $oneDrive = $this->model->where('admin_id', $this->user()->id)->findOrFail($id);
        $oneDrive->update(array_merge(config('onedrive'), $data));

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
}
