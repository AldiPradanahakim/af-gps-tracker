<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Services\Notification\NotificationService;
use Illuminate\Console\Command;

class DeviceHealthCheckCommand extends Command
{
    /**
     * Artisan Command.
     */
    protected $signature = 'device:health-check';

    /**
     * Command Description.
     */
    protected $description = 'Deteksi device aktif yang berhenti mengirim data (offline) dan kirim notifikasi.';

    public function handle(NotificationService $notificationService): int
    {
        /*
        |--------------------------------------------------------------------------
        | Kandidat Offline
        |--------------------------------------------------------------------------
        |
        | Device aktif, heartbeat sudah lewat ambang batas (selaras
        | dengan Device::ONLINE_THRESHOLD_MINUTES), dan belum pernah
        | dinotifikasi untuk episode offline saat ini
        | (offline_notified_at masih null - direset ke null lagi begitu
        | heartbeat baru masuk, lihat GPSProcessingService).
        |--------------------------------------------------------------------------
        */

        $thresholdMinutes = (int) config('mqtt.alerts.offline_threshold_minutes');

        $candidates = Device::query()

            ->where('is_active', true)

            ->whereNull('offline_notified_at')

            ->where(function ($query) use ($thresholdMinutes) {

                $query->whereNull('last_heartbeat')

                    ->orWhere(
                        'last_heartbeat',
                        '<',
                        now()->subMinutes($thresholdMinutes)
                    );
            })

            ->get();

        foreach ($candidates as $device) {

            $notificationService->createDeviceOfflineNotification(
                $device
            );

            $device->update([
                'offline_notified_at' => now(),
            ]);
        }

        $this->info(
            sprintf(
                'Device health check selesai. %d device ditandai offline.',
                $candidates->count()
            )
        );

        return self::SUCCESS;
    }
}
