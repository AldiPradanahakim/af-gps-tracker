<?php

namespace App\Services\Home;

use App\Models\User;
use App\Services\Home\Support\VehicleCardFormatter;
use App\Services\Notification\NotificationService;

class HomeService
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function index(User $user): array
    {
        $user->load([

            'devices.vehicle',

            'devices.geofences',

            'devices.deviceLogs' => function ($query) {

                $query->latest('received_at')->latest('id')->take(1);
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
        |
        | Hanya notification yang belum dibaca. Setelah dibaca (atau di
        | halaman lain), notification tidak ditampilkan lagi saat
        | refresh/login/masuk halaman.
        |
        */

        $notifications = $this->notificationService->unreadForUser(
            $user
        );

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
