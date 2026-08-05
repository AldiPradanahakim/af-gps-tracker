<?php

namespace App\Services\Vehicle;

use App\Models\Device;
use App\Repositories\Vehicle\VehicleRepository;

class VehicleService
{
    public function __construct(
        protected VehicleRepository $vehicleRepository
    ) {}

    /**
     * Data halaman detail kendaraan.
     */
    public function show(Device $device): array
    {
        return $this->vehicleRepository->detail($device);
    }

    /**
     * --------------------------------------------------------------------------
     * Update Vehicle Information
     * --------------------------------------------------------------------------
     */
    public function updateInformation(
        Device $device,
        array $data
    ): array {

        return $this->vehicleRepository->updateInformation(

            $device,

            $data

        );
    }

    /**
     * Latest Location.
     */
    public function latest(
        Device $device
    ): array {

        return $this->vehicleRepository->latest(
            $device
        );
    }
    /**
     * Travel History.
     */
    public function history(
        Device $device,
        ?string $date = null
    ): array {

        return $this->vehicleRepository->history(

            $device,

            $date

        );
    }

    /**
     * Playback.
     */
    public function playback(
        Device $device,
        ?string $date
    ): array {

        return $this->vehicleRepository->playback(

            $device,

            $date

        );
    }

    /**
     * Summary.
     */
    public function summary(
        Device $device
    ): array {

        return $this->vehicleRepository->summary(
            $device
        );
    }

    /**
     * Activity Timeline.
     */
    public function activity(
        Device $device
    ): array {

        return $this->vehicleRepository->activity(
            $device
        );
    }
}
