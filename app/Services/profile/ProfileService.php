<?php

namespace App\Services\Profile;

use App\Models\Device;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileService
{

    public function createUser(array $data, int $deviceId): User
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
            'phone' => $data['phone'],
        ];

        if (!empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        $user->update($payload);

        return $user;
    }
}
