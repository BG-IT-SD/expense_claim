<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// Schedule::command('fuel:update')->dailyAt('05:30');
Schedule::command('fuel:update')->everyMinute();
// update bu
Schedule::command('app:update-user-daily')->everyMinute();
