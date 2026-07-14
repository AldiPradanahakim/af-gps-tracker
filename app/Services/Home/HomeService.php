<?php

namespace App\Services\Home;

use App\Models\User;

class HomeService
{
    public function getHomeData(User $user): array
    {
        $device = $user->devices()
            ->with([
                'vehicle',
                'geofences',
                'notifications',
            ])
            ->first();

        return [
            'device' => $device,
            'vehicle' => $device?->vehicle,
            'activeGeofence' => $device?->geofences()->where('status', true)->first(),
            'notifications' => $device?->notifications()
                ->latest()
                ->limit(10)
                ->get(),
        ];
    }
}
