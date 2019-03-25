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
        \App\Console\Commands\TransferStaffLog::class,
        \App\Console\Commands\TransferStaff::class,
        \App\Console\Commands\UpdateDistrictJs::class,
        \App\Console\Commands\MakeWorkingSchedule::class,
        \App\Console\Commands\GetSalePerformanceFromTDOA::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('attendance:makeSchedule')->dailyAt('4:00');
        $schedule->command('staff:transfer')->dailyAt('4:30');
        $schedule->command('make:district-js')->dailyAt('4:05');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }

}
