<?php

namespace App\Console;

use DB;
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
        //This is the line of code added, at the end, we the have class name of FreeExpiredResources.php inside app\console\commands
        '\App\Console\Commands\FreeExpiredResources',
        '\App\Console\Commands\NotifyTodo',
        '\App\Console\Commands\MeetingMail',
        '\App\Console\Commands\ReportEmails',
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
        //          ->hourly();
        
        //insert name and signature of you command and define the time of excusion
        $schedule->command('FreeExpiredResources:freeresources')
                 ->dailyAt('00:15'); 
        
        $schedule->command('NotifyTodo:sendnotification')
                 ->dailyAt('00:05'); 
        
        $schedule->command('MeetingMail:sendmeetingmail')
                 ->everyThirtyMinutes();
        
        $schedule->command('ReportEmails:sendreportemails')
                 ->everyThirtyMinutes();
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
