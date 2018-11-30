<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\OneDrive;
use App\Helpers\Tool;
use Illuminate\Console\Command;

class Direct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:direct {remote : RemotePath}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Direct Share Link';

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
        $_remote = OneDrive::responseToArray(OneDrive::pathToItemId(OneDrive::getRequestPath($remote)));
        $remote_id = $_remote['code'] === 200 ? array_get($_remote, 'data.id') : exit('Remote Path Abnormal');
        $share = OneDrive::createShareLink($remote_id);
        $response = OneDrive::responseToArray($share);
        $response['code'] === 200 ? $this->info("Success! Direct Link:\n{$response['data']['redirect']}") : $this->warn("Failed!\n{$response['msg']} ");
    }
}
