<?php

namespace App\Services\Home\Support;

use App\Models\Device;

class VehicleCardFormatter
{
    /**
     * Format Device menjadi data Vehicle Card
     * yang digunakan oleh Sidebar, Map dan Realtime.
     */
    public static function make(Device $device): array
    {
        $vehicle = $device->vehicle;

        $latestLog = $device->deviceLogs()
            ->latest('received_at')
            ->latest('id')
            ->first();

        $payload = $latestLog?->payload ?? [];

        $payload = $latestLog?->payload ?? [];

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
            | Device Status
            |--------------------------------------------------------------------------
            */

            'is_active' => (bool) $device->is_active,

            'last_heartbeat' => $device->last_heartbeat,

            'activated_at' => $device->activated_at,

            /*
|--------------------------------------------------------------------------
| GPS
|--------------------------------------------------------------------------
*/

            'latitude' => isset($payload['latitude'])
                ? (float) $payload['latitude']
                : (isset($payload['lat'])
                    ? (float) $payload['lat']
                    : null),

            'longitude' => isset($payload['longitude'])
                ? (float) $payload['longitude']
                : (isset($payload['lng'])
                    ? (float) $payload['lng']
                    : null),

            'lat' => isset($payload['lat'])
                ? (float) $payload['lat']
                : (isset($payload['latitude'])
                    ? (float) $payload['latitude']
                    : null),

            'lng' => isset($payload['lng'])
                ? (float) $payload['lng']
                : (isset($payload['longitude'])
                    ? (float) $payload['longitude']
                    : null),

            'speed' => isset($payload['speed'])
                ? (float) $payload['speed']
                : 0,

            'heading' => isset($payload['heading'])
                ? (float) $payload['heading']
                : 0,

            /*
            |--------------------------------------------------------------------------
            | Battery
            |--------------------------------------------------------------------------
            */

            'battery' => isset($payload['battery'])
                ? (int) $payload['battery']
                : 100,

            'satellite' => isset($payload['satellite'])
                ? (int) $payload['satellite']
                : 0,

            /*
            |--------------------------------------------------------------------------
            | Address
            |--------------------------------------------------------------------------
            */

            'search_address' =>

            $payload['search_address']

                ?? null,

            /*
            |--------------------------------------------------------------------------
            | Geofence
            |--------------------------------------------------------------------------
            */

            'inside_geofence' => (bool) (

                $payload['inside_geofence']

                ?? false

            ),

            'entered_geofence' => (bool) (

                $payload['entered_geofence']

                ?? false

            ),

            'exited_geofence' => (bool) (

                $payload['exited_geofence']

                ?? false

            ),

            'geofence_changed' => (bool) (

                $payload['geofence_changed']

                ?? false

            ),

            'geofence_name' =>

            $payload['geofence_name']

                ?? null,

            /*
            |--------------------------------------------------------------------------
            | Log
            |--------------------------------------------------------------------------
            */

            'received_at' => optional(

                $latestLog?->received_at

            )->toISOString(),

        ];
    }
}
