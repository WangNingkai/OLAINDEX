<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\OneDrive;
use Illuminate\Support\Arr;

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
     * @throws \ErrorException
     */
    public function handle()
    {
        $this->call(
            !empty($one_drive_id  = $this->option('one_drive_id')) 
                ? 'od:refresh --one_drive_id=' . $one_drive_id
                : 'od:refresh'
        );
        $this->info('Please waiting...');
        $remote = $this->argument('remote');
        $_remote = OneDrive::pathToItemId($remote);
        $remote_id = $_remote['errno'] === 0 ? Arr::get($_remote, 'data.id') : exit('Remote Path Abnormal');
        $response = OneDrive::createShareLink($remote_id);
        $response['errno'] === 0
            ? $this->info("Success! Direct Link:\n{$response['data']['redirect']}")
            : $this->warn("Failed!\n{$response['msg']} ");
    }
}
