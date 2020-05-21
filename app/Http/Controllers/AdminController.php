<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Http\Controllers;


use App\Models\AccessToken;
use App\Models\Account;
use App\Models\Setting;
use GuzzleHttp\Psr7\Stream;
use Illuminate\Http\Request;
use Microsoft\Graph\Exception\GraphException;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Http\GraphResponse;
use yii\helpers\Json;

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
        dd($this->_request($id));
    }

    public function _request($id, $query = '/me/drive')
    {
        $accessToken = (new AccessToken($id))->getAccessToken();
        // Create a Graph client
        $graph = new Graph();
        $graph->setAccessToken($accessToken);
        /* @var \Microsoft\Graph\Http\GraphResponse $onenote */
        try {
            /* @var $response GraphResponse|Stream */
            $response = $graph->createRequest('GET', $query)
                ->setReturnType(Stream::class)
                ->execute();
        } catch (GraphException $e) {
            return '';
        }
        $data = $response->getContents();

        $response = is_json($data) ? json_decode($data, true) : $data;
        return $response;
    }

}
