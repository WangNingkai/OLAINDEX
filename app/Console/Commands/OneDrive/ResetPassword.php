<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\Tool;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ResetPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset Password';

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
        $password = Str::random(8);

        User::query()->update([
            'name' => 'admin',
            'password' => bcrypt($password),
        ]);
        $this->info("New Password:[ {$password} ]");
    }
}
