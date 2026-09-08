<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\StopHistory;
use App\Services\Geofence\ReverseGeocodingService;
use App\Services\Notification\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SendTestNotification extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'notification:test
        {device? : UUID atau device_id kendaraan. Kosongkan untuk memakai kendaraan pertama}
        {--type=all : geofence_enter, geofence_exit, stop, atau all}';

    /**
     * The console command description.
     */
    protected $description = 'Kirim notification uji coba (System + Realtime + Email/WhatsApp sesuai pengaturan device) lewat jalur production yang sama.';

    public function handle(
        NotificationService $notificationService,
        ReverseGeocodingService $reverseGeocodingService
    ): int {

        $device = $this->resolveDevice();

        if (! $device) {

            $this->error('Kendaraan tidak ditemukan. Pastikan sudah ada device yang teraktivasi.');

            return self::FAILURE;
        }

        $device->loadMissing(['user', 'vehicle', 'geofences']);

        $this->info("Kendaraan: {$device->vehicle?->vehicle_name} ({$device->device_id})");

        $this->info('User: ' . ($device->user?->email ?? '-'));

        $this->info('notification_setting: ' . json_encode($device->notification_setting));

        $this->info('stop_setting: ' . json_encode($device->stop_setting));

        $this->newLine();

        /*
        |--------------------------------------------------------------------------
        | Titik lokasi uji coba
        |--------------------------------------------------------------------------
        |
        | "Masuk"/"Berhenti" dipakaikan koordinat home_location device
        | (titik di dalam geofence). "Keluar" wajib pakai koordinat lain
        | yang jelas di luar area, supaya notification exit tidak pernah
        | menampilkan lokasi/alamat yang sama seperti saat masih di
        | dalam geofence - persis seperti perilaku pipeline GPS asli,
        | yang selalu memakai koordinat GPS payload saat itu juga.
        |--------------------------------------------------------------------------
        */

        $insideLatitude = (float) data_get($device->home_location, 'lat', -6.2);

        $insideLongitude = (float) data_get($device->home_location, 'lng', 106.8);

        $insideAddress = $reverseGeocodingService->search($insideLatitude, $insideLongitude);

        // ~3km ke utara - cukup jauh dari kebanyakan geofence radius/kelurahan.
        $outsideLatitude = $insideLatitude + 0.03;

        $outsideLongitude = $insideLongitude;

        $outsideAddress = $reverseGeocodingService->search($outsideLatitude, $outsideLongitude);

        $type = $this->option('type');

        $created = [];

        if (in_array($type, ['geofence_enter', 'geofence_exit', 'all'], true)) {

            $geofence = $device->geofences->firstWhere('status', true)
                ?? $device->geofences->first();

            if (! $geofence) {

                $this->warn('Device belum punya geofence, tipe geofence_enter/geofence_exit dilewati. Buat geofence dulu di halaman Detail Kendaraan.');

            } else {

                if (in_array($type, ['geofence_enter', 'all'], true)) {

                    $created[] = $notificationService->createGeofenceEnterNotification($device, $geofence, [

                        'lat' => $insideLatitude,

                        'lng' => $insideLongitude,

                        'search_address' => $insideAddress,

                    ]);
                }

                if (in_array($type, ['geofence_exit', 'all'], true)) {

                    $created[] = $notificationService->createGeofenceExitNotification($device, $geofence, [

                        'lat' => $outsideLatitude,

                        'lng' => $outsideLongitude,

                        'search_address' => $outsideAddress,

                    ]);
                }
            }
        }

        if (in_array($type, ['stop', 'all'], true)) {

            $startTime = now()->subMinutes(10);

            $endTime = now();

            $stopHistory = StopHistory::create([

                'device_id' => $device->id,

                'location' => [

                    'lat' => $insideLatitude,

                    'lng' => $insideLongitude,

                    'speed' => 0,

                ],

                'search_address' => $insideAddress,

                'start_time' => $startTime,

                'end_time' => $endTime,

                'duration_seconds' => (int) $endTime->diffInSeconds($startTime, absolute: true),

                'notification_sent' => false,

            ]);

            $created[] = $notificationService->createStopNotification($device, $stopHistory);

            $stopHistory->update(['notification_sent' => true]);
        }

        if (! $created) {

            $this->warn('Tidak ada notification yang dibuat.');

            return self::FAILURE;
        }

        $this->newLine();

        $this->info(count($created) . ' notification berhasil dibuat:');

        foreach ($created as $notification) {

            $this->line("- [{$notification->type}] {$notification->id} - " . data_get($notification->data, 'title'));
        }

        $this->newLine();

        $this->comment('Jalankan "php artisan queue:work --stop-when-empty" agar Email/WhatsApp (jika aktif di pengaturan device) benar-benar terkirim.');

        return self::SUCCESS;
    }

    /**
     * Cari device berdasarkan argumen (UUID atau device_id code).
     */
    protected function resolveDevice(): ?Device
    {
        $identifier = $this->argument('device');

        if (! $identifier) {

            return Device::query()->with('vehicle')->first();
        }

        /*
         * Kolom id bertipe uuid. Membandingkannya dengan teks bebas seperti
         * "GPS-AF-0001" membuat PostgreSQL membatalkan seluruh query dengan
         * "invalid input syntax for type uuid" - jadi device_id yang sah pun
         * tidak pernah sempat dicoba, padahal argumen ini memang menerima
         * keduanya. MySQL memaafkan hal itu, PostgreSQL tidak.
         */
        $query = Device::query()->with('vehicle');

        if (Str::isUuid($identifier)) {

            $query->where('id', $identifier);
        } else {

            $query->where('device_id', $identifier);
        }

        return $query->first();
    }
}
