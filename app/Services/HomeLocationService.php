<?php

namespace App\Services;

use App\Models\Device;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\HomeLocationRepository;

class HomeLocationService
{
    public function __construct(
        protected HomeLocationRepository $repository
    ) {}

    /**
     * ----------------------------------------------------------
     * Simpan atau perbarui Home Location untuk satu Device.
     *
     * Validasi ownership dilakukan di Controller.
     * ----------------------------------------------------------
     */
    public function save(string $deviceId, array $data): Device
    {
        return $this->repository->save(

            $deviceId,

            [
                'latitude'     => $data['latitude'],
                'longitude'    => $data['longitude'],
                'display_name' => $data['display_name'],
            ]

        );
    }

    /**
     * ----------------------------------------------------------
     * Simpan Home Location yang sama ke semua Device milik User
     * yang belum memiliki Home Location (opsi "Semua Kendaraan").
     * ----------------------------------------------------------
     */
    public function saveToAll(string $userId, array $data): Collection
    {
        return $this->repository->saveToAll(

            $userId,

            [
                'latitude'     => $data['latitude'],
                'longitude'    => $data['longitude'],
                'display_name' => $data['display_name'],
            ]

        );
    }

    /**
     * ----------------------------------------------------------
     * Hapus Home Location (set null).
     *
     * Tidak menghapus Device.
     * ----------------------------------------------------------
     */
    public function delete(string $deviceId): Device
    {
        return $this->repository->delete($deviceId);
    }
}
