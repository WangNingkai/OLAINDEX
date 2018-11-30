<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\OneDrive;
use Illuminate\Console\Command;

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
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        $this->call('refresh:token');
        $id = $this->argument('id');
        $response = OneDrive::responseToArray(OneDrive::itemIdToPath($id));
        if ($response['code'] === 200) {
            $this->info(array_get($response, 'data.path'));
        } else {
            $this->error($response['msg']);
            exit;
        }
    }
}
