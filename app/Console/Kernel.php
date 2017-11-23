<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\SendAttedenceNotifications::class,
        \App\Console\Commands\ImportParents::class,
        \App\Console\Commands\ImportSingleStudentParents::class,
        \App\Console\Commands\SendNotifications::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        // * * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1

        //Import New parents into Pacifyca mobile Application

        //$schedule->command('client:import-parents')->everyThirtyMinutes();

        //Send notification to parents
        //Run the task daily twic at 1:00 PM & 06:00 PM (1 PM and 6 PM)

        //$schedule->command('notification:send-attendence')->twiceDaily(13, 18);

    }
}
