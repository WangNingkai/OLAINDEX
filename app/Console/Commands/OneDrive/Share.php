<?php

namespace App\Console\Commands\OneDrive;

use App\Service\OneDrive;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

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
     * @throws \ErrorException
     */
    public function handle()
    {
        $this->call('od:refresh');
        $this->info('Please waiting...');
        $remote = $this->argument('remote');
        $_remote
            = OneDrive::getInstance(one_account())->pathToItemId($remote);
        $remote_id = $_remote['errno'] === 0 ? Arr::get($_remote, 'data.id')
            : exit('Remote Path Abnormal');
        $response = OneDrive::getInstance(one_account())->createShareLink($remote_id);
        if ($response['errno'] === 0) {
            $direct = str_replace(
                '15/download.aspx',
                '15/guestaccess.aspx',
                $response['data']['redirect']
            );
            $this->info("Success! Share Link:\n{$direct}");
        } else {
            $this->warn("Failed!\n{$response['msg']}");
        }
    }
}
