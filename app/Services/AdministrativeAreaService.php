<?php

namespace App\Services;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class AdministrativeAreaService
{
    private const CITY_CODE = '3273';

    private const CITY_NAME = 'Kota Bandung';

    private const BASE_PATH = 'administrative/geojson/3273';

    private ?array $city = null;

    private ?Collection $districts = null;

    private ?Collection $villages = null;

    /**
     * Membaca file GeoJSON.
     */
    private function readGeoJson(string $filename): array
    {
        return Cache::remember(
            'administrative-geojson:' . self::BASE_PATH . '/' . $filename,
            now()->addWeek(),
            function () use ($filename) {

                $path = self::BASE_PATH . '/' . $filename;

                if (!file_exists(storage_path('app/' . $path))) {
                    throw new InvalidArgumentException(
                        "GeoJSON {$filename} tidak ditemukan."
                    );
                }

                $json = json_decode(
                    file_get_contents(storage_path('app/' . $path)),
                    true
                );

                if (!is_array($json)) {
                    throw new InvalidArgumentException(
                        "GeoJSON {$filename} tidak valid."
                    );
                }

                return $json;
            }
        );
    }

    /**
     * Polygon Kota Bandung.
     */
    private function city(): array
    {
        if ($this->city === null) {
            $this->city = $this->readGeoJson('city.json');
        }

        return $this->city;
    }

    /**
     * Seluruh polygon kecamatan.
     */
    private function districts(): Collection
    {
        if ($this->districts === null) {
            $json = $this->readGeoJson('district.json');

            $this->districts = collect(
                $json['features'] ?? []
            );
        }

        return $this->districts;
    }

    /**
     * Seluruh polygon kelurahan.
     */
    private function villages(): Collection
    {
        if ($this->villages === null) {
            $json = $this->readGeoJson('village.json');

            $this->villages = collect(
                $json['features'] ?? []
            );
        }

        return $this->villages;
    }

    /**
     * Informasi Kota Bandung.
     */
    public function getCity(): array
    {
        $feature = $this->city()['features'][0] ?? null;

        if (!$feature) {
            throw new ModelNotFoundException(
                'Polygon Kota Bandung tidak ditemukan.'
            );
        }

        return [
            'code' => self::CITY_CODE,
            'name' => self::CITY_NAME,
            'feature' => $feature,
        ];
    }

    /**
     * Daftar seluruh kecamatan.
     */
    public function searchDistricts(
        ?string $keyword = null
    ): Collection {

        $keyword = mb_strtolower(trim($keyword ?? ''));

        $districts = $this->districts()
            ->map(function (array $feature) {

                $properties = $feature['properties'] ?? [];

                return [
                    'code' => $properties['id_kecamatan'],
                    'name' => $properties['nama_kecamatan'],
                    'region_code' => $properties['id_wilayah'],
                    'region_name' => $properties['nama_wilayah'],
                    'feature' => $feature,
                ];
            })
            ->unique('code')
            ->values();

        if ($keyword !== '') {

            $districts = $districts->filter(function (array $district) use ($keyword) {

                return str_contains(
                    mb_strtolower($district['name']),
                    $keyword
                );
            })->values();
        }

        return $districts
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();
    }

    /**
     * Detail kecamatan.
     */
    public function getDistrict(
        string $districtCode
    ): array {

        $district = $this->districts()
            ->first(function (array $feature) use ($districtCode) {

                return ($feature['properties']['id_kecamatan'] ?? null)
                    === $districtCode;
            });

        if (!$district) {
            throw new ModelNotFoundException(
                'Kecamatan tidak ditemukan.'
            );
        }

        $properties = $district['properties'];

        return [
            'code' => $properties['id_kecamatan'],
            'name' => $properties['nama_kecamatan'],
            'region_code' => $properties['id_wilayah'],
            'region_name' => $properties['nama_wilayah'],
            'feature' => $district,
        ];
    }

    /**
     * Daftar seluruh kelurahan.
     */
    public function searchVillages(
        string $districtCode,
        ?string $keyword = null
    ): Collection {

        $keyword = mb_strtolower(trim($keyword ?? ''));

        $villages = $this->villages()
            ->filter(function (array $feature) use ($districtCode) {

                return ($feature['properties']['id_kecamatan'] ?? null)
                    === $districtCode;
            })
            ->map(function (array $feature) {

                $properties = $feature['properties'] ?? [];

                return [
                    'code' => $properties['id_kelurahan'],
                    'name' => $properties['nama_kelurahan'],
                    'district_code' => $properties['id_kecamatan'],
                    'district_name' => $properties['nama_kecamatan'],
                    'region_code' => $properties['id_wilayah'],
                    'region_name' => $properties['nama_wilayah'],
                    'postal_code' => $properties['kodepos'] ?? null,
                    'rt' => $properties['jumlah_rt'] ?? null,
                    'rw' => $properties['jumlah_rw'] ?? null,
                    'feature' => $feature,
                ];
            })
            ->values();

        if ($keyword !== '') {

            $villages = $villages
                ->filter(function (array $village) use ($keyword) {

                    return str_contains(
                        mb_strtolower($village['name']),
                        $keyword
                    );
                })
                ->values();
        }

        return $villages
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();
    }

    /**
     * Detail kelurahan.
     */
    public function getVillage(
        string $villageCode
    ): array {

        $village = $this->villages()
            ->first(function (array $feature) use ($villageCode) {

                return ($feature['properties']['id_kelurahan'] ?? null)
                    === $villageCode;
            });

        if (!$village) {
            throw new ModelNotFoundException(
                'Kelurahan tidak ditemukan.'
            );
        }

        $properties = $village['properties'];

        return [
            'code' => $properties['id_kelurahan'],
            'name' => $properties['nama_kelurahan'],
            'district_code' => $properties['id_kecamatan'],
            'district_name' => $properties['nama_kecamatan'],
            'region_code' => $properties['id_wilayah'],
            'region_name' => $properties['nama_wilayah'],
            'postal_code' => $properties['kodepos'] ?? null,
            'rt' => $properties['jumlah_rt'] ?? null,
            'rw' => $properties['jumlah_rw'] ?? null,
            'feature' => $village,
        ];
    }

    /**
     * Mengambil polygon berdasarkan level wilayah.
     *
     * Level:
     * city
     * district
     * village
     */
    public function getPolygon(
        string $level,
        string $code = ''
    ): array {

        $level = strtolower(trim($level));

        return match ($level) {

            'city' => $this->getCity()['feature'],

            'district' => $this->getDistrict($code)['feature'],

            'village' => $this->getVillage($code)['feature'],

            default => throw new InvalidArgumentException(
                "Level wilayah '{$level}' tidak didukung."
            ),
        };
    }

    /**
     * Mengambil GeoJSON berdasarkan level wilayah.
     */
    public function getGeoJson(
        string $level,
        string $code = ''
    ): array {

        $feature = $this->getPolygon($level, $code);

        return [
            'type' => 'FeatureCollection',
            'features' => [
                $feature,
            ],
        ];
    }

    /**
     * Mengambil seluruh GeoJSON sesuai level.
     */
    public function getAllGeoJson(
        string $level
    ): array {

        $level = strtolower(trim($level));

        return match ($level) {

            'city' => $this->city(),

            'district' => [
                'type' => 'FeatureCollection',
                'features' => $this->districts()->values()->all(),
            ],

            'village' => [
                'type' => 'FeatureCollection',
                'features' => $this->villages()->values()->all(),
            ],

            default => throw new InvalidArgumentException(
                "Level wilayah '{$level}' tidak didukung."
            ),
        };
    }
}
