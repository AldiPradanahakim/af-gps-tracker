<?php

namespace App\Services\Home;

use App\Models\User;

class HomeService
{
    public function index(User $user): array
    {
        $user->load([

            'devices.vehicle',

            'devices.geofences',

            'devices.notifications' => function ($query) {

                $query->latest()->take(10);
            },

            'devices.deviceLogs' => function ($query) {

                $query->latest()->take(1);
            },

        ]);

        /*
        |--------------------------------------------------------------------------
        | Devices
        |--------------------------------------------------------------------------
        */

        $devices = $user->devices->map(function ($device) {

            return [

                'id' => $device->id,

                'device_code' => $device->device_id,

                'vehicle_name' => $device->vehicle?->vehicle_name,

                'vehicle_type' => $device->vehicle?->vehicle_type,

                'plate_number' => $device->vehicle?->plate_number,

                /*
                 | Home Location
                 | sementara null.
                 | nanti otomatis terisi setelah fitur
                 | Home Location dibuat.
                 */

                'home_location' => null,

            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Vehicles (Map Marker)
        |--------------------------------------------------------------------------
        */

        $vehicles = $user->devices->map(function ($device) {

            $vehicle = $device->vehicle;

            $latestLog = $device->deviceLogs->first();

            $payload = $latestLog?->payload ?? [];

            return [

                'device_id' => $device->id,

                'device_code' => $device->device_id,

                'vehicle_name' => $vehicle?->vehicle_name,

                'vehicle_type' => $vehicle?->vehicle_type,

                'plate_number' => $vehicle?->plate_number,

                'is_active' => $device->is_active,

                'last_heartbeat' => $device->last_heartbeat,

                'activated_at' => $device->activated_at,

                'latitude' => $payload['latitude'] ?? -6.914744,

                'longitude' => $payload['longitude'] ?? 107.609810,

                'speed' => $payload['speed'] ?? 0,

                'heading' => $payload['heading'] ?? 0,

                'battery' => $payload['battery'] ?? 100,

                'received_at' => $latestLog?->received_at,

            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Notifications
        |--------------------------------------------------------------------------
        */

        $notifications = $user->devices

            ->flatMap(fn($device) => $device->notifications)

            ->sortByDesc('created_at')

            ->take(10)

            ->values();

        /*
        |--------------------------------------------------------------------------
        | Geofences
        |--------------------------------------------------------------------------
        */

        $geofences = $user->devices

            ->flatMap(fn($device) => $device->geofences)

            ->values();

        return [

            'user' => $user,

            'devices' => $devices,

            'vehicles' => $vehicles,

            'notifications' => $notifications,

            'geofences' => $geofences,

        ];
    }
}
