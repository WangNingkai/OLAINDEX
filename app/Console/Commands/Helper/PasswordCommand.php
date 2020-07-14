<?php

namespace App\Console\Commands\Helper;

use App\Models\User;
use Illuminate\Console\Command;

class PasswordCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'helper:reset-password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset User`s Password';

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
        $users = User::all(['id', 'name']);
        $name = $this->choice('选择要重置密码的账号：', array_column($users->toArray(), 'name'), 0);
        $password = $this->ask('请输入密码：');
        $user = User::query()->where('name', $name)->first();
        if ($user->fill([
            'password' => \Hash::make($password)
        ])->save()) {
            $this->info("账号： [{$name}] 密码：[{$password}]");
            $this->info('修改成功！');
        } else {
            $this->warn('修改失败！');
        }
    }
}
