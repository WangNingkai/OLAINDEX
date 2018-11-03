<?php

namespace App\Console\Commands;

use App\Helpers\Tool;
use App\Http\Controllers\OneDriveController;
use App\Http\Controllers\GraphRequestController;
use Illuminate\Console\Command;
use Microsoft\Graph\Model\DriveItem;

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
    }
}
