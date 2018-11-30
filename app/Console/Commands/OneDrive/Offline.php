<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\OneDrive;
use Illuminate\Console\Command;

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
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        $this->call('refresh:token');
        $remote = $this->argument('remote');
        $url = $this->argument('url');
        $result = OneDrive::uploadUrl($remote, $url);
        $response = OneDrive::responseToArray($result);
        if ($response['code'] === 200) {
            $redirect = array_get($response, 'data.redirect');
            $this->info('progress link: ' . $redirect);
            $done = false;
            while (!$done) {
                $content = OneDrive::request('get', $redirect, '', true)->getBody()->getContents();
                /* @var $content \Illuminate\Http\JsonResponse */
                $result = OneDrive::responseToArray($content);
                $status = array_get($result, 'status');
                if ($status === 'failed') {
                    $this->error(array_get($result, 'error.message'));
                    $done = true;
                } elseif ($status === 'inProgress') {
                    $this->info('Progress: ' . array_get($result, 'percentageComplete'));
                    sleep(3);
                    $done = false;
                } elseif ($status === 'completed') {
                    $this->info('Progress: ' . array_get($result, 'percentageComplete'));
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
