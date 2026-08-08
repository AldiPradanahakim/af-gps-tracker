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
    public function getByUser(string $userId): Collection
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
    public function getByDevice(string $deviceId): Collection
    {
        return Geofence::query()
            ->where('device_id', $deviceId)
            ->latest()
            ->get();
    }

    /**
     * Cari geofence.
     */
    public function find(string $id): ?Geofence
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
        string $id,
        string $userId
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
        string $deviceId
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
        string $deviceId,
        string $userId
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
     * Semua device milik user (untuk opsi "Semua Kendaraan").
     */
    public function getDevicesByUser(string $userId): Collection
    {
        return Device::query()

            ->with(['vehicle'])

            ->where('user_id', $userId)

            ->orderBy('id')

            ->get();
    }

    /**
     * Cek apakah device sudah memiliki geofence dengan tipe tertentu.
     * (BR-01: 1 device maksimal 1 geofence per tipe)
     */
    public function hasType(string $deviceId, string $type): bool
    {
        return Geofence::query()

            ->where('device_id', $deviceId)

            ->where('type', $type)

            ->exists();
    }

    /**
     * Daftar tipe geofence yang sudah dimiliki tiap device milik user.
     * Bentuk: [deviceId => ['radius', 'administrative', ...]]
     */
    public function getTypesByUser(string $userId): array
    {
        return Geofence::query()

            ->whereHas('device', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })

            ->get(['device_id', 'type'])

            ->groupBy('device_id')

            ->map(fn($items) => $items->pluck('type')->values()->all())

            ->all();
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
