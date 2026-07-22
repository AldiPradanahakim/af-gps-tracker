<?php

namespace App\Services\Geofence;

use App\Models\Device;
use App\Models\Geofence;

class GeofenceCheckerService
{
    public function __construct(
        protected RadiusGeofenceService $radiusGeofenceService,
        protected PolygonGeofenceService $polygonGeofenceService,
    ) {}

    /**
     * Process geofence.
     *
     * Return:
     * [
     *      'geofence' => ?Geofence,
     *      'inside' => bool,
     *      'previous_inside' => bool,
     *      'has_changed' => bool,
     *      'entered' => bool,
     *      'exited' => bool,
     * ]
     */
    public function process(
        Device $device,
        array $payload
    ): array {

        $geofence = $this->getActiveGeofence($device);

        if (! $geofence) {

            return [

                'geofence' => null,

                'inside' => false,

                'previous_inside' => false,

                'has_changed' => false,

                'entered' => false,

                'exited' => false,

            ];
        }

        $latitude = (float) $payload['lat'];

        $longitude = (float) $payload['lng'];

        $inside = $this->isInside(

            $geofence,

            $latitude,

            $longitude

        );

        $previousInside = (bool) $device->is_inside_geofence;

        $entered =

            ! $previousInside

            &&

            $inside;

        $exited =

            $previousInside

            &&

            ! $inside;

        $hasChanged =

            $entered

            ||

            $exited;

        if ($hasChanged) {

            $device->update([

                'is_inside_geofence' => $inside,

            ]);
        }

        return [

            'geofence' => $geofence,

            'inside' => $inside,

            'previous_inside' => $previousInside,

            'has_changed' => $hasChanged,

            'entered' => $entered,

            'exited' => $exited,

        ];
    }

    /**
     * Determine whether GPS coordinate
     * is inside geofence.
     */
    public function isInside(
        Geofence $geofence,
        float $latitude,
        float $longitude
    ): bool {

        return match ($geofence->type) {

            'radius'

            =>

            $this->radiusGeofenceService
                ->contains(

                    $geofence,

                    $latitude,

                    $longitude

                ),

            'administrative',

            'custom'

            =>

            $this->polygonGeofenceService
                ->contains(

                    $geofence,

                    $latitude,

                    $longitude

                ),

            default => false,
        };
    }

    /**
     * Get active geofence.
     */
    protected function getActiveGeofence(
        Device $device
    ): ?Geofence {

        if (! $device->relationLoaded('geofences')) {

            $device->load('geofences');
        }

        return $device->geofences

            ->where('status', true)

            ->first();
    }

    /**
     * Check whether device has active geofence.
     */
    public function hasActiveGeofence(
        Device $device
    ): bool {

        return $this->getActiveGeofence($device) !== null;
    }

    /**
     * Return active geofence.
     */
    public function activeGeofence(
        Device $device
    ): ?Geofence {

        return $this->getActiveGeofence($device);
    }
}
