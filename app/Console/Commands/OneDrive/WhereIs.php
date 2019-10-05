<?php

namespace App\Console\Commands\OneDrive;

use App\Service\OneDrive;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

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
        $this->call('od:refresh');
        $id = $this->argument('id');
        $response = OneDrive::getInstance(one_account())->itemIdToPath($id);
        if ($response['errno'] === 0) {
            $this->info(Arr::get($response, 'data.path'));
        } else {
            $this->error($response['msg']);
            exit;
        }
    }
}
