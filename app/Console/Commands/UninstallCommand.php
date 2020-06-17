<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Console\Commands;

use App\Service\Constants;
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
        $this->info(Constants::LOGO);
        $lockFile = install_path('install.lock');
        $envFile = base_path('.env');
        $sqlFile = install_path('data/database.sqlite');
        $step_1 = file_exists($lockFile) && unlink($lockFile);
        $step_2 = file_exists($envFile) && unlink($envFile);
        $step_3 = file_exists($sqlFile) && unlink($sqlFile);
        ($step_1 && $step_2 && $step_3) ? $this->info('Uninstall Complete') : $this->info('Uninstall Failed');
    }
}
