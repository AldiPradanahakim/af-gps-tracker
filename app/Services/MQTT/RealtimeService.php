<?php

namespace App\Services\MQTT;

use App\Events\VehicleLocationUpdated;
use App\Models\Device;
use App\Models\TravelHistory;

class RealtimeService
{
    /**
     * Broadcast realtime location.
     */
    public function broadcast(
        Device $device,
        TravelHistory $travelHistory,
        array $geofenceResult
    ): void {

        event(

            new VehicleLocationUpdated(

                deviceId: (string) $device->id,

                payload: $this->payload(

                    $device,

                    $travelHistory,

                    $geofenceResult

                )

            )

        );
    }

    /**
     * Build realtime payload.
     */
    protected function payload(
        Device $device,
        TravelHistory $travelHistory,
        array $geofenceResult
    ): array {

        $vehicle = $device->vehicle;

        $location = $travelHistory->location ?? [];

        return [

            /*
            |--------------------------------------------------------------------------
            | Device
            |--------------------------------------------------------------------------
            */

            'device_id' => $device->id,

            'device_code' => $device->device_id,

            /*
            |--------------------------------------------------------------------------
            | Vehicle
            |--------------------------------------------------------------------------
            */

            'vehicle_name' => $vehicle?->vehicle_name,

            'vehicle_type' => $vehicle?->vehicle_type,

            'plate_number' => $vehicle?->plate_number,

            /*
            |--------------------------------------------------------------------------
            | Marker
            |--------------------------------------------------------------------------
            */

            'marker_icon' => $vehicle?->marker_icon
                ?? 'car',

            'marker_color' => $vehicle?->marker_color
                ?? 'green',

            /*
            |--------------------------------------------------------------------------
            | GPS
            |--------------------------------------------------------------------------
            */

            'latitude' => (float) (

                $location['latitude']

                ?? $location['lat']

                ?? 0

            ),

            'longitude' => (float) (

                $location['longitude']

                ?? $location['lng']

                ?? 0

            ),

            'lat' => (float) (

                $location['lat']

                ?? $location['latitude']

                ?? 0

            ),

            'lng' => (float) (

                $location['lng']

                ?? $location['longitude']

                ?? 0

            ),

            'speed' => (float) (

                $location['speed']

                ?? 0

            ),

            'heading' => (float) (

                $location['heading']

                ?? 0

            ),

            'battery' => (int) (

                $location['battery']

                ?? 0

            ),

            'satellite' => (int) (

                $location['satellite']

                ?? 0

            ),

            'location' => $location,

            /*
            |--------------------------------------------------------------------------
            | Address
            |--------------------------------------------------------------------------
            */

            'search_address' => $travelHistory->search_address,

            /*
            |--------------------------------------------------------------------------
            | Time
            |--------------------------------------------------------------------------
            */

            'received_at' => optional(

                $travelHistory->received_at

            )->toISOString(),

            /*
            |--------------------------------------------------------------------------
            | Geofence
            |--------------------------------------------------------------------------
            */

            'inside_geofence' => $geofenceResult['inside'] ?? false,

            'entered_geofence' => $geofenceResult['entered'] ?? false,

            'exited_geofence' => $geofenceResult['exited'] ?? false,

            'geofence_changed' => $geofenceResult['has_changed'] ?? false,

            'geofence_name' => $geofenceResult['geofence']?->name,

        ];
    }
}
