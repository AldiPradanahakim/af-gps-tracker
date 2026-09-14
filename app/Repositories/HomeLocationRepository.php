<?php

namespace App\Repositories;

use App\Models\Device;
use Illuminate\Database\Eloquent\Collection;

class HomeLocationRepository
{
    /**
     * ----------------------------------------------------------
     * Simpan Lokasi Rumah untuk satu Device.
     *
     * Format JSON yang disimpan:
     *   { "lat": float, "lng": float, "display_name": string }
     *
     * CATATAN PENTING
     * Memindahkan Lokasi Rumah TIDAK ikut memindahkan titik pusat
     * geofence radius yang sudah terlanjur dibuat. Geofence adalah
     * area pengawasan yang sudah disetujui pemilik kendaraan -
     * menggesernya diam-diam bisa membuat kendaraan mendadak
     * dianggap keluar/masuk area tanpa pernah bergerak.
     *
     * Titik pusat hanya berpindah kalau pengguna membuka Edit
     * Radius dan memilih ulang sumber "Lokasi Rumah".
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

        return $device->fresh();
    }

    /**
     * ----------------------------------------------------------
     * Simpan Lokasi Rumah yang sama ke SEMUA Device milik User
     * yang belum memiliki Lokasi Rumah.
     *
     * Dipakai oleh halaman Utama (opsi "Semua Kendaraan").
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

        });

        return $devices->fresh();
    }

    /**
     * ----------------------------------------------------------
     * Hapus Lokasi Rumah — set home_location = null.
     * Tidak menghapus Device, dan tidak mengubah geofence yang
     * sudah dibuat (lihat catatan pada save()).
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
