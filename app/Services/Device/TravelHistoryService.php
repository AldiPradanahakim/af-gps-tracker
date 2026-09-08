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
     *
     * Method ini hanya dipanggil apabila DeviceLog berhasil dibuat.
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

                'lat' => (float) $payload['lat'],

                'lng' => (float) $payload['lng'],

                'speed' => (float) (
                    $payload['speed'] ?? 0
                ),

                'heading' => (float) (
                    $payload['heading'] ?? 0
                ),

                'battery' => isset(
                    $payload['battery']
                )
                    ? (int) $payload['battery']
                    : null,

                'satellite' => isset(
                    $payload['satellite']
                )
                    ? (int) $payload['satellite']
                    : null,

            ],

            'search_address' => $searchAddress,

            'received_at' => Carbon::parse(
                $payload['received_at']
            ),

        ]);
    }
}