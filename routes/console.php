<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Daily Freelancer Reputation & Badges Recalculation Engine Cron
Schedule::command('freelancers:recalculate-badges')
    ->daily()
    ->at('00:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/badges_cron.log'));

