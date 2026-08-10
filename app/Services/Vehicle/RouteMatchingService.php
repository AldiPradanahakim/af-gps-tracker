<?php

namespace App\Services\Vehicle;

use Illuminate\Support\Facades\Http;
use Throwable;

class RouteMatchingService
{
    /**
     * OSRM Map Matching API (public demo server).
     *
     * Catatan produksi: ini server demo gratis OSRM, bukan untuk beban
     * tinggi/komersial (lihat kebijakan project-osrm.org). Untuk
     * penggunaan produksi skala besar, ganti $endpoint dengan instance
     * OSRM sendiri atau layanan map-matching berbayar (Mapbox/Google).
     * Sampai saat itu, kegagalan endpoint ini SELALU jatuh ke garis
     * lurus (lihat return null di bawah) - tidak pernah mem-blok atau
     * merusak fitur riwayat/playback.
     */
    protected string $endpoint = 'https://router.project-osrm.org/match/v1/driving';

    /**
     * Maksimal titik yang dikirim ke OSRM per request. GPS asli yang
     * mengirim data tiap detik bisa menghasilkan puluhan ribu titik per
     * hari - terlalu banyak untuk satu request map-matching, jadi
     * di-downsample dulu secara merata.
     */
    protected int $maxPoints = 100;

    /**
     * Cocokkan urutan titik GPS ke jalan asli.
     *
     * @param  array<int, array{lat: float, lng: float}>  $points
     * @return array<int, array{0: float, 1: float}>|null  [lat, lng][] hasil map-matching, atau null jika gagal/tidak cukup titik (caller harus fallback ke garis lurus antar titik asli).
     */
    public function match(array $points): ?array
    {
        $points = $this->downsample(
            array_values(array_filter(
                $points,
                fn ($point) => isset($point['lat'], $point['lng'])
            ))
        );

        if (count($points) < 2) {

            return null;
        }

        try {

            $coordinates = implode(';', array_map(

                fn ($point) => $point['lng'] . ',' . $point['lat'],

                $points

            ));

            $response = Http::timeout(15)

                ->get("{$this->endpoint}/{$coordinates}", [

                    'geometries' => 'geojson',

                    'overview' => 'full',

                ]);

            if (! $response->successful()) {

                return null;
            }

            $body = $response->json();

            if (($body['code'] ?? null) !== 'Ok' || empty($body['matchings'])) {

                return null;
            }

            $geometry = $body['matchings'][0]['geometry']['coordinates'] ?? null;

            if (! is_array($geometry) || count($geometry) < 2) {

                return null;
            }

            // GeoJSON = [lng, lat] - dibalik ke [lat, lng] untuk Leaflet.
            return array_map(

                fn ($coordinate) => [(float) $coordinate[1], (float) $coordinate[0]],

                $geometry

            );
        } catch (Throwable $exception) {

            report($exception);

            return null;
        }
    }

    /**
     * Downsample titik secara merata supaya tidak melebihi $maxPoints.
     *
     * @param  array<int, array{lat: float, lng: float}>  $points
     * @return array<int, array{lat: float, lng: float}>
     */
    protected function downsample(array $points): array
    {
        $total = count($points);

        if ($total <= $this->maxPoints) {

            return $points;
        }

        $step = $total / $this->maxPoints;

        $sampled = [];

        for ($i = 0; $i < $this->maxPoints; $i++) {

            $sampled[] = $points[(int) floor($i * $step)];
        }

        // Titik terakhir selalu disertakan supaya rute tidak terpotong.
        $lastIndex = $total - 1;

        if (end($sampled) !== $points[$lastIndex]) {

            $sampled[] = $points[$lastIndex];
        }

        return $sampled;
    }
}
