<?php

namespace App\Services;

use App\Models\Device;
use App\Models\Vehicle;

class VehicleService
{
    public function create(Device $device, array $data): Vehicle
    {
        return $device->vehicle()->create([
            'vehicle_name' => $data['vehicle_name'],
            'vehicle_type' => $data['vehicle_type'],
            'plate_number' => strtoupper($data['plate_number']),
        ]);
    }
}
