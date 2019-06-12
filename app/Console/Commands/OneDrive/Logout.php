<?php

namespace App\Console\Commands\OneDrive;

use App\Models\Setting;
use App\Utils\Tool;
use Illuminate\Console\Command;

class Logout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:logout {--f|force : Force Logout}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Account Logout';

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
     */
    public function handle()
    {
        if ($this->option('force')) {
            return $this->logout();
        }
        if ($this->confirm('Confirm Logout?')) {
            return $this->logout();
        }
    }

    /**
     * Execute Reset Command
     */
    public function logout()
    {
        $data = [
            'access_token' => '',
            'refresh_token' => '',
            'access_token_expires' => 0,
            'root' => '/',
            'image_hosting' => 0,
            'image_hosting_path' => '',
            'account_email' => '',
            'account_state' => '暂时无法使用',
            'account_extend' => ''
        ];
        $saved = Setting::batchUpdate($data);
        if ($saved) {
            $this->call('cache:clear');
            $this->warn('Logout Success!');
        }
    }
}
