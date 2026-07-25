<?php

namespace App\Services\Home;

use App\Models\User;
use App\Services\Home\Support\VehicleCardFormatter;

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

            'devices.travelHistories' => function ($query) {

                $query->latest()->take(1);
            },

        ]);

        /*
|--------------------------------------------------------------------------
| Devices
|--------------------------------------------------------------------------
*/

        $devices = $user->devices->map(function ($device) {

            $lastLocation = $device->travelHistories->first()?->location;

            return [

                'id' => $device->id,

                'device_id' => $device->device_id,

                'vehicle_name' => $device->vehicle?->vehicle_name,

                'vehicle_type' => $device->vehicle?->vehicle_type,

                'plate_number' => $device->vehicle?->plate_number,

                /*
        |--------------------------------------------------------------------------
        | Home Location
        |--------------------------------------------------------------------------
        */

                'home_location' => $device->home_location,

                /*
        |--------------------------------------------------------------------------
        | Last GPS Location
        |--------------------------------------------------------------------------
        */

                'last_location' => [

                    'latitude' => data_get($lastLocation, 'lat'),

                    'longitude' => data_get($lastLocation, 'lng'),

                ],

            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Vehicles (Map Marker)
        |--------------------------------------------------------------------------
        */

        $vehicles = $user->devices->map(

            fn($device) => VehicleCardFormatter::make($device)

        )->values();

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
