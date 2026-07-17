<?php

namespace App\Repositories;

use App\Models\Device;
use App\Models\User;
use Illuminate\Support\Collection;

class SearchRepository
{
    /**
     * Mencari kendaraan milik user.
     */
    public function searchVehicle(User $user, string $keyword): Collection
    {
        return Device::query()

            ->with('vehicle')

            ->where('user_id', $user->id)

            ->whereHas('vehicle', function ($query) use ($keyword) {

                $query

                    ->where('vehicle_name', 'LIKE', "%{$keyword}%")

                    ->orWhere('plate_number', 'LIKE', "%{$keyword}%");
            })

            ->get()

            ->map(function ($device) {

                $vehicle = $device->vehicle;

                return [

                    'type' => 'vehicle',

                    'id' => $device->id,

                    'device_id' => $device->device_id,

                    'title' => $vehicle?->vehicle_name,

                    'subtitle' => $vehicle?->plate_number,

                    'vehicle_name' => $vehicle?->vehicle_name,

                    'plate_number' => $vehicle?->plate_number,

                    'vehicle_type' => $vehicle?->vehicle_type,

                ];
            })

            ->values();
    }

    /**
     * Mengambil device berdasarkan id.
     */
    public function findDevice(int $deviceId): ?Device
    {
        return Device::query()

            ->with('vehicle')

            ->find($deviceId);
    }
}
