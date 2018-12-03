<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\Tool;
use App\Helpers\OneDrive;
use Illuminate\Console\Command;

class Move extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:mv
                            {origin : Origin Path}
                            {target : Target Path}
                            {--rename= : Rename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move Item';

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
        $this->info('开始移动...');
        $this->info('Please waiting...');
        $origin = $this->argument('origin');
        $_origin
            = OneDrive::responseToArray(OneDrive::pathToItemId(OneDrive::getRequestPath($origin)));
        $origin_id = $_origin['code'] === 200 ? array_get($_origin, 'data.id')
            : exit('Origin Path Abnormal');
        $target = $this->argument('target');
        $_target
            = OneDrive::responseToArray(OneDrive::pathToItemId(OneDrive::getRequestPath($target)));
        $target_id = $_origin['code'] === 200 ? array_get($_target, 'data.id')
            : exit('Target Path Abnormal');
        $rename = $this->option('rename') ?? '';
        $move = OneDrive::move($origin_id, $target_id, $rename);
        $response = OneDrive::responseToArray($move);
        $response['code'] === 200 ? $this->info("Move Success!")
            : $this->warn("Failed!\n{$response['msg']} ");
    }
}
