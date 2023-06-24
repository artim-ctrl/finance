<?php

declare(strict_types = 1);

namespace App\Console;

use App\Console\Commands\IncreaseIncomes;
use App\Console\Commands\PayIncomes;
use App\Console\Commands\PayLoans;
use App\Console\Commands\UpdateCourses;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

final class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(callback: UpdateCourses::class)->dailyAt(time: '00:00');
        $schedule->call(callback: IncreaseIncomes::class)->dailyAt(time: '09:00');
        $schedule->call(callback: PayIncomes::class)->dailyAt(time: '12:00');
        $schedule->call(callback: PayLoans::class)->dailyAt(time: '13:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(paths: __DIR__ . '/Commands');

        require base_path(path: 'routes/console.php');
    }
}
