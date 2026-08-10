<?php

namespace App\Services\Notification;

use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;

class NotificationPresenter
{
    /**
     * Format waktu notification ke zona WIB dengan format Indonesia.
     *
     * Penyimpanan tetap UTC (default Laravel); hanya tampilan yang
     * dikonversi, supaya tidak mengubah timezone aplikasi secara global.
     */
    public static function formatDateTime(?Carbon $dateTime): string
    {
        if (! $dateTime) {

            return '-';
        }

        return $dateTime
            ->copy()
            ->timezone('Asia/Jakarta')
            ->locale('id')
            ->translatedFormat('d F Y, H:i') . ' WIB';
    }

    /**
     * Tautan Google Maps untuk satu titik koordinat.
     */
    public static function mapsLink(?float $latitude, ?float $longitude): ?string
    {
        if ($latitude === null || $longitude === null) {

            return null;
        }

        return "https://maps.google.com/?q={$latitude},{$longitude}";
    }

    /**
     * Tautan halaman pelacakan publik (tanpa login) untuk notification ini.
     *
     * Diikat ke notification (bukan device) supaya titik yang tampil di
     * peta selalu SAMA dengan lokasi yang tertulis di pesan Email/
     * WhatsApp - bukan lokasi device terkini yang bisa saja sudah
     * berubah/berbeda saat link dibuka.
     *
     * Menggunakan signed URL Laravel: hanya valid dengan signature yang
     * benar dan kedaluwarsa otomatis setelah $hours jam, supaya link
     * yang tersebar lewat WhatsApp/Email tidak bisa dipakai selamanya
     * atau ditebak untuk notification lain.
     */
    public static function publicTrackingLink(
        Notification $notification,
        int $hours = 48
    ): string {

        return URL::temporarySignedRoute(
            'track.show',
            now()->addHours($hours),
            ['notification' => $notification->id],
        );
    }
}
