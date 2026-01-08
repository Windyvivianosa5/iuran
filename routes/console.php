<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Kirim reminder pembayaran setiap hari jam 8 pagi
// Hanya akan kirim email di 7 hari terakhir bulan
Schedule::command('check:unpaid-users')
    ->dailyAt('08:00')
    ->withoutOverlapping();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');