<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Symfony\Component\Process\Process;

class RefreshJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $artisan = base_path('artisan');
        $log = storage_path('app' . DIRECTORY_SEPARATOR . 'refresh.log');
        $phpPath = config('olaindex.php_path');
        $isWin = stripos(PHP_OS, 'WIN') === 0;
        $command = $isWin
            ? "start /b {$phpPath} {$artisan} refresh:data >> {$log}"
            : "/usr/bin/nohup {$phpPath} {$artisan} refresh:data >> {$log}  2>&1 &";
        $process = new Process($command);
        $process->run(static function ($type, $buffer) {
            if (Process::ERR === $type) {
                echo 'ERR > ' . $buffer;
            } else {
                echo 'OUT > ' . $buffer;
            }
        });
    }
}
