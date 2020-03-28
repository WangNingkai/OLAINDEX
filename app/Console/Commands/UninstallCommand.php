<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UninstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uninstall';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uninstall App';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        $lockFile = install_path('install.lock');
        $envFile = base_path('.env');
        $sqlFile = install_path('data/database.sqlite');
        $step_1 = file_exists($lockFile) && unlink($lockFile);
        $step_2 = file_exists($envFile) && unlink($envFile);
        $step_3 = file_exists($sqlFile) && unlink($sqlFile);
        ($step_1 && $step_2 && $step_3) ? $this->info('Uninstall Complete') : $this->info('Uninstall Failed');
    }
}
