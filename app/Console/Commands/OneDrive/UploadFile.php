<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\Tool;
use App\Helpers\OneDrive;
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
                            {--folder : Upload File Folder}
                            {--archive : Archive File}
                            {--chuck=104857600 : Chuck Size(byte)}';
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
        $folder = $this->option('folder');
        $archive = !empty($this->option('archive')) ? true : false;

        $local = OneDrive::compressedFile($local, $archive);
        if (!$local) {
            return $this->error('file not found!');
        }

        if (!empty($folder)) {
            $this->uploadFolder($local, $remote, $chuck);
        } else {
            $this->uploadFile($local, $remote, $chuck);
        }
    }

    public function uploadFile($local, $remote, $chuck)
    {
        $file_size = OneDrive::readFileSize($local);
        if ($file_size < 4194304) {
            return $this->upload($local, $remote);
        } else {
            return $this->uploadBySession($local, $remote, $chuck);
        }
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
        $response = OneDrive::uploadByPath($remote . $file_name, $content);

        if ($response['errno'] === 0) {
            $this->info('Upload Success!');
            @unlink($local);
        } else {
            $this->warn('Failed!');
        }
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
        $file_size = OneDrive::readFileSize($local);
        $file_name = basename($local);
        $target_path = Tool::getAbsolutePath($remote);
        $url_response = OneDrive::createUploadSession($target_path . $file_name);
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
            $response = OneDrive::uploadToSession(
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
                    $offset = intval($ranges[0]);
                    $status = @floor($offset / $file_size * 100) . '%';
                    $this->info("success. progress:{$status}");
                    $done = false;
                } elseif (!empty($data['@content.downloadUrl'])
                    || !empty($data['id'])
                ) {
                    $this->info('Upload Success!');
                    $done = true;
                    @unlink($local);
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

    /**
     * upload file folder
     *
     * @param     $local
     * @param     $remote
     * @param int $chunk
     * @return void
     */
    public function uploadFolder($local, $remote = '/', $chunk)
    {
        $local = realpath($local);
        $remote = $this->getAbsolutePath($remote);
        $this->folderToUpload($local, $remote, $chunk);
    }

    /**
     * Recursively get the file path
     *
     * @param     $local
     * @param     $remote
     * @param int $chunk
     * @return void
     */
    public function folderToUpload($local, $remote, $chunk)
    {
        $files = scandir($local);

        foreach ($files as $file) {
            if (in_array($file, ['.', '..', '.DS_Store'])) {
                continue;
            }

            if (is_dir($local . '/' . $file)) {
                $this->folderToUpload($local . '/' . $file, $remote . $file . '/', $chunk);
            } else {
                $localfile = realpath($local . '/' . $file);
                $this->uploadFile($localfile, $remote, $chunk);
            }
        }
    }

    /**
     * get file absolute path
     *
     * @param [type] $path
     * @return void
     */
    public function getAbsolutePath($path)
    {
        $path = str_replace(['/', '\\', '//'], '/', $path);
        $parts = array_filter(explode('/', $path), 'strlen');
        $absolutes = [];

        foreach ($parts as $part) {
            if ('.' == $part) {
                continue;
            }

            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }

        return str_replace('//', '/', '/' . implode('/', $absolutes) . '/');
    }
}
