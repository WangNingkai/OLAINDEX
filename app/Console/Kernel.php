<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Spatie\ShortSchedule\ShortSchedule;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        Commands\RefreshCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        // $schedule->command('refresh:data')->everyFifteenMinutes();
        $schedule->command('telescope:prune')->daily();
    }

    /**
     * Define the application's command schedule.
     *
     * @param ShortSchedule $shortSchedule
     * @return void
     */
    protected function shortSchedule(ShortSchedule $shortSchedule)
    {
        // 此命令每秒钟会运行一次
        // $shortSchedule->command('artisan-command')->everySecond();

        // 此命令每30秒会运行一次
        // $shortSchedule->command('another-artisan-command')->everySeconds(30);

        // 此命令每0.5秒会运行一次
        // $shortSchedule->command('another-artisan-command')->everySeconds(0.5);

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
