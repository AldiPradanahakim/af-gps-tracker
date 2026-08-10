<?php

namespace App\Repositories;

use App\Models\Device;
use App\Models\Geofence;
use Illuminate\Database\Eloquent\Collection;

class HomeLocationRepository
{
    /**
     * ----------------------------------------------------------
     * Save Home Location untuk satu Device.
     *
     * Format JSON yang disimpan:
     *   { "lat": float, "lng": float, "display_name": string }
     * ----------------------------------------------------------
     */
    public function save(string $deviceId, array $data): Device
    {
        $device = Device::findOrFail($deviceId);

        $device->update([

            'home_location' => [

                'lat'          => (float) $data['latitude'],

                'lng'          => (float) $data['longitude'],

                'display_name' => $data['display_name'],

            ],

        ]);

        $this->syncHomeGeofence(
            $device,
            (float) $data['latitude'],
            (float) $data['longitude']
        );

        return $device->fresh();
    }

    /**
     * ----------------------------------------------------------
     * Save Home Location yang sama ke SEMUA Device milik User
     * yang belum memiliki Home Location.
     *
     * Dipakai oleh halaman Home (opsi "Semua Kendaraan").
     * ----------------------------------------------------------
     */
    public function saveToAll(string $userId, array $data): Collection
    {
        $devices = Device::where('user_id', $userId)
            ->whereNull('home_location')
            ->get();

        $devices->each(function (Device $device) use ($data) {

            $device->update([

                'home_location' => [

                    'lat'          => (float) $data['latitude'],

                    'lng'          => (float) $data['longitude'],

                    'display_name' => $data['display_name'],

                ],

            ]);

            $this->syncHomeGeofence(
                $device,
                (float) $data['latitude'],
                (float) $data['longitude']
            );

        });

        return $devices->fresh();
    }

    /**
     * ----------------------------------------------------------
     * Sinkronkan titik pusat geofence radius yang sumbernya
     * "home_location" agar ikut pindah saat Home Location diubah.
     * Tanpa ini, geofence radius tetap memakai koordinat lama
     * (snapshot saat geofence dibuat) walau Home Location sudah
     * diperbarui.
     * ----------------------------------------------------------
     */
    private function syncHomeGeofence(
        Device $device,
        float $latitude,
        float $longitude
    ): void {

        Geofence::where('device_id', $device->id)
            ->where('type', 'radius')
            ->get()
            ->each(function (Geofence $geofence) use ($latitude, $longitude) {

                $config = $geofence->config ?? [];

                if (($config['source'] ?? null) !== 'home_location') {
                    return;
                }

                $config['center'] = [
                    'lat' => $latitude,
                    'lng' => $longitude,
                ];

                $geofence->update(['config' => $config]);

            });
    }

    /**
     * ----------------------------------------------------------
     * Delete Home Location — set home_location = null.
     * Tidak menghapus Device.
     * ----------------------------------------------------------
     */
    public function delete(string $deviceId): Device
    {
        $device = Device::findOrFail($deviceId);

        $device->update([

            'home_location' => null,

        ]);

        return $device->fresh();
    }
}
