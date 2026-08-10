<?php

namespace App\Jobs;

use App\Models\StopHistory;
use App\Models\TravelHistory;
use App\Services\Geofence\ReverseGeocodingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ResolveGpsAddressJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    /**
     * Resolusi alamat asinkron untuk titik GPS yang belum ada di cache
     * reverse-geocoding saat pipeline ingest berjalan (lihat
     * GPSProcessingService dan ReverseGeocodingService::searchCached()).
     * Dijalankan di queue worker - boleh menunggu throttle rate limit
     * provider, tidak memblokir proses mqtt:subscribe.
     */
    public function __construct(
        public float $latitude,
        public float $longitude,
        public ?string $travelHistoryId = null,
        public ?string $stopHistoryId = null,
    ) {}

    public function handle(
        ReverseGeocodingService $reverseGeocodingService
    ): void {

        $address = $reverseGeocodingService->search(
            $this->latitude,
            $this->longitude
        );

        if ($address === 'Alamat tidak ditemukan') {
            return;
        }

        if ($this->travelHistoryId) {

            TravelHistory::whereKey($this->travelHistoryId)
                ->update([
                    'search_address' => $address,
                ]);
        }

        if ($this->stopHistoryId) {

            StopHistory::whereKey($this->stopHistoryId)
                ->update([
                    'search_address' => $address,
                ]);
        }
    }
}
