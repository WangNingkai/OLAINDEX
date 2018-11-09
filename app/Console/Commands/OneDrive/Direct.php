<?php

namespace App\Console\Commands\OneDrive;

use Illuminate\Console\Command;

class Direct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:direct
                            {path : 文件地址}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Direct For File';

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
    }
}
