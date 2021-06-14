<?php

namespace App\Console;


use App\Console\Commands\NotificationsProcess;
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
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // crawlers
        $schedule->command('socialblade:grab')->dailyAt("10:00");
        $schedule->command('twitch:grab')->dailyAt("12:00");

        // common crons
        $schedule->command('recompute:priorities')->dailyAt("14:00");
        $schedule->command('images:process')->dailyAt("15:00");
        $schedule->command('image:tool imagekit_clear')->dailyAt("16:00");

//        $schedule->command('cache:refresh')->everyFifteenMinutes();
        $schedule->command('cache:refresh')->everyMinute();

        if (app()->environment('localhost', 'local', 'dev')) {
            $schedule->command('users:fake')->dailyAt("01:30");
            $schedule->command('likes:fake')->dailyAt("02:30");
        }

        //
        $schedule->command("likes:recompute")->dailyAt("04:00");
        $schedule->command(NotificationsProcess::SIGNATURE)->dailyAt("0:0");
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
