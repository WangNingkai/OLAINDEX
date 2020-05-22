<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Http\Controllers;


use App\Helpers\Tool;
use App\Models\Account;
use App\Models\Setting;
use App\Service\GraphClient;
use Illuminate\Http\Request;
use Cache;

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

    public function account()
    {
        $accounts = Account::all(['id', 'accountType', 'remark', 'status']);
        return response()->json($accounts->toArray());

    }

    public function accountDetail($id)
    {
        $key = 'ac:id:' . $id;
        if (!Cache::has($key)) {
            $resp = $this->_request($id, 'GET', '/me/drive');
            $data = $resp->getBody();
            Cache::add($key, $data, 300);
            $info = Cache::get($key);
        } else {
            $info = Cache::get($key);
        }
        return response()->json($info);
    }

    private function _request($id, $method = 'GET', $query = '', $options = [])
    {
        $query .= '?' . build_query($options, false);

        $req = new GraphClient($id);
        $req->setMethod($method)
            ->setQuery($query)
            ->setReturnStream(false);
        return $req->execute();
    }

}
