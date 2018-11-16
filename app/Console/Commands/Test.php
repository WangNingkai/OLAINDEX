<?php

namespace App\Console\Commands;

use App\Helpers\Tool;
use App\Http\Controllers\OneDriveController;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 't:t';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command Test';

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
        /*$items = Tool::fetchDir('app');
        $files = array_where($items,function ($value){
            return is_file($value);
        });
        dd($files);*/
        $od = new OneDriveController();
        $info = Tool::handleResponse($od->getMe());
        dd($info);
    }
}
