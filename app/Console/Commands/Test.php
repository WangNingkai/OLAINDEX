<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Matomo\Ini\IniReader;
use Matomo\Ini\IniWriter;
use Noodlehaus\Config;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 't';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @throws \Matomo\Ini\IniReadingException
     * @throws \Matomo\Ini\IniWritingException
     */
    public function handle()
    {
//        $conf = Config::load(storage_path('app/example.config.json'));
////        dump($conf->all());
////        $conf->set('app_type','cn');
////        dump($conf->all());
//        dd($conf->get('app_type'));
        $reader = new IniReader();
        $array = $reader->readFile(storage_path('app/example.account.ini'));
//        dd($array);
        $array['user1']['name'] = 123;
        $writer = new IniWriter();
        $writer->writeToFile(storage_path('app/example.account.ini'), $array);

    }
}
