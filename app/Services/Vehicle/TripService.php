<?php

namespace App\Services\Vehicle;

use App\Models\Device;
use App\Models\StopHistory;
use App\Models\TravelHistory;
use App\Repositories\Vehicle\VehicleRepository;
use Illuminate\Support\Collection;

/**
 * --------------------------------------------------------------------------
 * Trip Service
 * --------------------------------------------------------------------------
 * Mengelompokkan travel_histories mentah (titik-titik GPS) menjadi
 * "trip" (perjalanan) yang dibatasi oleh stop_histories yang sudah
 * selesai (end_time != null).
 *
 * Sebuah trip = kumpulan titik travel_histories yang berurutan, dimulai
 * tepat setelah end_time sebuah stop dan berakhir tepat sebelum
 * start_time stop berikutnya (atau sampai titik terakhir yang tersedia
 * kalau tidak ada stop lagi).
 * --------------------------------------------------------------------------
 */
class TripService
{
    /**
     * Jari-jari bumi (km) untuk perhitungan jarak Haversine.
     */
    private const EARTH_RADIUS_KM = 6371;

    /**
     * Segmen dengan kurang dari jumlah titik ini dibuang (bukan trip
     * sungguhan, hanya noise / titik GPS yang berdiri sendiri).
     */
    private const MIN_POINTS_PER_TRIP = 2;

    public function __construct(
        protected VehicleRepository $vehicleRepository
    ) {}

    /**
     * --------------------------------------------------------------------------
     * Bangun Daftar Trip
     * --------------------------------------------------------------------------
     */
    public function trips(
        Device $device,
        ?string $startDate = null,
        ?string $endDate = null
    ): array {

        $raw = $this->vehicleRepository->tripRawData(

            $device,

            $startDate,

            $endDate

        );

        /** @var Collection<int, TravelHistory> $travel */
        $travel = $raw['travel'];

        /** @var Collection<int, StopHistory> $stops */
        $stops = $raw['stops'];

        if ($travel->count() < self::MIN_POINTS_PER_TRIP) {

            return [];
        }

        return $this->partition($travel, $stops)

            ->filter(

                fn(Collection $segment) =>

                $segment->count() >= self::MIN_POINTS_PER_TRIP

            )

            ->map(

                fn(Collection $segment) =>

                $this->summarizeSegment($segment)

            )

            ->values()

            ->toArray();
    }

    /**
     * --------------------------------------------------------------------------
     * Partisi Travel History Berdasarkan Stop History
     * --------------------------------------------------------------------------
     * Single pass menyusuri $travel (sudah urut received_at ASC) sambil
     * menggeser pointer $stops (sudah urut start_time ASC):
     *
     * - Titik yang jatuh di dalam jendela [start_time, end_time) sebuah
     *   stop dibuang (bagian dari behenti, bukan trip).
     * - Begitu sebuah titik >= end_time stop yang sedang aktif, segmen
     *   berjalan saat ini ditutup (trip berakhir) dan segmen baru dimulai
     *   (trip berikutnya), lalu pointer stop maju.
     * --------------------------------------------------------------------------
     */
    protected function partition(
        Collection $travel,
        Collection $stops
    ): Collection {

        $stops = $stops->values();

        $segments = collect();

        $current = collect();

        $stopIndex = 0;

        $stopCount = $stops->count();

        foreach ($travel as $point) {

            /*
            |--------------------------------------------------------------------------
            | Tutup segmen setiap kali titik sudah melewati/menyentuh
            | end_time dari stop yang sedang jadi pembatas.
            |--------------------------------------------------------------------------
            */

            while (

                $stopIndex < $stopCount &&

                $point->received_at->gte(

                    $stops[$stopIndex]->end_time

                )

            ) {

                $segments->push($current);

                $current = collect();

                $stopIndex++;
            }

            /*
            |--------------------------------------------------------------------------
            | Buang titik yang jatuh di dalam jendela stop yang sedang aktif.
            |--------------------------------------------------------------------------
            */

            if (

                $stopIndex < $stopCount &&

                $point->received_at->gte($stops[$stopIndex]->start_time) &&

                $point->received_at->lt($stops[$stopIndex]->end_time)

            ) {

                continue;
            }

            $current->push($point);
        }

        $segments->push($current);

        return $segments;
    }

    /**
     * --------------------------------------------------------------------------
     * Ringkas Satu Segmen Menjadi Objek Trip
     * --------------------------------------------------------------------------
     */
    protected function summarizeSegment(
        Collection $segment
    ): array {

        $points = $segment->values();

        /** @var TravelHistory $first */
        $first = $points->first();

        /** @var TravelHistory $last */
        $last = $points->last();

        $durationSeconds = (int) $first->received_at->diffInSeconds(

            $last->received_at,

            absolute: true

        );

        return [

            'start_time' => optional($first->received_at)?->toDateTimeString(),

            'end_time' => optional($last->received_at)?->toDateTimeString(),

            'duration' => $this->formatDuration($durationSeconds),

            'duration_seconds' => $durationSeconds,

            'distance_km' => $this->segmentDistanceKm($points),

            'start_address' => $first->search_address,

            'end_address' => $last->search_address,

            'point_count' => $points->count(),

        ];
    }

    /**
     * --------------------------------------------------------------------------
     * Total Jarak Segmen (KM) - Jumlah Haversine Antar Titik Berurutan
     * --------------------------------------------------------------------------
     */
    protected function segmentDistanceKm(
        Collection $points
    ): float {

        $distance = 0.0;

        for ($i = 1; $i < $points->count(); $i++) {

            /** @var TravelHistory $previous */
            $previous = $points[$i - 1];

            /** @var TravelHistory $current */
            $current = $points[$i];

            $previousLocation = $previous->location ?? [];

            $currentLocation = $current->location ?? [];

            if (

                !isset($previousLocation['lat'], $previousLocation['lng']) ||

                !isset($currentLocation['lat'], $currentLocation['lng'])

            ) {

                continue;
            }

            $distance += $this->haversineKm(

                (float) $previousLocation['lat'],

                (float) $previousLocation['lng'],

                (float) $currentLocation['lat'],

                (float) $currentLocation['lng']

            );
        }

        return round($distance, 1);
    }

    /**
     * --------------------------------------------------------------------------
     * Haversine Formula (KM)
     * --------------------------------------------------------------------------
     */
    protected function haversineKm(

        float $lat1,

        float $lng1,

        float $lat2,

        float $lng2

    ): float {

        $latDelta = deg2rad($lat2 - $lat1);

        $lngDelta = deg2rad($lng2 - $lng1);

        $a = sin($latDelta / 2) ** 2

            + cos(deg2rad($lat1))

            * cos(deg2rad($lat2))

            * sin($lngDelta / 2) ** 2;

        $c = 2 * asin(sqrt($a));

        return self::EARTH_RADIUS_KM * $c;
    }

    /**
     * --------------------------------------------------------------------------
     * Format Detik -> "Xj Ym" / "Ym"
     * --------------------------------------------------------------------------
     * Replika persis VehicleRepository::formatDuration() (protected di
     * sana, jadi tidak bisa dipakai langsung dari sini).
     * --------------------------------------------------------------------------
     */
    protected function formatDuration(int $seconds): string
    {
        if ($seconds <= 0) {

            return '-';
        }

        $hours = intdiv($seconds, 3600);

        $minutes = intdiv($seconds % 3600, 60);

        if ($hours > 0) {

            return "{$hours}j {$minutes}m";
        }

        return "{$minutes}m";
    }
}
