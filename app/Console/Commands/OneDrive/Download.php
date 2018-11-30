<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\OneDrive;
use App\Helpers\Tool;
use Illuminate\Console\Command;

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
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        $this->call('od:refresh');
        $remote = $this->argument('remote');
        $id = $this->option('id');
        if ($id) {
            $result = OneDrive::getItem($id);
        } else {
            if (empty($remote)) exit('Parameters Missing!');
            $graphPath = OneDrive::getRequestPath($remote);
            $result = OneDrive::getItemByPath($graphPath);
        }
        $response = OneDrive::responseToArray($result);
        if ($response['code'] === 200) {
            $download = $response['data']['@microsoft.graph.downloadUrl'];
            $this->info("Download Link:\n{$download}");
        } else  $this->warn("Failed!\n{$response['msg']} ");
    }
}
