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
        'App\Console\Commands\RetrieveCurrencies',
        'App\Console\Commands\SendEmails',
        'App\Console\Commands\PushNotifications',
        'App\Console\Commands\DeleteImages',
        'App\Console\Commands\DeleteCarts',
        'App\Console\Commands\RetrieveTootbus',
        'App\Console\Commands\CheckBarcodes',
        'App\Console\Commands\CheckSolds',
        'App\Console\Commands\CustomerReminderEmails',
        'App\Console\Commands\PlaceBarcodes',
        'App\Console\Commands\BookingCounterReminderEmails',
        'App\Console\Commands\BookingInformation',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('retrieve:currencies')
            ->everyFifteenMinutes();
        $schedule->command('send:emails')
            ->everyMinute();
        $schedule->command('send:booking-info')
            ->everyMinute();
        $schedule->command('push:notifications')
            ->everyMinute();
        $schedule->command('delete:images')
            ->daily();
        $schedule->command('delete:carts')
            ->everyMinute();
        $schedule->command('retrieve:tootbus')
        ->daily();
        $schedule->command('check:barcodes')
        ->daily();
        $schedule->command('check:solds')
            ->daily();
//        $schedule->command('reminder:emails')
//        ->dailyAt('22:00');
//        //$schedule->command('place:barcodes')->twiceDaily(0, 12);
//        $schedule->command('booking-counter:reminder-emails')
//        ->dailyAt('09:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
