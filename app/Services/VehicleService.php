<?php

namespace App\Services;

use App\Models\Device;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VehicleService
{
    public function store(array $data): Vehicle
    {
        return DB::transaction(function () use ($data) {

            $user = Auth::user();

            assert($user instanceof User);

            $device = Device::where('user_id', $user->id)->firstOrFail();

            return Vehicle::create([

                'device_id'     => $device->id,

                'vehicle_name'  => $data['vehicle_name'],

                'vehicle_type'  => $data['vehicle_type'],

                'plate_number'  => strtoupper($data['plate_number']),

            ]);
        });
    }
}
