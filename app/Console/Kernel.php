<?php

namespace App\Console;

use App\Console\Commands\IncreaseIncomes;
use App\Console\Commands\PayIncomes;
use App\Console\Commands\PayLoans;
use App\Console\Commands\UpdateCourses;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(UpdateCourses::class)->dailyAt('00:00');
        $schedule->call(IncreaseIncomes::class)->dailyAt('09:00');
        $schedule->call(PayIncomes::class)->dailyAt('12:00');
        $schedule->call(PayLoans::class)->dailyAt('13:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
