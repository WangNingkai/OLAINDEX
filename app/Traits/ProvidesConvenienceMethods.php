<?php

namespace App\Traits;

use Auth;
use Illuminate\Http\Resources\Json\JsonResource;
use stdClass;

trait ProvidesConvenienceMethods
{
    public function success($data = [], $code = 200, $params = [])
    {
        $result = [
            'code'        => $code,
            'message'     => $code == 200 ? 'OK' : trans('success.' . $code, $params),
            'server_time' => time(),
            'data'        => []
        ];
        if (is_object($data)) {
            if ($data instanceof JsonResource) {
                $result['data'] = $data->resolve();
                if (method_exists($data, 'getMeta')) {
                    $result['meta'] = $data->getMeta();
                }
            } else {
                $data = $data->toArray();
                $result['data'] = $data;
            }
        } elseif (is_null($data)) {
            $result['data'] = new stdClass;
        } else {
            $result['data'] = $data;
        }

        return response()->json($result, substr($code, 0, 3));
    }

    public function error($code = 400, $params = [], $msg = '')
    {
        return response()->json([
            'code'    => $code,
            'message' => !empty($msg) ? $msg : trans('errors.' . $code, $params)
        ], substr($code, 0, 3));
    }

    protected function user()
    {
        return Auth::user();
    }
}
