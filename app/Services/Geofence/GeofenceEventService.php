<?php

namespace App\Services\Geofence;

use App\Models\Device;
use App\Models\Geofence;
use App\Models\GeofenceHistory;
use App\Services\Notification\NotificationService;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

/**
 * Menerjemahkan hasil GeofenceCheckerService menjadi efek samping:
 * riwayat masuk/keluar, notifikasi per geofence, dan pengingat
 * "masih di luar area".
 *
 * Dipisah dari GPSProcessingService supaya pipeline ingest GPS tetap
 * tipis, dan supaya logika ini bisa diuji tanpa MQTT.
 */
class GeofenceEventService
{
    /**
     * Jeda minimum antar pengingat. Interval yang lebih rapat akan
     * menghabiskan kuota email/WhatsApp tanpa menambah informasi.
     */
    public const MIN_REPEAT_MINUTES = 5;

    public const MAX_REPEAT_MINUTES = 180;

    public const DEFAULT_REPEAT_MINUTES = 15;

    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * @param  array<string, mixed>  $geofenceResult  hasil GeofenceCheckerService::process()
     * @param  array<string, mixed>  $payload         payload GPS mentah
     */
    public function handle(
        Device $device,
        array $geofenceResult,
        array $payload,
        ?string $searchAddress = null
    ): void {

        $results = $geofenceResult['results'] ?? [];

        if (empty($results)) {

            return;
        }

        foreach ($results as $result) {

            $geofence = $result['geofence'] ?? null;

            if (! $geofence instanceof Geofence) {

                continue;
            }

            /*
            | Pengecekan yang gagal dibaca tidak boleh menghasilkan
            | kejadian apa pun - statusnya belum tentu benar.
            */

            if ($result['evaluation_failed'] ?? false) {

                continue;
            }

            /*
            |------------------------------------------------------------------
            | Tiap geofence diproses terpisah. Kegagalan pada satu area
            | (mis. tabel notifikasi sedang bermasalah) tidak boleh ikut
            | membatalkan area lain - dan karena status baru hanya disimpan
            | setelah efeknya berhasil, area yang gagal akan dicoba lagi
            | pada payload GPS berikutnya.
            |------------------------------------------------------------------
            */

            try {

                if ($result['entered'] ?? false) {

                    $this->handleEnter(
                        $device,
                        $geofence,
                        $result,
                        $payload,
                        $searchAddress
                    );

                    continue;
                }

                if ($result['exited'] ?? false) {

                    $this->handleExit(
                        $device,
                        $geofence,
                        $result,
                        $payload,
                        $searchAddress
                    );

                    continue;
                }

                $this->handleStillOutside(
                    $device,
                    $geofence,
                    $result,
                    $payload,
                    $searchAddress
                );
            } catch (\Throwable $exception) {

                report($exception);
            }
        }
    }

    /**
     * Kendaraan masuk kembali ke satu area.
     */
    protected function handleEnter(
        Device $device,
        Geofence $geofence,
        array $result,
        array $payload,
        ?string $searchAddress
    ): void {

        $this->recordHistory(
            $device,
            $geofence,
            'enter',
            $result,
            $payload,
            $searchAddress
        );

        $this->notificationService->createGeofenceEnterNotification(
            device: $device,
            geofence: $geofence,
            payload: $this->notificationPayload($payload, $searchAddress)
        );

        /*
        |--------------------------------------------------------------------------
        | Status baru disimpan PALING AKHIR - setelah riwayat dan notifikasi
        | benar-benar tercatat. Kalau salah satunya gagal, status lama tetap
        | utuh dan payload GPS berikutnya akan mencoba lagi, sehingga
        | kejadian ini tidak pernah hilang tanpa jejak.
        |--------------------------------------------------------------------------
        */

        $geofence->forceFill([
            'is_inside' => true,
            'state_changed_at' => Carbon::now(),
            'last_exit_notified_at' => null,
        ])->save();
    }

    /**
     * Kendaraan keluar dari satu area.
     */
    protected function handleExit(
        Device $device,
        Geofence $geofence,
        array $result,
        array $payload,
        ?string $searchAddress
    ): void {

        $this->recordHistory(
            $device,
            $geofence,
            'exit',
            $result,
            $payload,
            $searchAddress
        );

        $this->notificationService->createGeofenceExitNotification(
            device: $device,
            geofence: $geofence,
            payload: $this->notificationPayload($payload, $searchAddress)
        );

        /*
        |--------------------------------------------------------------------------
        | Status baru disimpan PALING AKHIR (lihat handleEnter).
        |
        | last_exit_notified_at menandai awal periode "di luar". Pengingat
        | berikutnya dihitung dari sini, bukan dari waktu perpindahan status,
        | supaya jeda antar pesan yang diterima pengguna benar-benar sesuai
        | pengaturan.
        |--------------------------------------------------------------------------
        */

        $geofence->forceFill([
            'is_inside' => false,
            'state_changed_at' => Carbon::now(),
            'last_exit_notified_at' => Carbon::now(),
        ])->save();
    }

