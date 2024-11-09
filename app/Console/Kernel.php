<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

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
        Log::info("Logger");
        $schedule->command('messages:send-scheduled')->everyMinute();
        $schedule->command('messages:send-prediction')->everyMinute();
    }

    /**
     * The Artisan commands provided by your application.
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\SendScheduledMessages::class,
        \App\Console\Commands\Prediction::class,
    ];
}
