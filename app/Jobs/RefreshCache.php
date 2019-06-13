<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Symfony\Component\Process\Process;

class RefreshCache implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $artisan = base_path('artisan');
        $log = storage_path('app' . DIRECTORY_SEPARATOR . 'refresh.log');
        $phpPath = config('olaindex.php_path');
        $isWin = stripos(PHP_OS, 'WIN') === 0;
        $command = $isWin
            ? "start /b {$phpPath} {$artisan} od:cache >> {$log}"
            : "/usr/bin/nohup {$phpPath} {$artisan} od:cache >> {$log}  2>&1 &";
        $process = new Process($command);
        $process->run(static function ($type, $buffer) {
            if (Process::ERR === $type) {
                echo 'ERR > ' . $buffer;
            } else {
                echo 'OUT > ' . $buffer;
            }
        });
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
    }
}
