<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use Illuminate\Support\Arr;
use App\Jobs\OneDriveUpload;

class Aria2cToOnedriveUpload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aria2c:upload_to_onedrive
                                {--gid=}
                                {--path=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '将aria2c下载的文件上传到onedrive';

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // TODO: job
        $gid = $this->option('gid');
        $path = $this->option('path');
        if (empty($gid) || empty($path)) {
            // 错误记录
            return;
        }

        $data = [
            'type'   => is_file($path) ? 'file' : 'folder',
            'source' => $path,
        ];

        $result = explode('@@', $path);
        foreach ($result as $item) {
            $match = [];

            if (preg_match('/(odid|path)=([\S]+)/', $item, $match)) {
                if ($match[1] == 'path') {
                    $target_path = 'upload/';
                    if ($match[2]) {
                        $target_path = str_replace('\\', '/', $match[2]);
                    }
                    Arr::set($data, 'target', $target_path);
                } else {
                    $onedrive_id = $match[2];
                    if ($match[2]) {
                        getDefaultOneDriveAccount();
                        $onedrive_id = app('onedrive')->id;
                    }

                    Arr::set($data, 'onedrive_id', $onedrive_id);
                }
            }
        }

        $task = Task::create($data);

        dispatch(new OneDriveUpload($task));
    }
}
