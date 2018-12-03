<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\OneDrive;
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
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
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
     * @throws \GuzzleHttp\Exception\GuzzleException
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
            $graphPath = OneDrive::getRequestPath($remote);
            $id_response
                = OneDrive::responseToArray(OneDrive::pathToItemId($graphPath));
            if ($id_response['code'] === 200) {
                $id = $id_response['data']['id'];
            } else {
                $this->warn('Path Abnormal!');
                exit;
            }
        }
        $response = OneDrive::responseToArray(OneDrive::delete($id));
        $this->call('cache:clear');
        $response['code'] === 200 ? $this->info("Deleted!")
            : $this->warn("Failed!\n{$response['msg']} ");
    }
}
