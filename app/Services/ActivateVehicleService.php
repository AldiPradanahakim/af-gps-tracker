<?php

namespace App\Services;

use App\Models\Device;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ActivateVehicleService
{
    /**
     * Simpan informasi kendaraan.
     */
    public function store(array $data): Vehicle
    {
        return DB::transaction(function () use ($data) {

            /** @var User|null $user */
            $user = Auth::user();

            if (! $user) {

                throw new HttpException(
                    401,
                    'Silakan login terlebih dahulu.'
                );
            }

            $device = Device::query()

                ->where('user_id', $user->id)

                ->where('is_active', true)

                ->latest('activated_at')

                ->first();

            if (! $device) {

                throw new HttpException(
                    403,
                    'Perangkat belum diaktivasi.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Kendaraan Sudah Ada
            |--------------------------------------------------------------------------
            |
            | Tanpa ini, submit ulang (reload/back-button setelah onboarding
            | selesai) jatuh ke QueryException dari unique('device_id') di
            | tabel vehicles - 500 mentah alih-alih pesan yang jelas.
            |--------------------------------------------------------------------------
            */

            if (Vehicle::where('device_id', $device->id)->exists()) {

                throw new HttpException(
                    422,
                    'Kendaraan untuk perangkat ini sudah terdaftar.'
                );
            }

            return Vehicle::create([

                /*
                |--------------------------------------------------------------------------
                | Device
                |--------------------------------------------------------------------------
                */

                'device_id' => $device->id,

                /*
                |--------------------------------------------------------------------------
                | Vehicle
                |--------------------------------------------------------------------------
                */

                'vehicle_name' => $data['vehicle_name'],

                'vehicle_type' => $data['vehicle_type'],

                'plate_number' => strtoupper($data['plate_number']),

                /*
                |--------------------------------------------------------------------------
                | Marker
                |--------------------------------------------------------------------------
                */

                'marker_icon' => $data['marker_icon'],

                'marker_color' => $data['marker_color'],

            ]);
        });
    }
}
