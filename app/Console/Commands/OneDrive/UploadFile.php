<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\Tool;
use App\Helpers\OneDrive;
use Illuminate\Console\Command;

class UploadFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:upload
                            {local : Local Path}
                            {remote : Remote Path}
                            {--chuck=5242880 : Chuck Size(byte) }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'UploadFile File';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        $this->call('od:refresh');
        $local = $this->argument('local');
        $remote = $this->argument('remote');
        $chuck = $this->option('chuck');
        $file_size = OneDrive::readFileSize($local);
        if ($file_size < 4194304) {
            return $this->upload($local, $remote);
        } else {
            return $this->uploadBySession($local, $remote, $chuck);
        }
    }

    /**
     * 普通文件上传
     * @param string $local 本地文件地址
     * @param string $remote 远程上传地址（包括文件名）
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function upload($local, $remote)
    {
        $content = file_get_contents($local);
        $file_name = basename($local);
        $graphPath = OneDrive::getRequestPath($remote . $file_name);
        $result = OneDrive::uploadByPath($graphPath, $content);
        $response = OneDrive::responseToArray($result);
        $response['code'] === 200 ? $this->info('Upload Success!') : $this->warn('Failed!');
    }

    /**
     * @param $local
     * @param $remote
     * @param int $chuck
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function uploadBySession($local, $remote, $chuck = 3276800)
    {
        ini_set('memory_limit', '-1');
        $file_size = OneDrive::readFileSize($local);
        $file_name = basename($local);
        $target_path = Tool::getAbsolutePath($remote);
        $path = trim($target_path, '/') === '' ? ":/{$file_name}:/" : OneDrive::getRequestPath($target_path . $file_name);
        $url_request = OneDrive::createUploadSession($path);
        $url_response = OneDrive::responseToArray($url_request);
        if ($url_response['code'] === 200) {
            $url = array_get($url_response, 'data.uploadUrl');
        } else {
            $this->warn($url_response['msg']);
            exit;
        }
        $this->info("File Path:\n{$local}");
        $this->info("Upload Url:\n{$url}");
        $done = false;
        $offset = 0;
        $length = $chuck;
        while (!$done) {
            $retry = 0;
            $res = OneDrive::uploadToSession($url, $local, $offset, $length);
            $response = OneDrive::responseToArray($res);
            if ($response['code'] === 200) {
                $data = $response['data'];
                if (!empty($data['nextExpectedRanges'])) {
                    $this->info("length: {$data['nextExpectedRanges'][0]}");
                    $ranges = explode('-', $data['nextExpectedRanges'][0]);
                    $offset = intval($ranges[0]);
                    $status = @floor($offset / $file_size * 100) . '%';
                    $this->info("success. progress:{$status}");
                    $done = false;
                } elseif (!empty($data['@content.downloadUrl']) || !empty($data['id'])) {
                    $this->info('Upload Success!');
                    $done = true;
                } else {
                    $retry++;
                    if ($retry <= 3) {
                        $this->warn("Retry{$retry}times，Please wait 10s...");
                        sleep(10);
                    } else {
                        $this->warn('Upload Failed!');
                        OneDrive::deleteUploadSession($url);
                        break;
                    }
                }
            } else {
                $this->warn('Upload Failed!');
                OneDrive::deleteUploadSession($url);
                break;
            }
        }
    }

}
