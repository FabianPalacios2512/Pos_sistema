<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 🔔 CRON: Emails de expiración de Trial Express
// Ejecutar diariamente a las 9:00 AM hora Colombia
Schedule::command('trial:send-expiration-emails')
    ->dailyAt('09:00')
    ->timezone('America/Bogota')
    ->withoutOverlapping()
    ->onOneServer();
