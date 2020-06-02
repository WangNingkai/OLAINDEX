<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Http\Controllers;

use App\Helpers\Tool;
use App\Service\GraphClient;

class DiskController extends BaseController
{
    public function list()
    {
    }

    public function view()
    {
    }

    private function _request($id, $method = 'GET', $query = '/me/drive/root/children', $options = [])
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
