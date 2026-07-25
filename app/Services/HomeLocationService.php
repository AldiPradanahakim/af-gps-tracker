<?php

namespace App\Services;

use App\Models\Device;
use Illuminate\Support\Facades\Auth;
use App\Repositories\HomeLocationRepository;
use Illuminate\Database\Eloquent\Collection;

class HomeLocationService
{
    public function __construct(
        protected HomeLocationRepository $repository
    ) {}

    public function save(array $data): Device|Collection
    {
        if ($data['device_id'] === 'all') {

            $deviceIds = Device::where(
                'user_id',
                Auth::id()
            )->pluck('id')->toArray();
        } else {

            $deviceIds = [
                (int) $data['device_id']
            ];
        }

        $this->repository->update(
            $deviceIds,
            [
                'lat' => (float) $data['latitude'],
                'lng' => (float) $data['longitude'],
                'display_name' => $data['display_name'],
            ]
        );

        if ($data['device_id'] === 'all') {

            return Device::whereIn(
                'id',
                $deviceIds
            )->get();
        }

        return Device::findOrFail(
            $deviceIds[0]
        );
    }
}
