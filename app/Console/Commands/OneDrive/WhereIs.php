<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\OneDrive;

class WhereIs extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'od:whereis {id : Item ID}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Find The Item\'s Remote Path';

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
        $id = $this->argument('id');
        $response = OneDrive::itemIdToPath($id);
        if ($response['errno'] === 0) {
            $this->info(array_get($response, 'data.path'));
        } else {
            $this->error($response['msg']);
            exit;
        }
    }
}
