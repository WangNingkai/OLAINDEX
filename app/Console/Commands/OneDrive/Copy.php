<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\OneDrive;
use Illuminate\Console\Command;

class Copy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:cp
                            {origin : Origin Path}
                            {target : Target Path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy Item';

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
        $this->info('开始复制...');
        $this->call('od:refresh');
        $origin = $this->argument('origin');
        $_origin = OneDrive::pathToItemId($origin);
        $origin_id = $_origin['errno'] === 0 ? array_get($_origin, 'data.id')
            : exit('Origin Path Abnormal');
        $target = $this->argument('target');
        $_target = OneDrive::pathToItemId($target);
        $target_id = $_origin['errno'] === 0 ? array_get($_target, 'data.id')
            : exit('Target Path Abnormal');
        $response = OneDrive::copy($origin_id, $target_id);
        if ($response['errno'] === 0) {
            $redirect = array_get($response, 'data.redirect');
            $done = false;
            while (!$done) {
                $resp = OneDrive::request(
                    'get',
                    $redirect,
                    false
                );
                $status = array_get($resp, 'data.status');
                if ($status === 'failed') {
                    $this->error(array_get($resp, 'data.error.message'));
                    $done = true;
                } elseif ($status === 'inProgress') {
                    $this->info(
                        'Progress: '
                        . array_get($resp, 'data.percentageComplete')
                    );
                    sleep(3);
                    $done = false;
                } elseif ($status === 'completed') {
                    $this->info(
                        'Progress: '
                        . array_get($resp, 'data.percentageComplete')
                    );
                    $done = true;
                } elseif ($status === 'notStarted') {
                    $this->error('Status:' . $status);
                    $done = false;
                } else {
                    $this->error('Status:' . $status);
                    $done = true;
                }
            }
        } else {
            $this->warn("Failed!\n{$response['msg']} ");
        }
    }
}
