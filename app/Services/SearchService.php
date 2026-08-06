<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\SearchRepository;
use Illuminate\Support\Facades\Http;

class SearchService
{
    public function __construct(
        protected SearchRepository $searchRepository
    ) {}

    /**
     * Search kendaraan + alamat + wilayah administratif.
     */
    public function search(string $keyword, User $user): array
    {
        $vehicles = $this->searchRepository
            ->searchVehicle($user, $keyword)
            ->toArray();

        $locations = $this->searchLocation($keyword);

        return array_values(
            array_merge(
                $vehicles,
                $locations
            )
        );
    }

    /**
     * Search alamat / wilayah menggunakan Nominatim.
     */
    private function searchLocation(string $keyword): array
    {
        $response = Http::timeout(10)
            ->acceptJson()
            ->withHeaders([
                'User-Agent' => config('app.name') . '/1.0',
            ])
            ->get(
                'https://nominatim.openstreetmap.org/search',
                [
                    'q' => $keyword,
                    'format' => 'jsonv2',
                    'addressdetails' => 1,
                    'limit' => 8,
                    'countrycodes' => 'id',
                ]
            );

        if (! $response->successful()) {
            return [];
        }

        return collect($response->json())
            ->map(function ($item) {

                return [

                    'type' => 'location',

                    'id' => $item['place_id'] ?? null,

                    'title' => $item['display_name'] ?? null,

                    'subtitle' => $item['type'] ?? null,

                    'latitude' => isset($item['lat'])
                        ? (float) $item['lat']
                        : null,

                    'longitude' => isset($item['lon'])
                        ? (float) $item['lon']
                        : null,

                    'osm_id' => $item['osm_id'] ?? null,

                    'osm_type' => $item['osm_type'] ?? null,

                    'address' => $item['address'] ?? [],

                ];
            })
            ->values()
            ->toArray();
    }
}
