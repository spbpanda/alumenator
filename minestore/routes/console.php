<?php

use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

// Generate the random time for currency update
$hour = rand(0, 2);
$minute = rand(0, 59);
$randomTime = Carbon::createFromTime($hour, $minute)->format('H:i');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('pending_payments:cleanup')->everyThreeHours()->runInBackground();
Schedule::command('report:generate')->monthly()->runInBackground();
Schedule::command('currency:update')->dailyAt($randomTime)->runInBackground();
Schedule::command('minestore:deactivate-carts')->hourly()->runInBackground();
