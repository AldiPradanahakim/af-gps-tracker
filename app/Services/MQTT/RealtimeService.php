<?php

namespace App\Services\MQTT;

use App\Events\VehicleLocationUpdated;
use App\Models\Device;
use App\Models\TravelHistory;

class RealtimeService
{
    /**
     * Broadcast realtime location.
     *
     * Broadcast dilakukan untuk SETIAP payload MQTT
     * yang bukan duplicate message.
     *
     * Broadcast TIDAK bergantung pada DeviceLog
     * maupun TravelHistory.
     */
    public function broadcast(
        Device $device,
        array $payload,
        array $geofenceResult = [],
        ?TravelHistory $travelHistory = null,
        ?string $searchAddress = null
    ): void {

        event(
            new VehicleLocationUpdated(
                deviceId: (string) $device->id,

                payload: $this->payload(
                    $device,
                    $payload,
                    $geofenceResult,
                    $travelHistory,
                    $searchAddress
                )
            )
        );
    }

    /**
     * Build realtime payload.
     */
    protected function payload(
        Device $device,
        array $payload,
        array $geofenceResult,
        ?TravelHistory $travelHistory,
        ?string $searchAddress
    ): array {

        $vehicle = $device->vehicle;

        /*
        |--------------------------------------------------------------------------
        | Latest GPS Position
        |--------------------------------------------------------------------------
        |
        | PENTING:
        |
        | Realtime SELALU menggunakan payload MQTT terbaru.
        |
        | Jangan menggunakan TravelHistory sebagai sumber posisi
        | karena TravelHistory hanya dibuat ketika posisi melewati
        | threshold persistence.
        |
        */

        $latitude = (float) ($payload['lat'] ?? 0);

        $longitude = (float) ($payload['lng'] ?? 0);

        $speed = (float) ($payload['speed'] ?? 0);

        $heading = (float) ($payload['heading'] ?? 0);

        $battery = (int) ($payload['battery'] ?? 0);

        $satellite = (int) ($payload['satellite'] ?? 0);

        /*
        |--------------------------------------------------------------------------
        | Address
        |--------------------------------------------------------------------------
        */

        $address =
            $searchAddress
            ??
            $travelHistory?->search_address;

        /*
        |--------------------------------------------------------------------------
        | Received At
        |--------------------------------------------------------------------------
        */

        $receivedAt = null;

        if (! empty($payload['received_at'])) {

            try {

                $receivedAt = \App\Helpers\GpsTimestampParser::parse(
                    $payload['received_at']
                )->toISOString();

            } catch (\Throwable) {

                $receivedAt = now()->toISOString();
            }
        } else {

            $receivedAt = now()->toISOString();
        }

        /*
        |--------------------------------------------------------------------------
        | Distance / Odometer (jika dikirim oleh ESP32)
        |--------------------------------------------------------------------------
        */

        $distance = null;

        if (isset($payload['total_distance'])) {
            $distance = (float) $payload['total_distance'];
        } elseif (isset($payload['distance'])) {
            $distance = (float) $payload['distance'];
        } elseif (isset($payload['odometer'])) {
            $distance = (float) $payload['odometer'];
        }

        /*
        |--------------------------------------------------------------------------
        | Broadcast Payload
        |--------------------------------------------------------------------------
        */

        return [

            /*
            |--------------------------------------------------------------------------
            | Device
            |--------------------------------------------------------------------------
            */

            'device_id' => (string) $device->id,

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

            'is_online' => (bool) $device->is_online,

            /*
            |--------------------------------------------------------------------------
            | GPS
            |--------------------------------------------------------------------------
            */

            'latitude' => $latitude,

            'longitude' => $longitude,

            'lat' => $latitude,

            'lng' => $longitude,

            /*
            |--------------------------------------------------------------------------
            | GPS Data
            |--------------------------------------------------------------------------
            */

            'speed' => $speed,

            'heading' => $heading,

            'battery' => $battery,

            'satellite' => $satellite,

            'total_distance' => $distance,

            'distance' => $distance,

            /*
            |--------------------------------------------------------------------------
            | Complete Location
            |--------------------------------------------------------------------------
            */

            'location' => [

                'lat' => $latitude,

                'lng' => $longitude,

                'speed' => $speed,

                'heading' => $heading,

                'battery' => $battery,

                'satellite' => $satellite,

                'received_at' => $receivedAt,

                'total_distance' => $distance,

            ],

            /*
            |--------------------------------------------------------------------------
            | Address
            |--------------------------------------------------------------------------
            */

            'search_address' => $address,

            /*
            |--------------------------------------------------------------------------
            | MQTT Message
            |--------------------------------------------------------------------------
            */

            'message_id' => $payload['message_id'] ?? null,

            /*
            |--------------------------------------------------------------------------
            | Time
            |--------------------------------------------------------------------------
            */

            'received_at' => $receivedAt,

            'updated_at' => $receivedAt,

            /*
            |--------------------------------------------------------------------------
            | Geofence
            |--------------------------------------------------------------------------
            */

            'inside_geofence' =>
                (bool) (
                    $geofenceResult['inside']
                    ?? false
                ),

            'entered_geofence' =>
                (bool) (
                    $geofenceResult['entered']
                    ?? false
                ),

            'exited_geofence' =>
                (bool) (
                    $geofenceResult['exited']
                    ?? false
                ),

            'geofence_changed' =>
                (bool) (
                    $geofenceResult['has_changed']
                    ?? false
                ),

            'geofence_name' =>
                ($geofenceResult['geofence'] ?? null)?->name,

        ];
    }
}