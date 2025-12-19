<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\SendBookingReminderJob;
use App\Models\TransportReservation;
use Carbon\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {

        $now = Carbon::now();

        // قبل يوم
        TransportReservation::whereBetween(
            'pickup_datetime',
            [$now->copy()->addDay()->startOfMinute(), $now->copy()->addDay()->endOfMinute()]
        )->get()->each(function ($reservation) {
            SendBookingReminderJob::dispatch($reservation, 'day');
        });

        // قبل ساعة
        TransportReservation::whereBetween(
            'pickup_datetime',
            [$now->copy()->addHour()->startOfMinute(), $now->copy()->addHour()->endOfMinute()]
        )->get()->each(function ($reservation) {
            SendBookingReminderJob::dispatch($reservation, 'hour');
        });

    })->everyMinute();
}

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
