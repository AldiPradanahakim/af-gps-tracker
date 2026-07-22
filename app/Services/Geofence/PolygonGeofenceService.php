<?php

namespace App\Services\Geofence;

use App\Models\Geofence;
use InvalidArgumentException;

class PolygonGeofenceService
{
    /**
     * Determine whether coordinate is inside polygon.
     *
     * Supports:
     * - administrative
     * - custom
     *
     * @throws InvalidArgumentException
     */
    public function contains(
        Geofence $geofence,
        float $latitude,
        float $longitude
    ): bool {

        if (! in_array(
            $geofence->type,
            ['administrative', 'custom']
        )) {

            throw new InvalidArgumentException(
                'Geofence must be administrative or custom type.'
            );
        }

        $polygon = $this->extractPolygon(
            $geofence->config
        );

        return $this->pointInPolygon(
            $latitude,
            $longitude,
            $polygon
        );
    }

    /**
     * Ray Casting Algorithm.
     */
    protected function pointInPolygon(
        float $latitude,
        float $longitude,
        array $polygon
    ): bool {

        $inside = false;

        $total = count($polygon);

        if ($total < 3) {

            return false;
        }

        $j = $total - 1;

        for ($i = 0; $i < $total; $i++) {

            $latI = (float) $polygon[$i][1];
            $lngI = (float) $polygon[$i][0];

            $latJ = (float) $polygon[$j][1];
            $lngJ = (float) $polygon[$j][0];

            $intersect =

                (($latI > $latitude) !== ($latJ > $latitude))

                &&

                (

                    $longitude

                    <

                    ($lngJ - $lngI)

                    *

                    ($latitude - $latI)

                    /

                    (($latJ - $latI) ?: 0.000000001)

                    +

                    $lngI

                );

            if ($intersect) {

                $inside = ! $inside;
            }

            $j = $i;
        }

        return $inside;
    }

    /**
     * Extract polygon coordinates
     * from GeoJSON.
     *
     * @throws InvalidArgumentException
     */
    protected function extractPolygon(
        array $config
    ): array {

        if (! isset($config['geojson'])) {

            throw new InvalidArgumentException(
                'GeoJSON is required.'
            );
        }

        $geojson = $config['geojson'];

        if (
            ! isset($geojson['geometry'])
        ) {

            throw new InvalidArgumentException(
                'Invalid GeoJSON.'
            );
        }

        if (
            ! isset($geojson['geometry']['type'])
        ) {

            throw new InvalidArgumentException(
                'Geometry type is required.'
            );
        }

        if (
            $geojson['geometry']['type'] !== 'Polygon'
        ) {

            throw new InvalidArgumentException(
                'Only Polygon is supported.'
            );
        }

        if (
            ! isset($geojson['geometry']['coordinates'][0])
        ) {

            throw new InvalidArgumentException(
                'Polygon coordinates are required.'
            );
        }

        return $geojson['geometry']['coordinates'][0];
    }

    /**
     * Return polygon coordinates.
     */
    public function getPolygon(
        Geofence $geofence
    ): array {

        return $this->extractPolygon(
            $geofence->config
        );
    }

    /**
     * Determine polygon total points.
     */
    public function totalPoints(
        Geofence $geofence
    ): int {

        return count(

            $this->extractPolygon(
                $geofence->config
            )

        );
    }

    /**
     * Determine whether GeoJSON
     * is valid.
     */
    public function isValid(
        Geofence $geofence
    ): bool {

        try {

            $polygon = $this->extractPolygon(
                $geofence->config
            );

            return count($polygon) >= 3;
        } catch (\Throwable $e) {

            return false;
        }
    }

    /**
     * Return polygon bounding box.
     */
    public function getBoundingBox(
        Geofence $geofence
    ): array {

        $polygon = $this->extractPolygon(
            $geofence->config
        );

        $latitudes = [];
        $longitudes = [];

        foreach ($polygon as $point) {

            $longitudes[] = (float) $point[0];
            $latitudes[] = (float) $point[1];
        }

        return [

            'min_lat' => min($latitudes),

            'max_lat' => max($latitudes),

            'min_lng' => min($longitudes),

            'max_lng' => max($longitudes),

        ];
    }

    /**
     * Quick bounding-box filter.
     */
    public function insideBoundingBox(
        Geofence $geofence,
        float $latitude,
        float $longitude
    ): bool {

        $bbox = $this->getBoundingBox(
            $geofence
        );

        return

            $latitude >= $bbox['min_lat']

            &&

            $latitude <= $bbox['max_lat']

            &&

            $longitude >= $bbox['min_lng']

            &&

            $longitude <= $bbox['max_lng'];
    }
}
