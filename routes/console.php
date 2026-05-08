<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Automatically generate upcoming recurring services every Monday at midnight
Schedule::command('attendance:generate-recurring --weeks=4')->weekly()->mondays()->at('00:00');

