<?php

namespace App\Services\Geofence;

use App\Models\Geofence;
use InvalidArgumentException;

class RadiusGeofenceService
{
    /**
     * Earth's radius in meters.
     */
    protected const EARTH_RADIUS = 6371000;

    /**
     * Determine whether a coordinate is inside
     * a radius geofence.
     *
     * @throws InvalidArgumentException
     */
    public function contains(
        Geofence $geofence,
        float $latitude,
        float $longitude
    ): bool {

        if ($geofence->type !== 'radius') {

            throw new InvalidArgumentException(
                'Tipe geofence harus radius.'
            );
        }

        $config = $this->validateConfig(
            $geofence->config
        );

        $distance = $this->calculateDistance(

            $latitude,
            $longitude,

            $config['center']['lat'],
            $config['center']['lng']

        );

        return $distance <= $config['radius'];
    }

    /**
     * Calculate distance between two coordinates
     * using Haversine Formula.
     */
    public function calculateDistance(
        float $latitude1,
        float $longitude1,
        float $latitude2,
        float $longitude2
    ): float {

        $latitude1 = deg2rad($latitude1);
        $longitude1 = deg2rad($longitude1);

        $latitude2 = deg2rad($latitude2);
        $longitude2 = deg2rad($longitude2);

        $latitudeDelta = $latitude2 - $latitude1;
        $longitudeDelta = $longitude2 - $longitude1;

        $a =

            sin($latitudeDelta / 2) *
            sin($latitudeDelta / 2)

            +

            cos($latitude1)
            *
            cos($latitude2)
            *
            sin($longitudeDelta / 2)
            *
            sin($longitudeDelta / 2);

        $c =

            2
            *
            atan2(
                sqrt($a),
                sqrt(1 - $a)
            );

        return self::EARTH_RADIUS * $c;
    }

    /**
     * Validate radius configuration.
     *
     * Expected format:
     *
     * [
     *     "center" => [
     *         "lat" => -6.xxx,
     *         "lng" => 107.xxx
     *     ],
     *     "radius" => 500
     * ]
     *
     * @throws InvalidArgumentException
     */
    protected function validateConfig(
        array $config
    ): array {

        if (! isset($config['center'])) {

            throw new InvalidArgumentException(
                'Radius center is required.'
            );
        }

        if (! isset($config['center']['lat'])) {

            throw new InvalidArgumentException(
                'Center latitude is required.'
            );
        }

        if (! isset($config['center']['lng'])) {

            throw new InvalidArgumentException(
                'Center longitude is required.'
            );
        }

        if (! isset($config['radius'])) {

            throw new InvalidArgumentException(
                'Radius is required.'
            );
        }

        if ($config['radius'] <= 0) {

            throw new InvalidArgumentException(
                'Radius must be greater than zero.'
            );
        }

        return $config;
    }

    /**
     * Get configured radius.
     */
    public function getRadius(
        Geofence $geofence
    ): float {

        $config = $this->validateConfig(
            $geofence->config
        );

        return (float) $config['radius'];
    }

    /**
     * Get configured center point.
     */
    public function getCenter(
        Geofence $geofence
    ): array {

        $config = $this->validateConfig(
            $geofence->config
        );

        return [

            'lat' => (float) $config['center']['lat'],

            'lng' => (float) $config['center']['lng'],

        ];
    }

    /**
     * Get current distance from center.
     */
    public function getDistance(
        Geofence $geofence,
        float $latitude,
        float $longitude
    ): float {

        $center = $this->getCenter(
            $geofence
        );

        return $this->calculateDistance(

            $latitude,
            $longitude,

            $center['lat'],
            $center['lng']

        );
    }
}
