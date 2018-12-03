<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\OneDrive;
use App\Helpers\Tool;
use Illuminate\Console\Command;

class CreateFolder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:mkdir
                            {name : Floder Name}
                            {remote : Remote Path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create New Folder';

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
        $name = $this->argument('name');
        $remote = $this->argument('remote');
        $graphPath = OneDrive::getRequestPath($remote);
        $result = OneDrive::mkdirByPath($name, $graphPath);
        $response = OneDrive::responseToArray($result);
        $this->call('cache:clear');
        $response['code'] === 200 ? $this->info("Folder Created!")
            : $this->warn("Failed!\n{$response['msg']} ");
    }
}
