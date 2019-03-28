<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\Constants;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install App';

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
     * Execute Command
     */
    public function handle()
    {
        $this->info(Constants::LOGO);
        $this->call('cache:clear');
        $this->warn('Please make sure you have rights to configure!');
        $this->info('chmod -R 755 storage/* && chown -R www:www *');
        if (!file_exists(storage_path('app/config.json'))) {
            $this->warn('Missing the Configuration File');
            copy(
                storage_path('app/example.config.json'),
                storage_path('app/config.json')
            );
            $this->info('Done!');
        };
        if (!file_exists(base_path('.env.example'))) {
            $this->warn('No [.env.example] File,please make sure the project complete!');
            exit;
        }
        $app_url = $this->ask('Bind Domain(For Authorize)');
        $search_db = [
            'APP_KEY=',
            'APP_URL=http://localhost:8000',
        ];
        $replace_db = [
            'APP_KEY=' . Str::random(32),
            'APP_URL=' . $app_url,
        ];
        $envExample = file_get_contents(base_path('.env.example'));
        $env = str_replace($search_db, $replace_db, $envExample);
        if (file_exists(base_path('.env'))) {
            if ($this->confirm('Already have [.env] ,overwrite?')) {
                @unlink(base_path('.env'));
                file_put_contents(base_path('.env'), $env);
            }
        } else {
            file_put_contents(base_path('.env'), $env);
        }
        $this->call('config:cache'); // 生成配置缓存否则报错
        $this->warn('Password：[ 12345678 ]');
        $cmd = ['chmod', '777', 'storage/app/config.json'];
        $process = new Process($cmd);
        $process->run();
        if (!$process->isSuccessful()) {
            $this->info('Please run this command to make sure you have the permission'
                . '[ chmod 777 storage/app/config.json ]');
            throw new ProcessFailedException($process);
        }
        echo $process->getOutput();
        $this->warn('All Done!');
    }
}
