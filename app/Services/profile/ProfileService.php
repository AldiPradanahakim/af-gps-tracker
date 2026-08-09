<?php

namespace App\Services\Profile;

use App\Models\Device;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileService
{

    public function createUser(array $data, string $deviceId): User
    {
        return DB::transaction(function () use ($data, $deviceId) {

            /*
            |--------------------------------------------------------------------------
            | Lock baris device supaya dua sesi anonim yang aktivasi device
            | yang sama secara bersamaan tidak bisa berdua-duanya lolos
            | pengecekan "belum diklaim" (race condition).
            |--------------------------------------------------------------------------
            */

            $device = Device::whereKey($deviceId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($device->user_id !== null) {

                throw ValidationException::withMessages([
                    'device_id' => 'Perangkat sudah diaktivasi oleh pengguna lain.',
                ]);
            }

            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'phone'    => $data['phone'],
                'password' => Hash::make($data['password']),
            ]);

            $device->update([
                'user_id'      => $user->id,
                'is_active'    => true,
                'activated_at' => now(),
            ]);

            return $user;
        });
    }


    public function update(User $user, array $data): User
    {
        $payload = [

            'name'  => $data['name'],

            'email' => $data['email'],

            'phone' => $data['phone'],

        ];

        if (!empty($data['password'])) {

            if (
                !Hash::check(
                    $data['current_password'],
                    $user->password
                )
            ) {

                throw ValidationException::withMessages([

                    'current_password' => [
                        'Password lama tidak sesuai.'
                    ],

                ]);
            }

            $payload['password'] = Hash::make($data['password']);
        }

        $user->update($payload);

        return $user->fresh();
    }
}
