<?php

namespace App\Console\Commands;

use App\Helpers\Tool;
use App\Http\Controllers\OneDriveController;
use Illuminate\Console\Command;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:app';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Console Test';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        if (!refresh_token()) {
            echo 'refresh token error';
        }
//        $od = new OneDriveController();
//        $res = $od->requestApi('get', '/me/drive');
//        $res = $od->handleResponse($res);
//        dd(quota());
//        dd(json_decode($res->getBody()->getContents(), true));
//        $response = $od->readFileSize('D:/Downloads/Document/book.pdf');
//        $res = Tool::handleResponse($response);
//        $response = $od->readFileContent('D:/Downloads/Document/book.pdf', 0, 10240);
//        dd($response);
//        dd(Tool::handleResponse($res));
        $this->upload();
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function upload()
    {
        $od = new OneDriveController();
        $path = Tool::convertPath('book.pdf');
        $url_request = $od->createUploadSession($path);
        $url_response = Tool::handleResponse($url_request);
        if ($url_response['code'] == 200) {
            $url = $url_response['data']['uploadUrl'];
            $expires = date('Y-m-d H:i:s', strtotime($url_response['data']['expirationDateTime']));
        } else {
            echo '创建session失败';
            die;
        }
        $file = 'D:/Downloads/Document/book.pdf';
        echo '上传地址：' . $url . PHP_EOL;
        echo '上传文件：' . $file . PHP_EOL;
        echo '超时时间：' . $expires . PHP_EOL;
        $done = false;
        $offset = 0;
        $length = 327680 * 10;
        $file_size = $od->readFileSize($file);
        $begin_time = microtime(true);
        set_time_limit(0);
        while (!$done) {
            $res = $od->uploadToSession($url, $file, $offset, $length);
            $response = Tool::handleResponse($res);
            if ($response['code'] == 200) {
                $data = $response['data'];
                if (!empty($data['nextExpectedRanges'])) {
                    $upload_time = microtime(true) - $begin_time;
                    $speed = Tool::convertSize($length / $upload_time) . '/s';
                    $status = @floor($offset / $file_size * 100) . '%';
                    $length = intval($length / $upload_time / 32768 * 2) * 327680;
                    $length = ($length > 104857600) ? 104857600 : $length;
                    $ranges = explode('-', $data['nextExpectedRanges'][0]);
                    $offset = intval($ranges[0]);
                    $done = false;
                    echo '分片上传成功 上传速度： ' . $speed . ' 上传进度： ' . $status . PHP_EOL;
                } elseif (!empty($data['@content.downloadUrl']) || !empty($data['id'])) {
                    //上传完成
                    $upload_time = microtime(true) - $begin_time;
                    $speed = Tool::convertSize($length / $upload_time) . '/s';
                    $status = @floor($offset / $file_size * 100) . '%';
                    $done = true;
                    echo '文件上传成功 上传速度： ' . $speed . ' 上传进度： ' . $status . PHP_EOL;
                } else {
                    $done = false;
                    echo '文件上传失败' . PHP_EOL;
                }
            } else {
                echo '分片上传失败' . PHP_EOL;
                break;
            }
        }
    }
}
