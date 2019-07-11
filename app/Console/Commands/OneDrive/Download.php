<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\OneDrive;

class Download extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:download
                            {remote? : Download Remote Path}
                            {--id= : Download Remote File ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download File';

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
        $remote = $this->argument('remote');
        $id = $this->option('id');
        if ($id) {
            $response = OneDrive::getItem($id);
        } else {
            if (empty($remote)) {
                exit('Parameters Missing!');
            }
            $response = OneDrive::getItemByPath($remote);
        }
        if ($response['errno'] === 0) {
            $download = $response['data']['@microsoft.graph.downloadUrl'] ??
                exit('404 NOT FOUND');
            $this->info("Download Link:\n{$download}");
        } else {
            $this->warn("Failed!\n{$response['msg']} ");
        }
    }
}
