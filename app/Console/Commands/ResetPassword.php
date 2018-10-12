<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class ResetPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '重置密码';

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
        $this->warn('========== 开始重置密码 ==========');
        $password = str_random(8);
        DB::table('parameters')->where('name','password')->update(['value' => md5($password)]);
        Artisan::call('cache:clear');
        $this->info('重置密码成功，新密码：'.$password);
    }
}
