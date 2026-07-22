<?php

namespace App\Repositories;

use App\Models\Device;
use App\Models\Geofence;
use Illuminate\Database\Eloquent\Collection;

class GeofenceRepository
{
    /**
     * Seluruh geofence milik user.
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
     * Geofence berdasarkan device.
     */
    public function getByDevice(int $deviceId): Collection
    {
        return Geofence::query()
            ->where('device_id', $deviceId)
            ->latest()
            ->get();
    }

    /**
     * Cari geofence.
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
     * Cari geofence milik user.
     */
    public function findOwnedByUser(
        int $id,
        int $userId
    ): ?Geofence {

        return Geofence::query()

            ->whereKey($id)

            ->whereHas('device', function ($query) use ($userId) {

                $query->where('user_id', $userId);
            })

            ->first();
    }

    /**
     * Cari device.
     */
    public function findDevice(
        int $deviceId
    ): ?Device {

        return Device::query()

            ->with([
                'vehicle',
            ])

            ->find($deviceId);
    }

    /**
     * Cari device milik user.
     */
    public function findDeviceByUser(
        int $deviceId,
        int $userId
    ): ?Device {

        return Device::query()

            ->with([
                'vehicle',
            ])

            ->whereKey($deviceId)

            ->where('user_id', $userId)

            ->first();
    }

    /**
     * Simpan geofence.
     */
    public function create(
        array $data
    ): Geofence {

        return Geofence::create($data);
    }

    /**
     * Update geofence.
     */
    public function update(
        Geofence $geofence,
        array $data
    ): Geofence {

        $geofence->update($data);

        return $geofence->refresh();
    }

    /**
     * Update status.
     */
    public function updateStatus(
        Geofence $geofence,
        bool $status
    ): Geofence {

        $geofence->update([

            'status' => $status,

        ]);

        return $geofence->refresh();
    }

    /**
     * Hapus satu geofence.
     */
    public function delete(
        Geofence $geofence
    ): bool {

        return (bool) $geofence->delete();
    }

    /**
     * Hapus banyak geofence.
     */
    public function deleteMany(
        array $ids
    ): int {

        return Geofence::query()

            ->whereIn(
                'id',
                $ids
            )

            ->delete();
    }
}
