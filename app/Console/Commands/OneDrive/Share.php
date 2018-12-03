<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\Tool;
use App\Helpers\OneDrive;
use Illuminate\Console\Command;

class Share extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:share {remote : Remote Path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ShareLink For File';

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
        $this->call('od:refresh');
        $this->info('Please waiting...');
        $remote = $this->argument('remote');
        $_remote
            = OneDrive::responseToArray(OneDrive::pathToItemId(OneDrive::getRequestPath($remote)));
        $remote_id = $_remote['code'] === 200 ? array_get($_remote, 'data.id')
            : exit('Remote Path Abnormal');
        $share = OneDrive::createShareLink($remote_id);
        $response = OneDrive::responseToArray($share);
        if ($response['code'] === 200) {
            $direct = str_replace('15/download.aspx', '15/guestaccess.aspx',
                $response['data']['redirect']);
            $this->info("Success! Share Link:\n{$direct}");
        } else {
            $this->warn("Failed!\n{$response['msg']}");
        }
    }
}
