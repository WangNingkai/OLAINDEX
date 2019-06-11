<?php

namespace App\Console\Commands\OneDrive;

use App\Utils\Tool;
use App\Service\OneDrive;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

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
     * @throws \ErrorException
     */
    public function handle()
    {
        $this->call('od:refresh');
        $local = $this->argument('local');
        $remote = $this->argument('remote');
        $chuck = $this->option('chuck');
        $file_size = OneDrive::getInstance(one_account())->readFileSize($local);
        if ($file_size < 4194304) {
            return $this->upload($local, $remote);
        }
        return $this->uploadBySession($local, $remote, $chuck);
    }

    /**
     * @param $local
     * @param $remote
     *
     * @throws \ErrorException
     */
    public function upload($local, $remote)
    {
        $content = file_get_contents($local);
        $file_name = basename($local);
        $response = OneDrive::getInstance(one_account())->uploadByPath($remote . $file_name, $content);
        $response['errno'] === 0 ? $this->info('Upload Success!')
            : $this->warn('Failed!');
    }

    /**
     * @param     $local
     * @param     $remote
     * @param int $chuck
     *
     * @throws \ErrorException
     */
    public function uploadBySession($local, $remote, $chuck = 3276800)
    {
        ini_set('memory_limit', '-1');
        $file_size = OneDrive::getInstance(one_account())->readFileSize($local);
        $file_name = basename($local);
        $target_path = Tool::getAbsolutePath($remote);
        $url_response = OneDrive::getInstance(one_account())->createUploadSession($target_path . $file_name);
        if ($url_response['errno'] === 0) {
            $url = Arr::get($url_response, 'data.uploadUrl');
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
            $response = OneDrive::getInstance(one_account())->uploadToSession(
                $url,
                $local,
                $offset,
                $length
            );
            if ($response['errno'] === 0) {
                $data = $response['data'];
                if (!empty($data['nextExpectedRanges'])) {
                    $this->info("length: {$data['nextExpectedRanges'][0]}");
                    $ranges = explode('-', $data['nextExpectedRanges'][0]);
                    $offset = (int)$ranges[0];
                    $status = @floor($offset / $file_size * 100) . '%';
                    $this->info("success. progress:{$status}");
                    $done = false;
                } elseif (!empty($data['@content.downloadUrl'])
                    || !empty($data['id'])
                ) {
                    $this->info('Upload Success!');
                    $done = true;
                } else {
                    $retry++;
                    if ($retry <= 3) {
                        $this->warn("Retry{$retry}times，Please wait 10s...");
                        sleep(10);
                    } else {
                        $this->warn('Upload Failed!');
                        OneDrive::getInstance(one_account())->deleteUploadSession($url);
                        break;
                    }
                }
            } else {
                $this->warn('Upload Failed!');
                OneDrive::getInstance(one_account())->deleteUploadSession($url);
                break;
            }
        }
    }
}
