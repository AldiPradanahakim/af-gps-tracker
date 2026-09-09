<?php

namespace App\Services\Notification;

use App\Helpers\AppTime;
use App\Models\Notification;
use App\Models\User;
use App\Models\TravelHistory;
use App\Services\Geofence\ReverseGeocodingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;
use Throwable;

class NotificationPresenter
{
    /**
     * Nilai search_address yang dianggap "belum jadi" - harus di-resolve
     * ulang sebelum dikirim ke Email/WhatsApp atau ditampilkan di
     * halaman pelacakan publik.
     *
     * 'Memuat alamat...' adalah placeholder yang ditulis
     * GPSProcessingService ketika cache reverse-geocoding miss pada
     * jalur ingest MQTT (yang memang tidak boleh memblokir).
     */
    protected const PLACEHOLDER_ADDRESSES = [
        'Memuat alamat...',
        'Alamat tidak ditemukan',
        'Mencari alamat...',
        '-',
        '',
    ];

    /**
     * Format waktu notification dengan format Indonesia.
     *
     * Zona waktunya mengikuti pilihan PENERIMA notifikasi ($recipient),
     * bukan zona waktu server. Email/WhatsApp dikirim lewat queued job
     * yang tidak punya sesi login, jadi penerima harus dioper eksplisit -
     * kalau tidak, pemilik kendaraan di Papua akan menerima pesan
     * berjam WIB.
     *
     * Penyimpanan timestamp tidak berubah; hanya tampilannya yang
     * dikonversi.
     */
    public static function formatDateTime(
        ?Carbon $dateTime,
        ?User $recipient = null
    ): string {

        return AppTime::formatLong(
            $dateTime,
            user: $recipient
        );
    }

    /**
     * Label singkat zona waktu (WIB/WITA/WIT) untuk penerima tertentu.
     */
    public static function timezoneLabel(?User $recipient = null): string
    {
        return AppTime::timezoneLabel($recipient);
    }

    /**
     * Penerima notification (pemilik perangkat) - sumber zona waktu
     * untuk seluruh tanggal/jam di dalam pesan.
     */
    public static function recipient(Notification $notification): ?User
    {
        return $notification->device?->user;
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
     * Koordinat yang harus ditampilkan untuk sebuah notification.
     *
     * Sebagian tipe notification (baterai lemah, perangkat offline/
     * online) tidak membawa koordinat di payload-nya, sehingga link
     * "Lihat Lokasi di Peta" sebelumnya membuka peta kosong. Untuk
     * kasus itu kita jatuhkan ke posisi terakhir yang diketahui device
     * PADA SAAT notification dibuat, lalu simpan permanen ke kolom
     * `data` supaya link tetap konsisten kapan pun dibuka.
     *
     * @return array{lat: float|null, lng: float|null}
     */
    public static function resolveLocation(Notification $notification): array
    {
        $latitude = data_get($notification->data, 'location.lat');

        $longitude = data_get($notification->data, 'location.lng');

        if ($latitude !== null && $longitude !== null) {

            return [
                'lat' => (float) $latitude,
                'lng' => (float) $longitude,
            ];
        }

        $travelHistory = TravelHistory::query()
            ->where('device_id', $notification->device_id)
            ->where('received_at', '<=', $notification->created_at ?? now())
            ->latest('received_at')
            ->first()

            ?? TravelHistory::query()
                ->where('device_id', $notification->device_id)
                ->latest('received_at')
                ->first();

        $latitude = data_get($travelHistory?->location, 'lat');

        $longitude = data_get($travelHistory?->location, 'lng');

        if ($latitude === null || $longitude === null) {

            return ['lat' => null, 'lng' => null];
        }

        self::persist($notification, [

            'location' => [
                'lat' => (float) $latitude,
                'lng' => (float) $longitude,
            ],

            'search_address' => self::isPlaceholder(
                data_get($notification->data, 'search_address')
            )
                ? $travelHistory?->search_address
                : null,

        ]);

        return [
            'lat' => (float) $latitude,
            'lng' => (float) $longitude,
        ];
    }

    /**
     * Alamat lengkap untuk sebuah notification.
     *
     * Jalur ingest MQTT tidak boleh memblokir, jadi ketika cache
     * reverse-geocoding miss dia hanya menulis placeholder
     * "Memuat alamat...". Placeholder itulah yang sebelumnya ikut
     * terkirim ke Email/WhatsApp dan tampil di halaman pelacakan.
     *
     * Di sini - dan hanya di konteks yang boleh menunggu (queued job /
     * request HTTP biasa) - alamat di-resolve sungguhan, lalu hasilnya
     * disimpan balik ke kolom `data` supaya tidak perlu di-geocode
     * berulang kali.
     */
    public static function resolveAddress(Notification $notification): ?string
    {
        $address = data_get($notification->data, 'search_address');

        if (! self::isPlaceholder($address)) {

            return $address;
        }

        $location = self::resolveLocation($notification);

        if ($location['lat'] === null || $location['lng'] === null) {

            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | resolveLocation() bisa mengisi search_address dari travel
        | history terdekat - pakai itu dulu sebelum memanggil provider.
        |--------------------------------------------------------------------------
        */

        $address = data_get($notification->fresh()?->data ?? $notification->data, 'search_address');

        if (! self::isPlaceholder($address)) {

            return $address;
        }

        try {

            $resolved = app(ReverseGeocodingService::class)->search(
                $location['lat'],
                $location['lng']
            );
        } catch (Throwable $exception) {

            report($exception);

            return null;
        }

        if (self::isPlaceholder($resolved)) {

            return null;
        }

        self::persist($notification, [
            'search_address' => $resolved,
        ]);

        return $resolved;
    }

    /**
     * Apakah nilai alamat masih berupa placeholder/kosong.
     */
    public static function isPlaceholder(?string $address): bool
    {
        return in_array(
            trim((string) $address),
            self::PLACEHOLDER_ADDRESSES,
            true
        );
    }

    /**
     * Simpan perubahan sebagian ke kolom `data` notification tanpa
     * menimpa key lain.
     */
    protected static function persist(
        Notification $notification,
        array $changes
    ): void {

        $data = $notification->data ?? [];

        $changed = false;

        foreach ($changes as $key => $value) {

            if ($value === null) {
                continue;
            }

            data_set($data, $key, $value);

            $changed = true;
        }

        if (! $changed) {
            return;
        }

        $notification->data = $data;

        $notification->saveQuietly();
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
     * atau ditebak untuk notification lain. Masa berlaku 30 hari dipilih
     * supaya pesan lama di kotak masuk/riwayat WhatsApp tidak keburu
     * mati saat dibuka kembali.
     */
    public static function publicTrackingLink(
        Notification $notification,
        int $hours = 720
    ): string {

        return URL::temporarySignedRoute(
            'track.show',
            now()->addHours($hours),
            ['notification' => $notification->id],
        );
    }
}
