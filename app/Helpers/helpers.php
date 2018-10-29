<?php

use App\Http\Controllers\FetchController;
use App\Helpers\Tool;

if (!function_exists('id2path')) {
    /**
     * @param $id
     * @return string
     */
    function id2path($id)
    {
        $fetch = new FetchController();
        $file = $fetch->getFileById($id);
        $path = $file['parentReference']['path'];
        $root = Tool::config('root', '/');
        if ($root == '/') {
            $key = mb_strpos($path, ':');
            $path = mb_substr($path, $key + 1);
            $pathArr = explode('/', $path);
            unset($pathArr[0]);
        } else {
            $path = mb_strstr($path, $root, false, 'utf8');
            $start = mb_strlen($root, 'utf8');
            $rest = mb_substr($path, $start, null, 'utf8');
            $pathArr = explode('/', $rest);
        }
        array_push($pathArr, $file['name']);
        return trim(implode('/', $pathArr), '/');
    }
}
