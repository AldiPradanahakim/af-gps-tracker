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

    private const NATIONAL_BASE_PATH = 'administrative/geojson';

    private ?array $city = null;

    private ?Collection $districts = null;

    private ?Collection $villages = null;

    private ?Collection $provinces = null;

    private ?Collection $regencies = null;

    /**
     * Membaca file GeoJSON.
     */
    private function readGeoJson(
        string $filename,
        string $basePath = self::BASE_PATH
    ): array {
        return Cache::remember(
            'administrative-geojson:' . $basePath . '/' . $filename,
            now()->addWeek(),
            function () use ($filename, $basePath) {

                $path = $basePath . '/' . $filename;

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
     * Seluruh polygon provinsi (nasional, tidak terikat kota tertentu).
     */
    private function provinces(): Collection
    {
        if ($this->provinces === null) {
            $json = $this->readGeoJson(
                'province.json',
                self::NATIONAL_BASE_PATH
            );

            $this->provinces = collect(
                $json['features'] ?? []
            );
        }

        return $this->provinces;
    }

    /**
     * Daftar seluruh provinsi.
     */
    public function searchProvinces(
        ?string $keyword = null
    ): Collection {

        $keyword = mb_strtolower(trim($keyword ?? ''));

        $provinces = $this->provinces()
            ->map(function (array $feature) {

                $properties = $feature['properties'] ?? [];

                return [
                    'code' => $properties['KODE_PROV'] ?? null,
                    'name' => $properties['PROVINSI'] ?? null,
                    'feature' => $feature,
                ];
            })
            ->filter(fn(array $province) => $province['code'] !== null)
            ->unique('code')
            ->values();

        if ($keyword !== '') {

            $provinces = $provinces->filter(function (array $province) use ($keyword) {

                return str_contains(
                    mb_strtolower($province['name'] ?? ''),
                    $keyword
                );
            })->values();
        }

        return $provinces
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();
    }

    /**
     * Detail provinsi.
     */
    public function getProvince(
        string $provinceCode
    ): array {

        $province = $this->provinces()
            ->first(function (array $feature) use ($provinceCode) {

                return ($feature['properties']['KODE_PROV'] ?? null)
                    === $provinceCode;
            });

        if (!$province) {
            throw new ModelNotFoundException(
                'Provinsi tidak ditemukan.'
            );
        }

        $properties = $province['properties'];

        return [
            'code' => $properties['KODE_PROV'],
            'name' => $properties['PROVINSI'],
            'feature' => $province,
        ];
    }

    /**
     * Seluruh polygon kabupaten/kota (nasional, tidak terikat provinsi
     * tertentu).
     */
    private function regencies(): Collection
    {
        if ($this->regencies === null) {
            $json = $this->readGeoJson(
                'regency.json',
                self::NATIONAL_BASE_PATH
            );

            $this->regencies = collect(
                $json['features'] ?? []
            );
        }

        return $this->regencies;
    }

    /**
     * Daftar seluruh kabupaten/kota.
     */
    public function searchRegencies(
        ?string $keyword = null
    ): Collection {

        $keyword = mb_strtolower(trim($keyword ?? ''));

        $regencies = $this->regencies()
            ->map(function (array $feature) {

                $properties = $feature['properties'] ?? [];

                return [
                    'code' => $properties['KDPKAB'] ?? null,
                    'name' => $properties['WADMKK'] ?? null,
                    'province_code' => $properties['KDPPUM'] ?? null,
                    'province_name' => $properties['WADMPR'] ?? null,
                    'feature' => $feature,
                ];
            })
            ->filter(fn(array $regency) => $regency['code'] !== null && $regency['name'] !== null)
            ->unique('code')
            ->values();

        if ($keyword !== '') {

            $regencies = $regencies->filter(function (array $regency) use ($keyword) {

                return str_contains(
                    mb_strtolower($regency['name'] ?? ''),
                    $keyword
                );
            })->values();
        }

        return $regencies
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();
    }

    /**
     * Detail kabupaten/kota.
     */
    public function getRegency(
        string $regencyCode
    ): array {

        $regency = $this->regencies()
            ->first(function (array $feature) use ($regencyCode) {

                return ($feature['properties']['KDPKAB'] ?? null)
                    === $regencyCode;
            });

        if (!$regency) {
            throw new ModelNotFoundException(
                'Kabupaten/Kota tidak ditemukan.'
            );
        }

        $properties = $regency['properties'];

        return [
            'code' => $properties['KDPKAB'],
            'name' => $properties['WADMKK'],
            'province_code' => $properties['KDPPUM'] ?? null,
            'province_name' => $properties['WADMPR'] ?? null,
            'feature' => $regency,
        ];
    }

    /**
     * Informasi Kota Bandung.
     */
    public function getCity(): array
    {
        $feature = $this->city()['features'][0] ?? null;

        if (!$feature) {
            throw new ModelNotFoundException(
                'Poligon Kota Bandung tidak ditemukan.'
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

            'province' => $this->getProvince($code)['feature'],

            'regency' => $this->getRegency($code)['feature'],

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

            'province' => [
                'type' => 'FeatureCollection',
                'features' => $this->provinces()->values()->all(),
            ],

            'regency' => [
                'type' => 'FeatureCollection',
                'features' => $this->regencies()->values()->all(),
            ],

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
