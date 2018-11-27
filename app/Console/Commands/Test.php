<?php

namespace App\Console\Commands;

use App\Helpers\OneDrive;
use App\Helpers\Tool;
use Illuminate\Console\Command;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 't';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Command';

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
        Tool::refreshToken();
        $res1 = OneDrive::requestApi('get', '/me/drive/items/01FGBPEHWUSY5I7NIRV5CIAAPFVJFPEBP4');
//        $res2 = OneDrive::requestApi('get', '/me/drive/root:/share:/children?$expand=thumbnails&$top=2');
//        $res = OneDrive::requestApi('get', '/me/drive/root/children?$top=2');
//        dump(OneDrive::handleResponse($res));
        dd($res1);
    }
}
