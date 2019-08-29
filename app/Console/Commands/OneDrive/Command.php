<?php

namespace App\Console\Commands\OneDrive;

use Illuminate\Console\Command as BaseCommand;
use Symfony\Component\Console\Input\InputOption;

class Command extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'base';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '选择默认onedrive';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->addOption('one_drive_id', 'od_id', InputOption::VALUE_OPTIONAL, $this->description, null);
    }
}
