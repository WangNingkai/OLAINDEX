<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
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
        info('start create task');
        $gid = $this->option('gid');
        $path = $this->option('path');
        if (empty($gid) || empty($path)) {
            info('上传缺少参数', [
                'gid'  => $gid,
                'path' => $path
            ]);
            return;
        }

        getDefaultOneDriveAccount();
        $data = [
            'gid'         => $gid,
            'type'        => is_file($path) ? 'file' : 'folder',
            'source'      => $path,
            'target'      => '/upload/',
            'onedrive_id' => app('onedrive')->id,
        ];

        $result = explode('@@', $path);
        foreach ($result as $item) {
            $match = [];

            if (preg_match('/(odid|path)=([\S]+)/', $item, $match)) {
                if ($match[1] == 'path') {
                    if ($match[2]) {
                        $target_path = str_replace('\\', '/', $match[2]);
                    }

                    $data['target'] = $target_path;
                } else {
                    if ($match[2]) {
                        $onedrive_id = $match[2];
                    }

                    $data['onedrive_id'] = $onedrive_id;
                }
            }
        }
        info('task data', $data);
        info('onedrive', collect(app('onedrive'))->toArray());
        $task = Task::create($data);
        info('end create task');
        info('push queue');
        dispatch(new OneDriveUpload($task));
        info('push end queue');
    }
}
