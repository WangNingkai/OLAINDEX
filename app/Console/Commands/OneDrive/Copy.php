<?php

namespace App\Console\Commands\OneDrive;

use App\Service\OneDrive;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

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
        $_origin = OneDrive::getInstance(one_account())->pathToItemId($origin);
        $origin_id = $_origin['errno'] === 0 ? Arr::get($_origin, 'data.id')
            : exit('Origin Path Abnormal');
        $target = $this->argument('target');
        $_target = OneDrive::getInstance(one_account())->pathToItemId($target);
        $target_id = $_origin['errno'] === 0 ? Arr::get($_target, 'data.id')
            : exit('Target Path Abnormal');
        $response = OneDrive::getInstance(one_account())->copy($origin_id, $target_id);
        if ($response['errno'] === 0) {
            $redirect = Arr::get($response, 'data.redirect');
            $done = false;
            while (!$done) {
                $resp = OneDrive::getInstance(one_account())->request(
                    'get',
                    $redirect
                );
                $status = Arr::get($resp, 'data.status');
                if ($status === 'failed') {
                    $this->error(Arr::get($resp, 'data.error.message'));
                    $done = true;
                } elseif ($status === 'inProgress') {
                    $this->info(
                        'Progress: '
                        . Arr::get($resp, 'data.percentageComplete')
                    );
                    sleep(3);
                    $done = false;
                } elseif ($status === 'completed') {
                    $this->info(
                        'Progress: '
                        . Arr::get($resp, 'data.percentageComplete')
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
