<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('draws:generate --count=7', function () {
    Artisan::call(\App\Console\Commands\GenerateDraws::class);
})->purpose('Generowanie losowań');
