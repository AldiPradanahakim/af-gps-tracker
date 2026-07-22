<?php

namespace App\Services\Geofence;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Throwable;

class ReverseGeocodingService
{
    /**
     * Nominatim Reverse Geocoding API.
     */
    protected string $endpoint =
    'https://nominatim.openstreetmap.org/reverse';

    /**
     * Convert latitude & longitude into address.
     *
     * @throws InvalidArgumentException
     */
    public function search(
        float $latitude,
        float $longitude
    ): string {

        $this->validateCoordinate(
            $latitude,
            $longitude
        );

        try {

            $response = Http::timeout(10)

                ->withHeaders([

                    'User-Agent' => config('app.name') . '/1.0',

                    'Accept' => 'application/json',

                ])

                ->get($this->endpoint, [

                    'format' => 'jsonv2',

                    'lat' => $latitude,

                    'lon' => $longitude,

                    'addressdetails' => 1,

                ]);

            $response->throw();

            return $this->extractAddress(

                $response->json()

            );
        } catch (RequestException $exception) {

            report($exception);

            return 'Alamat tidak ditemukan';
        } catch (Throwable $exception) {

            report($exception);

            return 'Alamat tidak ditemukan';
        }
    }

    /**
     * Validate latitude and longitude.
     *
     * @throws InvalidArgumentException
     */
    protected function validateCoordinate(
        float $latitude,
        float $longitude
    ): void {

        if (
            $latitude < -90 ||
            $latitude > 90
        ) {

            throw new InvalidArgumentException(
                'Invalid latitude.'
            );
        }

        if (
            $longitude < -180 ||
            $longitude > 180
        ) {

            throw new InvalidArgumentException(
                'Invalid longitude.'
            );
        }
    }

    /**
     * Extract readable address.
     */
    protected function extractAddress(
        array $result
    ): string {

        return $result['display_name']
            ?? 'Alamat tidak ditemukan';
    }
}
