<?php

namespace App\Services\Device;

use App\Models\Device;

class OverspeedDetectionService
{
    /**
     * Deteksi transisi normal -> overspeed. Notifikasi hanya dikirim
     * saat KECEPATAN MELINTASI batas (bukan setiap titik selama masih
     * di atas batas), memakai state device.is_inside_geofence-style
     * pattern: di sini disimpan lewat notification terakhir bertipe
     * overspeed yang belum "ditutup" oleh titik di bawah batas.
     *
     * Return true hanya pada titik transisi (saat notifikasi harus
     * dikirim), false selebihnya (termasuk saat fitur nonaktif, masih
     * di bawah batas, atau sudah pernah dinotifikasi untuk episode
     * overspeed yang sama).
     */
    public function process(
        Device $device,
        array $payload
    ): bool {

        $setting = $this->extractSetting(
            $device
        );

        if (! $setting['enabled']) {
            return false;
        }

        $speed = (float) ($payload['speed'] ?? 0);

        $isOverspeed = $speed > $setting['limit_kmh'];

        $wasOverspeed = (bool) $device->overspeed_active;

        if ($isOverspeed && ! $wasOverspeed) {

            $device->update([
                'overspeed_active' => true,
            ]);

            return true;
        }

        if (! $isOverspeed && $wasOverspeed) {

            $device->update([
                'overspeed_active' => false,
            ]);
        }

        return false;
    }

    /**
     * Extract pengaturan overspeed.
     */
    protected function extractSetting(
        Device $device
    ): array {

        $setting = $device->speed_setting ?? [];

        return [

            'enabled' => (bool) (
                $setting['enabled']
                ?? false
            ),

            'limit_kmh' => (int) (
                $setting['limit_kmh']
                ?? 80
            ),

        ];
    }

    /**
     * Check apakah overspeed detection aktif untuk device ini.
     */
    public function isEnabled(
        Device $device
    ): bool {

        return $this->extractSetting(
            $device
        )['enabled'];
    }
}
