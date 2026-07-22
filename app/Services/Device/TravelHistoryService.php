<?php

namespace App\Services\Device;

use App\Models\Device;
use App\Models\DeviceLog;
use App\Models\TravelHistory;
use Carbon\Carbon;

class TravelHistoryService
{
    /**
     * Store travel history.
     */
    public function store(
        Device $device,
        DeviceLog $deviceLog,
        array $payload,
        string $searchAddress
    ): TravelHistory {

        return TravelHistory::create([

            'device_log_id' => $deviceLog->id,

            'device_id' => $device->id,

            'location' => [
                'lat'       => $payload['lat'],
                'lng'       => $payload['lng'],
                'speed'     => $payload['speed'] ?? 0,
                'heading'   => $payload['heading'] ?? 0,
                'battery'   => $payload['battery'] ?? null,
                'satellite' => $payload['satellite'] ?? null,
            ],

            'search_address' => $searchAddress,

            'received_at' => Carbon::parse(
                $payload['received_at']
            ),

        ]);
    }
}
