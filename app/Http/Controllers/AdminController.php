<?php


namespace App\Http\Controllers;


use App\Models\Setting;
use Illuminate\Http\Request;

class AdminController extends BaseController
{
    public function config(Request $request)
    {
        if ($request->isMethod('get')) {
            return view(config('olaindex.theme') . 'admin.config');
        }
        $data = $request->except('_token');
        Setting::batchUpdate($data);
        $this->showMessage('保存成功！');

        return redirect()->back();
    }

}
