<?php

namespace App\Repositories;

use App\Models\Device;
use App\Models\Geofence;
use Illuminate\Database\Eloquent\Collection;

class GeofenceRepository
{
    /**
     * Mengambil seluruh geofence milik user.
     */
    public function getByUser(int $userId): Collection
    {
        return Geofence::query()

            ->with([
                'device',
                'device.vehicle',
            ])

            ->whereHas('device', function ($query) use ($userId) {

                $query->where('user_id', $userId);
            })

            ->latest()

            ->get();
    }

    /**
     * Mengambil seluruh geofence berdasarkan device.
     */
    public function getByDevice(int $deviceId): Collection
    {
        return Geofence::query()

            ->where('device_id', $deviceId)

            ->latest()

            ->get();
    }

    /**
     * Mencari geofence berdasarkan id.
     */
    public function find(int $id): ?Geofence
    {
        return Geofence::query()

            ->with([
                'device',
                'device.vehicle',
            ])

            ->find($id);
    }

    /**
     * Menyimpan geofence baru.
     */
    public function create(array $data): Geofence
    {
        return Geofence::create($data);
    }

    /**
     * Update geofence.
     */
    public function update(Geofence $geofence, array $data): Geofence
    {
        $geofence->update($data);

        return $geofence->refresh();
    }

    /**
     * Mengaktifkan / Menonaktifkan geofence.
     */
    public function updateStatus(
        Geofence $geofence,
        bool $status
    ): bool {

        return $geofence->update([
            'status' => $status,
        ]);
    }

    /**
     * Menghapus geofence.
     */
    public function delete(Geofence $geofence): bool
    {
        return (bool) $geofence->delete();
    }

    /**
     * Mengambil device beserta kendaraan.
     */
    public function findDevice(int $deviceId): ?Device
    {
        return Device::query()

            ->with('vehicle')

            ->find($deviceId);
    }
}
