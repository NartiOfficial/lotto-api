<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Scheduler w dni losowań (wtorek, czwartek, sobota) o 21:40
Schedule::command('draws:generate')
    ->cron('40 21 * * 2,4,6')
    ->appendOutputTo(storage_path('logs/draws_generate.log'));

// Scheduler wysyłający e-maile do zwycięzców w dni losowań (wtorek, czwartek, sobota) o 22:00
Schedule::command('emails:send-winners')
    ->cron('0 22 * * 2,4,6')
    ->appendOutputTo(storage_path('logs/emails_send_winners.log'));


// Scheduler testowy
Schedule::command('draws:generate')
    ->everyMinute()
    ->appendOutputTo(storage_path('logs/test_draws_generate.log'));

// Scheduler testowy
Schedule::command('emails:send-winners')
    ->everyMinute()
    ->appendOutputTo(storage_path('logs/test_emails_send_winners.log'));

// sail artisan schedule:run