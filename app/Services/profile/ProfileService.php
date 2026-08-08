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

            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'phone'    => $data['phone'],
                'password' => Hash::make($data['password']),
            ]);

            $device = Device::findOrFail($deviceId);

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
