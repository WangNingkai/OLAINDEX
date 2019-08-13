<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Constants;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;

/**
 * 后台管理操作
 * Class AdminController
 *
 * @package App\Http\Controllers
 */
class AdminController extends Controller
{
    public function showBasic()
    {
        $admin = auth('admin')->user();

        return view(config('olaindex.theme') . 'admin.basic', compact('admin'));
    }

    /**
     * 基础设置
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function basic(Request $request)
    {
        $data = $request->validate([
            'name'               => 'sometimes|nullable|string',
            'theme'              => 'sometimes|nullable|string|in:' . implode(',', Constants::SITE_THEME),
            'hotlink_protection' => 'sometimes|nullable|string',
            'copyright'          => 'sometimes|nullable|string',
            'statistics'         => 'sometimes|nullable|string',
        ]);

        $admin = auth('admin')->user();
        $data = array_map(function (&$item) {
            return is_null($item) ? $item = '' : $item;
        }, $data);

        $admin->update($data);

        return success();
    }

    /**
     * 密码设置
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function profile(Request $request)
    {
        $data = $request->validate([
            'old_password'     => 'required|string',
            'password'         => 'required|string|different:old_password',
            'password_confirm' => 'required|string|same:password',
        ]);

        $admin = auth('admin')->user();
        $admin->update(Arr::only($data, 'password'));

        return success();
    }
}
