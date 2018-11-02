<?php

namespace App\Console\Commands;

use App\Helpers\Tool;
use App\Http\Controllers\OauthController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\UploadController;
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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!refresh_token()) {
            return;
        }
        $request = new RequestController();
        $res = $request->requestGraph('get', '/me/drive');
        $quota = $res['quota'];
        foreach ($quota as $key => $item) {
            $quota[$key] = Tool::convertSize($item);
        }
        dd($quota);
    }
}
