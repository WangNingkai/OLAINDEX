<?php

namespace App\Http\Controllers\Admin;

use App\Models\Image;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Http\Controllers\Controller;
use App\Models\OneDrive;

class UtilController extends Controller
{
    /**
     * 多图传图
     */
    public function storeImage(Request $request)
    {
        $data = $request->validate([
            'image' => 'required|image'
        ]);

        $file = request()->file('image');

        if (empty($file) || !$file->isValid()) {
            return $this->error(40401);
        }

        $image = (new ImageService($file->path()))->save();

        return $this->success([
            'id'   => $image->id,
            'path' => $image->path
        ]);
    }

    /**
     * 多图删图
     */
    public function destroyImage(Request $request)
    {
        $data = $request->validate([
            'image_ids'   => 'required|array',
            'image_ids.*' => 'integer'
        ]);

        $ids = array_unique($data['image_ids']);

        if (!empty($ids)) {
            $images = Image::whereIn('id', $ids)->where('admin_id', $this->user()->id)->get();

            foreach ($images as $image) {
                $image->delete();
            }
        }

        return $this->success();
    }

    public function list()
    {
        $onedrives = OneDrive::get();

        return $this->success($onedrives);
    }

    public function generateGoogle2fa()
    {
        $google2fa = app('pragmarx.google2fa');
        $secret = $google2fa->generateSecretKey();
        $admin = auth('admin')->user();
        $qrcode = $google2fa->getQRCodeInline(
            $admin->name,
            $admin->email,
            $secret
        );

        return view('default.admin.google2fa', compact('qrcode', 'secret'));
    }

    public function authGoogle2fa(Request $request)
    {
        $redirect = redirect()->route('admin.basic');

        if ($request->input('remember') == 'on') {
            $cookie_remember = cookie()->forever('remember_2fa', 1);
            $redirect = $redirect->cookie($cookie_remember);
        }

        return $redirect;
    }

    public function bindGoogle2fa(Request $request)
    {
        $data = $request->validate([
            'tfa_secret' => 'required|string|size:16',
            'code'       => 'required|string|size:6'
        ]);

        $admin = auth('admin')->user();

        if ($admin->is_tfa) {
            return redirect()->route('admin.basic')->withErrors(["{$admin->name} 已经绑定二步验证"]);
        }

        if (app('pragmarx.google2fa')->verifyKey($data['tfa_secret'], $data['code'])) {
            $admin->is_tfa = true;
            $admin->tfa_secret = $data['tfa_secret'];
            $admin->save();
        } else {
            return redirect()->back()->withErrors(["{$admin->name} 二步验证错误"]);
        }

        return redirectSuccess('admin.basic');
    }

    public function unbindGoogle2fa(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|size:6'
        ]);

        $admin = auth('admin')->user();

        if (!$admin->is_tfa) {
            return redirect()->route('admin.basic')->withErrors(["{$admin->name} 请先绑定二步验证"]);
        }

        if (app('pragmarx.google2fa')->verifyKey($admin->tfa_secret, $data['code'])) {
            $admin->is_tfa = false;
            $admin->tfa_secret = null;
            $admin->save();
        } else {
            return redirect()->back()->withErrors(["{$admin->name} 二步验证错误"]);
        }

        return redirectSuccess('admin.basic');
    }

    public function aria2c()
    {
        return view()->exists('ng') ? view('ng') : abort(404, '请先编译Aria2c');
    }
}
