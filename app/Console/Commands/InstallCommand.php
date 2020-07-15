<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Console\Commands;

use App\Service\Constants;
use Carbon\Carbon;
use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install App';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        $this->comment(Constants::LOGO);
        // step 1
        $canWritable = is_writable(storage_path());
        if (!$canWritable) {
            $this->warn('Please make sure the [storage] path can write!');
        }
        $lockFile = install_path('install.lock');
        if (file_exists($lockFile)) {
            $this->warn('Already Installed!');
            exit;
        }
        // step 2
        $sqlFile = install_path('data/database.sqlite');
        $sqlSampleFile = install_path('data/database.sample.sqlite');
        if (!file_exists($sqlSampleFile)) {
            $this->warn('[database.sample.sqlite] file missing,Please make sure the project complete!');
            exit;
        }
        if (!file_exists($sqlFile)) {
            $this->warn('Database not found,Creating...');
            copy($sqlSampleFile, $sqlFile);
        } else {
            $this->warn('Already have database file,Re-creating...');
            rename($sqlFile, $sqlFile . '.' . date('YmdHis') . '.bak');
            copy($sqlSampleFile, $sqlFile);
        }
        chmod($sqlFile, 0777);

        // step 3
        $this->warn('Env file not found,Creating...');
        $envSampleFile = base_path('.env.example');
        $envFile = base_path('.env');
        if (!file_exists($envSampleFile)) {
            $this->warn('[.env.example] file missing,Please make sure the project complete!');
            exit;
        }
        $_search = [
            'APP_KEY=',
        ];
        $_replace = [
            'APP_KEY=' . str_random(32),
        ];
        $envExample = file_get_contents($envSampleFile);
        $env = str_replace($_search, $_replace, $envExample);
        if (file_exists($envFile)) {
            if ($this->confirm('Already have [.env] ,overwrite?', true)) {
                rename($envFile, $envFile . '.' . date('YmdHis') . '.bak');
                file_put_contents($envFile, $env);
            }
        } else {
            file_put_contents($envFile, $env);
        }
        // step 4
        $this->call('config:cache');
        $this->call('migrate', ['--force' => true, '--seed' => true]);
        file_put_contents($lockFile, Carbon::now());
        $this->call('config:cache');
        $this->info('default name: [ admin ]');
        $this->info('default password: [ 123456 ]');
        $this->info('Install Complete!');
    }
}
