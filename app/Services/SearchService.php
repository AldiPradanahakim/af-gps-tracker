<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\SearchRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SearchService
{
    /**
     * Radius bias (dalam derajat) untuk viewbox Nominatim di sekitar
     * titik jangkar (lokasi kendaraan / pusat peta). ~0.3 derajat kira-kira
     * setara kotak 60-65km, cukup relevan untuk konteks pelacakan kendaraan
     * tanpa terlalu membatasi hasil.
     */
    private const ANCHOR_BIAS_DEGREES = 0.3;

    /**
     * TTL cache hasil pencarian lokasi Nominatim. Pencarian tempat tidak
     * butuh cache jangka panjang seperti reverse-geocoding alamat — ini
     * hanya untuk menghindari request berulang ke Nominatim saat user
     * mengetik keyword yang sama/mirip dalam satu sesi (autocomplete).
     */
    private const LOCATION_CACHE_TTL_MINUTES = 30;

    public function __construct(
        protected SearchRepository $searchRepository
    ) {}

    /**
     * Search kendaraan + alamat + wilayah administratif.
     */
    public function search(string $keyword, User $user, ?float $lat = null, ?float $lng = null): array
    {
        $vehicles = $this->searchRepository
            ->searchVehicle($user, $keyword)
            ->toArray();

        $locations = $this->searchLocation($keyword, $lat, $lng);

        return array_values(
            array_merge(
                $vehicles,
                $locations
            )
        );
    }

    /**
     * Search alamat / wilayah menggunakan Nominatim.
     *
     * Hasil dibiaskan ke sekitar titik jangkar ($lat/$lng — biasanya lokasi
     * terakhir kendaraan yang sedang dilihat user). Catatan penting hasil
     * eksperimen langsung ke Nominatim: parameter `bounded=0` (soft bias)
     * ternyata TIDAK mengubah ranking sama sekali untuk keyword generik
     * seperti "museum"/"cafe" — hasilnya identik dengan tanpa viewbox sama
     * sekali. Yang benar-benar bekerja adalah `bounded=1` (hard filter ke
     * dalam viewbox). Karena itu strategi di sini adalah: coba dulu dengan
     * `bounded=1` (hasil relevan & dekat), dan HANYA jika itu tidak
     * menghasilkan apa-apa (mis. user mencari nama tempat spesifik di kota
     * lain seperti "Malioboro Yogyakarta" saat anchor di Bandung), fallback
     * ke pencarian tanpa viewbox sama sekali supaya pencarian tempat jauh
     * tetap berfungsi.
     */
    private function searchLocation(string $keyword, ?float $lat = null, ?float $lng = null): array
    {
        $cacheKey = $this->buildLocationCacheKey($keyword, $lat, $lng);

        return Cache::remember(
            $cacheKey,
            now()->addMinutes(self::LOCATION_CACHE_TTL_MINUTES),
            fn () => $this->fetchLocation($keyword, $lat, $lng)
        );
    }

    /**
     * Bangun cache key dari keyword yang dinormalisasi + "bucket" koordinat
     * jangkar (dibulatkan ~2 desimal / ~1km) supaya request dengan anchor
     * yang praktis sama (kendaraan diam/bergerak sedikit) tetap hit cache
     * yang sama.
     */
    private function buildLocationCacheKey(string $keyword, ?float $lat, ?float $lng): string
    {
        $normalizedKeyword = mb_strtolower(trim($keyword));

        $anchor = ($lat !== null && $lng !== null)
            ? round($lat, 2) . ',' . round($lng, 2)
            : 'none';

        return 'search:location:' . md5($normalizedKeyword . '|' . $anchor);
    }

    /**
     * Panggil Nominatim untuk mencari alamat / wilayah.
     */
    private function fetchLocation(string $keyword, ?float $lat = null, ?float $lng = null): array
    {
        $baseQuery = [
            'q' => $keyword,
            'format' => 'jsonv2',
            'addressdetails' => 1,
            'limit' => 8,
            'countrycodes' => 'id',
        ];

        if ($lat !== null && $lng !== null) {
            $viewboxQuery = $baseQuery + [
                'viewbox' => implode(',', [
                    $lng - self::ANCHOR_BIAS_DEGREES,
                    $lat + self::ANCHOR_BIAS_DEGREES,
                    $lng + self::ANCHOR_BIAS_DEGREES,
                    $lat - self::ANCHOR_BIAS_DEGREES,
                ]),
                'bounded' => 1,
            ];

            $biasedResults = $this->callNominatim($viewboxQuery);

            if (! empty($biasedResults)) {
                return $biasedResults;
            }

            // Tidak ada hasil di sekitar anchor (mis. user cari tempat
            // spesifik di kota lain) - fallback ke pencarian tanpa batas.
        }

        return $this->callNominatim($baseQuery);
    }

    /**
     * Eksekusi request pencarian lokasi (LocationIQ kalau
     * LOCATIONIQ_API_KEY terisi - limit lebih longgar & data lebih
     * lengkap untuk tempat kecil, fallback ke Nominatim gratis kalau
     * tidak) dan transformasikan hasilnya. Bentuk parameter query
     * (viewbox/bounded/dst) sama persis di kedua provider.
     */
    private function callNominatim(array $query): array
    {
        $locationIqKey = config('services.locationiq.key');

        $response = $locationIqKey

            ? Http::timeout(10)->acceptJson()->get(
                'https://us1.locationiq.com/v1/search',
                array_merge($query, ['key' => $locationIqKey, 'format' => 'json'])
            )

            : Http::timeout(10)
                ->acceptJson()
                ->withHeaders([
                    'User-Agent' => config('app.name') . '/1.0',
                ])
                ->get(
                    'https://nominatim.openstreetmap.org/search',
                    $query
                );

        if (! $response->successful()) {
            return [];
        }

        return collect($response->json())
            ->map(function ($item) {

                return [

                    'type' => 'location',

                    'id' => $item['place_id'] ?? null,

                    'title' => $item['display_name'] ?? null,

                    'subtitle' => $item['type'] ?? null,

                    'latitude' => isset($item['lat'])
                        ? (float) $item['lat']
                        : null,

                    'longitude' => isset($item['lon'])
                        ? (float) $item['lon']
                        : null,

                    'osm_id' => $item['osm_id'] ?? null,

                    'osm_type' => $item['osm_type'] ?? null,

                    'address' => $item['address'] ?? [],

                ];
            })
            ->values()
            ->toArray();
    }
}