    /**
     * Kendaraan masih di luar area yang sama - kirim pengingat kalau
     * fiturnya dinyalakan dan jeda waktunya sudah terlampaui.
     *
     * Pengingat hanya dikirim untuk area yang memang pernah tercatat
     * "keluar" (last_exit_notified_at terisi). Tanpa syarat itu,
     * menyalakan fitur ini akan langsung membanjiri pengguna dengan
     * pengingat untuk area yang kendaraannya memang tidak pernah masuk.
     */
    protected function handleStillOutside(
        Device $device,
        Geofence $geofence,
        array $result,
        array $payload,
        ?string $searchAddress
    ): void {

        if ($result['inside'] ?? true) {

            return;
        }

        if ($result['baseline'] ?? false) {

            return;
        }

        $setting = $this->repeatSetting($device);

        if (! $setting['enabled']) {

            return;
        }

        $lastNotifiedAt = $geofence->last_exit_notified_at;

        if (! $lastNotifiedAt instanceof CarbonInterface) {

            return;
        }

        $dueAt = $lastNotifiedAt->copy()->addMinutes($setting['minutes']);

        if (Carbon::now()->lessThan($dueAt)) {

            return;
        }

        $minutesOutside = $this->minutesSince(
            $geofence->state_changed_at ?? $lastNotifiedAt
        );

        $this->notificationService->createGeofenceStillOutsideNotification(
            device: $device,
            geofence: $geofence,
            payload: $this->notificationPayload($payload, $searchAddress),
            minutesOutside: $minutesOutside
        );

        $geofence->forceFill([
            'last_exit_notified_at' => Carbon::now(),
        ])->save();
    }

    /**
     * Catat perpindahan status ke riwayat geofence.
     *
     * Pengingat "masih di luar" sengaja TIDAK dicatat - riwayat hanya
     * berisi perpindahan, supaya jumlah barisnya mencerminkan kejadian
     * nyata, bukan frekuensi pengingat.
     */
    protected function recordHistory(
        Device $device,
        Geofence $geofence,
        string $event,
        array $result,
        array $payload,
        ?string $searchAddress
    ): GeofenceHistory {

        $previousStateChangedAt = $result['previous_state_changed_at'] ?? null;

        $durationSeconds = $previousStateChangedAt instanceof CarbonInterface
            ? max(0, $previousStateChangedAt->diffInSeconds(Carbon::now()))
            : null;

        return GeofenceHistory::create([

            'device_id' => $device->id,

            'geofence_id' => $geofence->id,

            'geofence_name' => $geofence->name,

            'geofence_type' => $geofence->type,

            'event' => $event,

            'location' => [
                'lat' => (float) $payload['lat'],
                'lng' => (float) $payload['lng'],
            ],

            'search_address' => $searchAddress,

            'occurred_at' => Carbon::now(),

            'duration_seconds' => $durationSeconds,

        ]);
    }

    /**
     * Pengaturan pengingat, sudah dinormalisasi dan dibatasi.
     *
     * @return array{enabled: bool, minutes: int}
     */
    public function repeatSetting(Device $device): array
    {
        $setting = $device->geofence_setting ?? [];

        $minutes = (int) ($setting['repeat_minutes'] ?? self::DEFAULT_REPEAT_MINUTES);

        return [

            'enabled' => (bool) ($setting['repeat_enabled'] ?? false),

            'minutes' => max(
                self::MIN_REPEAT_MINUTES,
                min(self::MAX_REPEAT_MINUTES, $minutes)
            ),

        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function notificationPayload(
        array $payload,
        ?string $searchAddress
    ): array {

        return [

            'lat' => $payload['lat'],

            'lng' => $payload['lng'],

            'search_address' => $searchAddress,

        ];
    }

    protected function minutesSince(?CarbonInterface $since): int
    {
        if (! $since instanceof CarbonInterface) {

            return 0;
        }

        return (int) floor(
            max(0, $since->diffInSeconds(Carbon::now())) / 60
        );
    }
}
