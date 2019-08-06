<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\OneDrive;

class CreateFolder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:mkdir
                            {name : Folder Name}
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
     * @throws \ErrorException
     */
    public function handle()
    {
        if (!empty($this->option('one_drive_id'))) {
            $this->call('od:refresh', ['--one_drive_id' => $this->option('one_drive_id')]);
        } else {
            $this->call('od:refresh');
        }

        $name = $this->argument('name');
        $remote = $this->argument('remote');
        $response = OneDrive::mkdirByPath($name, $remote);
        $this->call('cache:clear');
        $response['errno'] === 0 ? $this->info('Folder Created!') : $this->warn("Failed!\n{$response['msg']} ");
    }
}
