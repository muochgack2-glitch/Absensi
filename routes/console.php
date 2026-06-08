<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule: Auto-restart WhatsApp server daily at 3 AM
Schedule::command('wa:restart --force')
    ->dailyAt('03:00')
    ->name('wa-server-auto-restart')
    ->onSuccess(function () {
        \Log::info('WhatsApp server auto-restart completed successfully');
    })
    ->onFailure(function () {
        \Log::error('WhatsApp server auto-restart failed');
    });
