<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\OneDrive;
use App\Helpers\Tool;
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
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        $this->info('开始复制...');
        $this->call('od:refresh');
        $origin = $this->argument('origin');
        $_origin
            = OneDrive::responseToArray(OneDrive::pathToItemId(OneDrive::getRequestPath($origin)));
        $origin_id = $_origin['code'] === 200 ? array_get($_origin, 'data.id')
            : exit('Origin Path Abnormal');
        $target = $this->argument('target');
        $_target
            = OneDrive::responseToArray(OneDrive::pathToItemId(OneDrive::getRequestPath($target)));
        $target_id = $_origin['code'] === 200 ? array_get($_target, 'data.id')
            : exit('Target Path Abnormal');
        $copy = OneDrive::copy($origin_id, $target_id);
        /* @var $copy \Illuminate\Http\JsonResponse */
        $response = OneDrive::responseToArray($copy);
        if ($response['code'] === 200) {
            $redirect = array_get($response, 'data.redirect');
            $done = false;
            while (!$done) {
                $resp = OneDrive::request(
                    'get',
                    $redirect,
                    '',
                    true
                )
                    ->getBody()->getContents();
                $result = OneDrive::responseToArray($resp);
                $status = array_get($result, 'status');
                if ($status === 'failed') {
                    $this->error(array_get($result, 'error.message'));
                    $done = true;
                } elseif ($status === 'inProgress') {
                    $this->info(
                        'Progress: '
                        .array_get($result, 'percentageComplete')
                    );
                    sleep(3);
                    $done = false;
                } elseif ($status === 'completed') {
                    $this->info(
                        'Progress: '
                        .array_get($result, 'percentageComplete')
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
