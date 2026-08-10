<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Device Health Check
|--------------------------------------------------------------------------
|
| Mendeteksi device aktif yang berhenti mengirim data (heartbeat sudah
| lewat ambang batas) dan mengirim notifikasi "Perangkat Offline".
| Notifikasi "kembali online" dikirim sinkron di GPSProcessingService
| saat heartbeat baru masuk, bukan lewat job ini.
|--------------------------------------------------------------------------
*/

Schedule::command('device:health-check')
    ->everyMinute()
    ->withoutOverlapping();
