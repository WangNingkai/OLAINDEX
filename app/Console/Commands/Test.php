<?php

namespace App\Console\Commands;

use App\Helpers\Tool;
use App\Http\Controllers\OneDriveController;
use Illuminate\Console\Command;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:app';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Console Test';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        if (!refresh_token()) {
            echo 'refresh token error';
        }
        /*$od = new OneDriveController();
        $res = $od->requestApi('get', '/me/drive');
        $response = $od->handleResponse($res);
        dd(Tool::handleResponse($response));*/
    }
}
