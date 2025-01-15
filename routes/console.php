<?php

use Illuminate\Support\Facades\Artisan;

// Scheduler: wtorek (2), czwartek (4), sobota (6), o 21:40
Artisan::command('schedule:draw-check', function () {
    $this->call('draws:generate');
})->purpose('Ensure 6 upcoming draws')->cron('40 21 * * 2,4,6');
