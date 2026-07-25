<?php

namespace App\Repositories;

use App\Models\Device;

class HomeLocationRepository
{
    public function update(array $deviceIds, array $homeLocation): void
    {
        Device::whereIn(
            'id',
            $deviceIds
        )->update([
            'home_location' => json_encode(
                $homeLocation,
                JSON_UNESCAPED_UNICODE
            ),
        ]);
    }
}
