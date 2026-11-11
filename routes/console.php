<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

$generateRiwayatPengecekan = Schedule::command('riwayat-pengecekan:generate')
    ->withoutOverlapping()
    ->onOneServer();

if (app()->isLocal()) {
    $generateRiwayatPengecekan->everyTenSeconds();
} else {
    $generateRiwayatPengecekan->dailyAt('00:00')->timezone('Asia/Makassar');
}
