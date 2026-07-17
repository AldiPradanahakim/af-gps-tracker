<?php

namespace App\Services;

use App\Models\Geofence;
use App\Repositories\GeofenceRepository;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class GeofenceService
{
    public function __construct(
        protected GeofenceRepository $geofenceRepository
    ) {}

    /**
     * Menampilkan seluruh geofence milik user.
     */
    public function getAll(int $userId)
    {
        return $this->geofenceRepository
            ->getByUser($userId);
    }

    /**
     * Menyimpan geofence baru.
     */
    public function store(array $data): Geofence
    {
        return DB::transaction(function () use ($data) {

            $device = $this->geofenceRepository
                ->findDevice($data['device_id']);

            if (! $device) {
                throw new RuntimeException('Device tidak ditemukan.');
            }

            if ($data['type'] === 'radius') {

                $config = $this->buildRadiusConfig(
                    $device,
                    $data
                );
            } else {

                $config = $this->buildAdministrativeConfig(
                    $data
                );
            }

            return $this->geofenceRepository
                ->create([

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
     * Update status geofence.
     */
    public function updateStatus(
        int $id,
        bool $status
    ): Geofence {

        $geofence = $this->find($id);

        $this->geofenceRepository
            ->updateStatus(
                $geofence,
                $status
            );

        return $geofence->refresh();
    }

    /**
     * Hapus geofence.
     */
    public function delete(int $id): void
    {
        $this->geofenceRepository
            ->delete(
                $this->find($id)
            );
    }

    /**
     * Detail geofence.
     */
    public function find(int $id): Geofence
    {
        $geofence = $this->geofenceRepository
            ->find($id);

        if (! $geofence) {
            throw new RuntimeException(
                'Geofence tidak ditemukan.'
            );
        }

        return $geofence;
    }

    /**
     * Config Radius.
     */
    protected function buildRadiusConfig(
        $device,
        array $data
    ): array {

        $radius = (float) $data['radius'];

        if ($data['radius_unit'] === 'kilometer') {

            $radius *= 1000;
        }

        if ($data['source'] === 'home') {

            $location = $device->home_location;

            if (! $location) {

                throw new RuntimeException(
                    'Home Location belum diatur.'
                );
            }
        } else {

            $location = optional(
                $device->travelHistories()
                    ->latest('received_at')
                    ->first()
            )->location;

            if (! $location) {

                throw new RuntimeException(
                    'Lokasi kendaraan belum tersedia.'
                );
            }
        }

        return [

            'source' => $data['source'],

            'center' => [

                'latitude' => $location['latitude'],

                'longitude' => $location['longitude'],

            ],

            'radius' => $radius,

        ];
    }

    /**
     * Config Administrative.
     */
    protected function buildAdministrativeConfig(
        array $data
    ): array {

        return [

            'display_name' => $data['display_name'],

            'geojson' => $data['geojson'],

        ];
    }
}
