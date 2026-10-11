<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\Routific_create_project::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->dailyAt('08:00');
        // $schedule->command('minute:update')->everyMinute();

        // $schedule->command('db:backup')->everyMinute();
        $schedule->command('db:backup')->twiceDaily(8, 17);
        // $schedule->command('ssl:renew')->dailyAt('14:00');
        // $schedule->command('ssl:renew')->cron('0 0 3 */2 *');

        // $schedule->command('all:backup')->weekly();
        // $schedule->command('thegit:pullmaster')->everyMinute();
        // $schedule->command('s3:upload')->twiceDaily(9, 18);
    }

    /**
     * Get the bootstrap classes for the application.
     *
     * @return array
     */
    protected function bootstrappers()
    {
        $bootstrappers = parent::bootstrappers();
        array_splice($bootstrappers, 3, 0, [\App\Bootstrap\SuppressDeprecations::class]);
        return $bootstrappers;
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
