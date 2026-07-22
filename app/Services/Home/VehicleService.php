<?php

namespace App\Services\Home;

use App\Models\Device;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Services\Home\Support\VehicleCardFormatter;

class VehicleService
{
    /**
     * Simpan informasi kendaraan
     * setelah device berhasil diaktivasi
     * melalui Dashboard Home.
     */
    public function store(array $data): array
    {
        $user = Auth::user();

        $deviceId = session('home.activated_device_id');

        if (! $deviceId) {

            throw ValidationException::withMessages([

                'device' => [

                    'Perangkat belum diaktivasi.',

                ],

            ]);
        }

        return DB::transaction(function () use ($user, $deviceId, $data) {

            $device = Device::query()

                ->where('id', $deviceId)

                ->where('user_id', $user->id)

                ->with([

                    'vehicle',

                    'deviceLogs' => function ($query) {

                        $query->latest()->limit(1);
                    },

                ])

                ->first();

            if (! $device) {

                throw ValidationException::withMessages([

                    'device' => [

                        'Perangkat tidak ditemukan.',

                    ],

                ]);
            }

            if ($device->vehicle) {

                throw ValidationException::withMessages([

                    'device' => [

                        'Perangkat sudah memiliki kendaraan.',

                    ],

                ]);
            }

            Vehicle::create([

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

                'plate_number' => strtoupper(

                    $data['plate_number']

                ),

                /*
                |--------------------------------------------------------------------------
                | Marker
                |--------------------------------------------------------------------------
                */

                'marker_icon' => $data['marker_icon'],

                'marker_color' => $data['marker_color'],

            ]);

            /*
            |--------------------------------------------------------------------------
            | Reload Relation
            |--------------------------------------------------------------------------
            */

            $device->load([

                'vehicle',

                'deviceLogs' => function ($query) {

                    $query->latest()->limit(1);
                },

            ]);

            /*
            |--------------------------------------------------------------------------
            | Session selesai digunakan
            |--------------------------------------------------------------------------
            */

            session()->forget(

                'home.activated_device_id'

            );

            /*
            |--------------------------------------------------------------------------
            | Return Vehicle Card
            |--------------------------------------------------------------------------
            */

            return VehicleCardFormatter::make(

                $device

            );
        });
    }
}
