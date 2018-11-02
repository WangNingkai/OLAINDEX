<?php

namespace App\Http\Controllers;

class UploadController extends Controller
{
    public function upload($path, $content)
    {
        $path = trim($path, '/');
        $stream = \GuzzleHttp\Psr7\stream_for($content);
        $endpoint = "/me/drive/root:/{$path}:/content";
        $requestBody = $stream;
        $graph = new RequestController();
        $response = $graph->requestGraph('put', [$endpoint, $requestBody, []], true);
        return $response;
    }

    public function createUploadSession($path)
    {
        $id = path2id($path);
        $endpoint = "/me/drive/items/{$id}/createUploadSession";
        $requestBody = json_encode([
            'item' => [
                '@microsoft.graph.conflictBehavior' => 'rename',
            ]
        ]);
        $graph = new RequestController();
        $response = $graph->requestGraph('post', [$endpoint, $requestBody, []]);
        return $response;
    }

    public function UploadToSession($url, $file, $offset, $length = 10240)
    {
        $file_size = $this->ReadFileSize($file);
        $content_length = (($offset + $length) > $file_size) ? ($file_size - $offset) : $length;
        $end = $offset + $content_length - 1;
        $content = $this->ReadFileContent($file, $offset, $length);
        $headers = [
            'Content-Length' => $content_length,
            'Content-Content-Range' => "bytes {$offset}-{$end}/{$file_size}",
        ];
        $requestBody = $content;
        $graph = new RequestController();
        $response = $graph->requestGraph('put', [$url, $requestBody, $headers]);
        return $response;
    }

    public function UploadSessionStatus($url)
    {
        $graph = new RequestController();
        $response = $graph->requestGraph('get', $url);
        return $response;
    }

    public function DeleteUploadSession($url)
    {
        $graph = new RequestController();
        $response = $graph->requestGraph('delete', $url);
        return $response;
    }

    /**
     * 读取文件大小
     * @param $path
     * @return bool|int|string
     */
    public function ReadFileSize($path)
    {
        if (!file_exists($path))
            return false;
        $size = filesize($path);
        if (!($file = fopen($path, 'rb')))
            return false;
        if ($size >= 0) { //Check if it really is a small file (< 2 GB)
            if (fseek($file, 0, SEEK_END) === 0) { //It really is a small file
                fclose($file);
                return $size;
            }
        }
        //Quickly jump the first 2 GB with fseek. After that fseek is not working on 32 bit php (it uses int internally)
        $size = PHP_INT_MAX - 1;
        if (fseek($file, PHP_INT_MAX - 1) !== 0) {
            fclose($file);
            return false;
        }
        $length = 1024 * 1024;
        while (!feof($file)) { //Read the file until end
            $read = fread($file, $length);
            $size = bcadd($size, $length);
        }
        $size = bcsub($size, $length);
        $size = bcadd($size, strlen($read));
        fclose($file);
        return $size;
    }

    /**
     * 读取文件内容
     * @param $file
     * @param $offset
     * @param $length
     * @return bool|string
     */
    public function ReadFileContent($file, $offset, $length)
    {
        $handler = fopen($file, "rb") ?? die('获取文件内容失败');
        fseek($handler, $offset);
        return fread($handler, $length);
    }
}
