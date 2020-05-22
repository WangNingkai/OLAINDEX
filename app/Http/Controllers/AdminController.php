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
use Microsoft\Graph\Model\DriveItem;
use function GuzzleHttp\Psr7\parse_query;

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
        $accounts = Account::all();
        dd($accounts->toArray());

    }

    public function accountDetail($id)
    {
        $key = 'a:id:' . $id;
        if (!Cache::has($key)) {
            $resp = $this->_request($id);
            Cache::add($key, $resp, 300);
            $info = Cache::get($key);
        } else {
            $info = Cache::get($key);
        }

        dd($info);
    }

    public function test()
    {
        $resp = $this->_request(2, 'get', '/me/drive/root:/图片:/children', ['$top' => 20]);
        dd($resp->getBody());
        $nextLink = $resp->getNextLink();
        if ($nextLink) {
            $queryParams = parse_query(parse_url($nextLink)['query']);
        }
    }

    private function _request($id, $method = 'GET', $query = '', $options = [])
    {
        foreach ($options as $key => $value) {
            $query = Tool::buildQueryParams($query, $key, $value);
        }

        $req = new GraphClient($id);
        $req->setMethod($method)
            ->setQuery($query)
            ->setReturnStream(false);
        return $req->execute();
    }

}
