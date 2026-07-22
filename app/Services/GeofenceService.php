<?php

namespace App\Services;

use App\Models\Geofence;
use App\Repositories\GeofenceRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GeofenceService
{
    public function __construct(
        protected GeofenceRepository $repository
    ) {}

    /**
     * Ambil seluruh geofence milik user login.
     */
    public function getAll(): Collection
    {
        return $this->repository->getByUser(
            Auth::id()
        );
    }

    /**
     * Simpan geofence baru.
     */
    public function store(array $data): Geofence
    {
        return DB::transaction(function () use ($data) {

            $device = $this->repository->findDeviceByUser(
                $data['device_id'],
                Auth::id()
            );

            if (! $device) {

                throw ValidationException::withMessages([
                    'device_id' => 'Device tidak ditemukan.',
                ]);
            }

            $config = match ($data['type']) {

                'radius' => $this->buildRadiusConfig(
                    $device,
                    $data
                ),

                'administrative' => $this->buildAdministrativeConfig(
                    $data
                ),

                'custom' => $this->buildCustomConfig(
                    $data
                ),

                default => throw ValidationException::withMessages([
                    'type' => 'Tipe geofence tidak valid.',
                ]),
            };

            return $this->repository->create([

                'device_id' => $device->id,

                'name' => $data['name'],

                'description' => $data['description'] ?? null,

                'type' => $data['type'],

                'config' => $config,

                'status' => true,

            ]);
        });
    }
    /**
     * Bangun konfigurasi geofence radius.
     */
    protected function buildRadiusConfig(
        $device,
        array $data
    ): array {

        $radius = (float) $data['radius'];

        if ($radius <= 0) {

            throw ValidationException::withMessages([
                'radius' => 'Radius harus lebih dari 0.',
            ]);
        }

        $source = $data['radius_source'];

        $latitude = null;
        $longitude = null;

        switch ($source) {

            case 'home_location':

                $home = $device->home_location;

                if (
                    empty($home) ||
                    !isset($home['lat']) ||
                    !isset($home['lng'])
                ) {

                    throw ValidationException::withMessages([
                        'radius_source' => 'Home location belum tersedia.',
                    ]);
                }

                $latitude = (float) $home['lat'];
                $longitude = (float) $home['lng'];

                break;

            case 'current_location':

                $history = $device->travelHistories()
                    ->latest('received_at')
                    ->first();

                if (! $history) {

                    throw ValidationException::withMessages([
                        'radius_source' => 'Lokasi terakhir perangkat tidak ditemukan.',
                    ]);
                }

                $location = $history->location;

                if (
                    empty($location) ||
                    !isset($location['lat']) ||
                    !isset($location['lng'])
                ) {

                    throw ValidationException::withMessages([
                        'radius_source' => 'Lokasi terakhir tidak valid.',
                    ]);
                }

                $latitude = (float) $location['lat'];
                $longitude = (float) $location['lng'];

                break;

            case 'manual':

                if (
                    !isset($data['latitude']) ||
                    !isset($data['longitude'])
                ) {

                    throw ValidationException::withMessages([
                        'latitude' => 'Latitude wajib diisi.',
                        'longitude' => 'Longitude wajib diisi.',
                    ]);
                }

                $latitude = (float) $data['latitude'];
                $longitude = (float) $data['longitude'];

                break;

            default:

                throw ValidationException::withMessages([
                    'radius_source' => 'Sumber radius tidak valid.',
                ]);
        }

        return [

            'center' => [

                'lat' => $latitude,

                'lng' => $longitude,

            ],

            'radius' => $radius,

            'unit' => $data['radius_unit'] ?? 'meter',

            'source' => $source,

        ];
    }
    /**
     * Bangun konfigurasi geofence administratif.
     */
    protected function buildAdministrativeConfig(
        array $data
    ): array {

        if (
            empty($data['geojson']) ||
            empty($data['display_name']) ||
            empty($data['administrative_type'])
        ) {

            throw ValidationException::withMessages([
                'geojson' => 'Data wilayah administratif tidak lengkap.',
            ]);
        }

        $geojson = is_string($data['geojson'])
            ? json_decode($data['geojson'], true)
            : $data['geojson'];

        if (
            json_last_error() !== JSON_ERROR_NONE ||
            !is_array($geojson)
        ) {

            throw ValidationException::withMessages([
                'geojson' => 'GeoJSON tidak valid.',
            ]);
        }

        if (
            !isset($geojson['type']) ||
            !in_array(
                $geojson['type'],
                ['Polygon', 'MultiPolygon']
            )
        ) {

            throw ValidationException::withMessages([
                'geojson' => 'GeoJSON harus berupa Polygon atau MultiPolygon.',
            ]);
        }

        return [

            'display_name' => $data['display_name'],

            'administrative_type' => $data['administrative_type'],

            'geometry' => $geojson,

        ];
    }

    /**
     * Bangun konfigurasi geofence custom polygon.
     */
    protected function buildCustomConfig(
        array $data
    ): array {

        if (empty($data['geojson'])) {

            throw ValidationException::withMessages([
                'geojson' => 'Polygon belum dibuat.',
            ]);
        }

        $geojson = is_string($data['geojson'])
            ? json_decode($data['geojson'], true)
            : $data['geojson'];

        if (
            json_last_error() !== JSON_ERROR_NONE ||
            !is_array($geojson)
        ) {

            throw ValidationException::withMessages([
                'geojson' => 'GeoJSON tidak valid.',
            ]);
        }

        if (
            !isset($geojson['type']) ||
            !in_array(
                $geojson['type'],
                ['Polygon', 'MultiPolygon']
            )
        ) {

            throw ValidationException::withMessages([
                'geojson' => 'Polygon tidak valid.',
            ]);
        }

        return [

            'geometry' => $geojson,

        ];
    }
    /**
     * Update status geofence.
     */
    public function updateStatus(
        int $id,
        bool $status
    ): Geofence {

        $geofence = $this->repository->findOwnedByUser(
            $id,
            Auth::id()
        );

        if (! $geofence) {

            throw ValidationException::withMessages([
                'geofence' => 'Geofence tidak ditemukan.',
            ]);
        }

        return DB::transaction(function () use (
            $geofence,
            $status
        ) {

            return $this->repository->updateStatus(
                $geofence,
                $status
            );
        });
    }

    /**
     * Hapus satu geofence.
     */
    public function destroy(
        int $id
    ): void {

        $geofence = $this->repository->findOwnedByUser(
            $id,
            Auth::id()
        );

        if (! $geofence) {

            throw ValidationException::withMessages([
                'geofence' => 'Geofence tidak ditemukan.',
            ]);
        }

        DB::transaction(function () use (
            $geofence
        ) {

            $this->repository->delete(
                $geofence
            );
        });
    }

    /**
     * Hapus banyak geofence.
     */
    public function destroyMany(
        array $ids
    ): int {

        $ownedIds = [];

        foreach ($ids as $id) {

            $geofence = $this->repository->findOwnedByUser(
                $id,
                Auth::id()
            );

            if ($geofence) {

                $ownedIds[] = $geofence->id;
            }
        }

        if (empty($ownedIds)) {

            throw ValidationException::withMessages([
                'ids' => 'Tidak ada geofence yang dapat dihapus.',
            ]);
        }

        return DB::transaction(function () use (
            $ownedIds
        ) {

            return $this->repository->deleteMany(
                $ownedIds
            );
        });
    }

    public function getByDevice(
        int $deviceId
    ) {
        $device = $this->repository->findDeviceByUser(
            $deviceId,
            Auth::id()
        );

        if (! $device) {

            throw ValidationException::withMessages([
                'device_id' => 'Device tidak ditemukan.',
            ]);
        }

        return $this->repository->getByDevice(
            $device->id
        );
    }
}
