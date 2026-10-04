<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Pays yesterday's sponsor binary bonus at 00:10 IST, whatever the server's clock is set to.
// Needs the server cron to run `php artisan schedule:run` every minute.
Schedule::command('income:sponsor-binary-bonus')->dailyAt('00:10')->timezone('Asia/Kolkata')->withoutOverlapping();

/*
|--------------------------------------------------------------------------
| Scheduler heartbeat
|--------------------------------------------------------------------------
| Writes the current time every minute. If `php artisan scheduler:check`
| shows a recent time, cron is calling `schedule:run` correctly.
|--------------------------------------------------------------------------
*/
Schedule::call(function () {
    file_put_contents(storage_path('logs/scheduler-heartbeat.log'), now()->toDateTimeString());
})->everyMinute()->name('scheduler-heartbeat');

Artisan::command('scheduler:check', function () {
    $file = storage_path('logs/scheduler-heartbeat.log');

    if (!file_exists($file)) {
        $this->error('No heartbeat yet: cron has never run `php artisan schedule:run` on this server.');
        return 1;
    }

    $last = \Carbon\Carbon::parse(trim(file_get_contents($file)));
    $minutes = (int) $last->diffInMinutes(now(), true);

    if ($minutes <= 2) {
        $this->info("Cron is running. Last scheduler run: {$last} ({$minutes} min ago).");
        return 0;
    }

    $this->error("Cron looks stopped. Last scheduler run: {$last} ({$minutes} min ago).");
    return 1;
})->purpose('Check that cron is running the Laravel scheduler');
