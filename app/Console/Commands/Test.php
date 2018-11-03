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
        if (!refresh_token()) {
            return 'refresh token error';
        }
        $od = new OneDriveController();
//        dd($od->download('01FGBPEHTYTDGCEWKSD5HJKOSMLC63SQPF'));
//        dd($od->PathToItemId('/share'));
//        dd($od->mkdir('mkdir',"01FGBPEHWFDKSJ6ZD4OFCYNVBTVSWSY6HS"));
//        dd($od->deleteItem("01FGBPEHSZ5ZN3EUNRYFHKIP3GSACJRXN4",'"{B25BEE59-B151-4EC1-A43F-66900498DDBC},1"'));
//        dd($od->itemIdToPath('01FGBPEHRICMFZYO4BEBGY257G43IFAXO5'));
//        dd($od->getItem('01FGBPEHRICMFZYO4BEBGY257G43IFAXO5'));
    }
}
