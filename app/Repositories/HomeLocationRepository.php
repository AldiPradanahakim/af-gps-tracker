<?php

namespace App\Repositories;

use App\Models\Device;
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
    public function save(int $deviceId, array $data): Device
    {
        $device = Device::findOrFail($deviceId);

        $device->update([

            'home_location' => [

                'lat'          => (float) $data['latitude'],

                'lng'          => (float) $data['longitude'],

                'display_name' => $data['display_name'],

            ],

        ]);

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
    public function saveToAll(int $userId, array $data): Collection
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

        });

        return $devices->fresh();
    }

    /**
     * ----------------------------------------------------------
     * Delete Home Location — set home_location = null.
     * Tidak menghapus Device.
     * ----------------------------------------------------------
     */
    public function delete(int $deviceId): Device
    {
        $device = Device::findOrFail($deviceId);

        $device->update([

            'home_location' => null,

        ]);

        return $device->fresh();
    }
}
