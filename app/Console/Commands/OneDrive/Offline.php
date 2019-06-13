<?php

namespace App\Console\Commands\OneDrive;

use App\Service\OneDrive;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class Offline extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:offline {remote : Remote Path}
                                    {url : Offline Url}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remote download links to your drive';

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
        $this->call('refresh:token');
        $remote = $this->argument('remote');
        $url = $this->argument('url');
        $response = OneDrive::getInstance(one_account())->uploadUrl($remote, $url);
        if ($response['errno'] === 200) {
            $redirect = Arr::get($response, 'data.redirect');
            $this->info('progress link: '.$redirect);
            $done = false;
            while (!$done) {
                $result = OneDrive::getInstance(one_account())->request('get', $redirect, false);
                $status = Arr::get($result, 'data.status');
                if ($status === 'failed') {
                    $this->error(Arr::get($result, 'data.error.message'));
                    $done = true;
                } elseif ($status === 'inProgress') {
                    $this->info(
                        'Progress: '
                        .Arr::get($result, 'data.percentageComplete')
                    );
                    sleep(3);
                    $done = false;
                } elseif ($status === 'completed') {
                    $this->info(
                        'Progress: '
                        .Arr::get($result, 'data.percentageComplete')
                    );
                    $done = true;
                } elseif ($status === 'notStarted') {
                    $this->error('Status:'.$status);
                    $done = false;
                } else {
                    $this->error('Status:'.$status);
                    $done = true;
                }
            }
        } else {
            $this->warn("Failed!\n{$response['msg']} ");
        }
    }
}
