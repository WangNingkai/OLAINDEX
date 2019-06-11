<?php

namespace App\Console\Commands\OneDrive;

use App\Service\OneDrive;
use Illuminate\Console\Command;

class Remove extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:rm
                            {remote? : Remote path}
                            {--id= : ID}
                            {--f|force : Force Delete}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Item';

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
        $this->info('请稍等...');
        $this->call('od:refresh');
        if ($this->option('force')) {
            return $this->delete();
        }
        if ($this->confirm('You can not restore,continue?')) {
            return $this->delete();
        }
    }

    /**
     * @throws \ErrorException
     */
    public function delete()
    {
        if ($this->option('id')) {
            $id = $this->option('id');
        } else {
            $remote = $this->argument('remote');
            if (!$remote) {
                $this->warn('Parameter Missing!');
                exit;
            }
            $id_response
                = OneDrive::getInstance(one_account())->pathToItemId($remote);
            if ($id_response['errno'] === 0) {
                $id = $id_response['data']['id'];
            } else {
                $this->warn('Path Abnormal!');
                exit;
            }
        }
        $response = OneDrive::getInstance(one_account())->delete($id);
        $this->call('cache:clear');
        $response['errno'] === 0 ? $this->info('Deleted')
            : $this->warn("Failed!\n{$response['msg']} ");
    }
}
