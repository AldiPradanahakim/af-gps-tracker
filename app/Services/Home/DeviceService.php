<?php

namespace App\Services\Home;

use App\Models\Device;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class DeviceService
{
    /**
     * Aktivasi device baru dari halaman Home.
     */
    public function activate(array $data): Device
    {
        $user = Auth::user();

        assert($user instanceof User);

        return DB::transaction(function () use ($data, $user) {

            $device = Device::where(
                'device_id',
                $data['device_id']
            )->first();

            /*
            |--------------------------------------------------------------------------
            | Device tidak ditemukan
            |--------------------------------------------------------------------------
            */

            if (! $device) {

                throw ValidationException::withMessages([

                    'device_id' => 'Device ID tidak ditemukan.',

                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Password salah
            |--------------------------------------------------------------------------
            */

            if (! Hash::check($data['device_password'], $device->device_password)) {

                throw ValidationException::withMessages([

                    'device_password' => 'Password device salah.',

                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Device sudah dimiliki user lain
            |--------------------------------------------------------------------------
            */

            if (
                ! is_null($device->user_id)
                && $device->user_id !== $user->id
            ) {

                throw ValidationException::withMessages([

                    'device_id' => 'Perangkat sudah digunakan oleh pengguna lain.',

                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Device sudah dimiliki user ini
            |--------------------------------------------------------------------------
            */

            if ($device->user_id === $user->id) {

                throw ValidationException::withMessages([

                    'device_id' => 'Perangkat sudah ada pada akun Anda.',

                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Aktivasi Device
            |--------------------------------------------------------------------------
            */

            $device->update([

                'user_id'       => $user->id,

                'activated_at'  => now(),

                'is_active'     => true,

            ]);

            /*
            |--------------------------------------------------------------------------
            | Simpan device aktif sementara
            |--------------------------------------------------------------------------
            */

            session()->put(
                'home.activated_device_id',
                $device->id
            );

            return $device;
        });
    }
}
