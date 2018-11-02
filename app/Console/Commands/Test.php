<?php

namespace App\Console\Commands;

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

    /**
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        if (!refresh_token()) {
            return 'refresh token error';
        }
        $od = new OneDriveController();
        $res = $od->listChildren();
        dd($res);
    }
}
