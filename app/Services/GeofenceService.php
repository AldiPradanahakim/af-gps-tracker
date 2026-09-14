<?php

namespace App\Services;

use App\Models\Geofence;
use App\Repositories\GeofenceRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GeofenceService
{
    public function __construct(
        protected GeofenceRepository $repository
    ) {}

    /**
     * Ambil seluruh geofence milik user login.
     */
    public function getAll(): Collection
    {
        return $this->repository->getByUser(
            Auth::id()
        );
    }

    /**
     * Simpan geofence baru.
     *
     * device_id = integer -> simpan ke satu device.
     * device_id = "all"   -> simpan ke semua device milik user yang
     *                        belum memiliki geofence tipe tsb (BR-01).
     */
    public function store(array $data): Geofence|\Illuminate\Support\Collection
    {
        if ($data['device_id'] === 'all') {

            return $this->storeForAllDevices($data);
        }

        return $this->storeForSingleDevice($data);
    }

    /**
     * Simpan geofence untuk satu device (dengan validasi BR-01).
     */
    protected function storeForSingleDevice(array $data): Geofence
    {
        return DB::transaction(function () use ($data) {

            $device = $this->repository->findDeviceByUser(
                $data['device_id'],
                Auth::id()
            );

            if (! $device) {

                throw ValidationException::withMessages([
                    'device_id' => 'Device tidak ditemukan.',
                ]);
            }

            $this->assertTypeAvailable(
                $device->id,
                $data['type']
            );

            return $this->repository->create(
                $this->buildPayload($device, $data)
            );
        });
    }

    /**
     * Simpan geofence yang sama ke semua device milik user yang
     * belum memiliki geofence tipe tsb. Device yang sudah punya
     * dilewati (tidak membatalkan seluruh proses).
     */
    protected function storeForAllDevices(array $data): \Illuminate\Support\Collection
    {
        return DB::transaction(function () use ($data) {

            $devices = $this->repository->getDevicesByUser(
                Auth::id()
            );

            $created = collect();

            foreach ($devices as $device) {

                if ($this->repository->hasType($device->id, $data['type'])) {

                    continue;
                }

                $created->push(
                    $this->repository->create(
                        $this->buildPayload($device, $data)
                    )
                );
            }

            if ($created->isEmpty()) {

                throw ValidationException::withMessages([
                    'device_id' => 'Semua kendaraan sudah memiliki geofence tipe ini.',
                ]);
            }

            return $created;
        });
    }

    /**
     * Bangun payload create geofence untuk satu device.
     */
    protected function buildPayload($device, array $data): array
    {
        $config = match ($data['type']) {

            'radius' => $this->buildRadiusConfig(
                $device,
                $data
            ),

            'administrative' => $this->buildAdministrativeConfig(
                $data
            ),

            'custom' => $this->buildCustomConfig(
                $data
            ),

            default => throw ValidationException::withMessages([
                'type' => 'Tipe geofence tidak valid.',
            ]),
        };

        return [

            'device_id' => $device->id,

            'name' => $data['name'],

            'description' => $data['description'] ?? null,

            'type' => $data['type'],

            'config' => $config,

            'status' => true,

        ];
    }

    /**
     * BR-01: 1 device maksimal 1 geofence per tipe.
     */
    protected function assertTypeAvailable(
        string $deviceId,
        string $type
    ): void {

        if ($this->repository->hasType($deviceId, $type)) {

            throw ValidationException::withMessages([
                'type' => 'Kendaraan ini sudah memiliki geofence tipe '
                    . $this->typeLabel($type) . '.',
            ]);
        }
    }

    /**
     * Label tipe geofence untuk pesan error.
     */
    protected function typeLabel(string $type): string
    {
        return match ($type) {
            'radius' => 'Radius',
            'administrative' => 'Administratif',
            'custom' => 'Poligon',
            default => $type,
        };
    }

    /**
     * Tipe geofence yang sudah dimiliki tiap device milik user login.
     * Dipakai frontend untuk menonaktifkan pilihan yang sudah penuh.
     */
    public function getTypesByUser(): array
    {
        return $this->repository->getTypesByUser(
            Auth::id()
        );
    }
    /**
     * Bangun konfigurasi geofence radius.
     */
    protected function buildRadiusConfig(
        $device,
        array $data
    ): array {

        $radius = (float) $data['radius'];

        if ($radius <= 0) {

            throw ValidationException::withMessages([
                'radius' => 'Radius harus lebih dari 0.',
            ]);
        }

        $source = $data['radius_source'];

        $center = $this->resolveRadiusCenter(
            $device,
            $source,
            $data
        );

        return [

            'center' => $center,

            'radius' => $radius,

            'unit' => $data['radius_unit'] ?? 'meter',

            'source' => $source,

        ];
    }

    /**
     * Resolve titik pusat radius dari sumber yang dipilih.
     *
     * Dipakai bersama oleh create dan update supaya titik pusat SELALU
     * diambil dari sumber data terbaru di server, bukan dari snapshot
     * koordinat yang dikirim frontend (snapshot itu bisa basi, misalnya
     * ketika Home Location baru saja diubah di halaman yang sama).
     *
     * @return array{lat: float, lng: float}
     */
    protected function resolveRadiusCenter(
        $device,
        string $source,
        array $data
    ): array {

        switch ($source) {

            case 'home_location':

                $home = $device?->home_location;

                if (
                    empty($home) ||
                    !isset($home['lat']) ||
                    !isset($home['lng'])
                ) {

                    throw ValidationException::withMessages([
                        'radius_source' => 'Lokasi Rumah belum tersedia. Atur Lokasi Rumah kendaraan ini terlebih dahulu.',
                    ]);
                }

                return [
                    'lat' => (float) $home['lat'],
                    'lng' => (float) $home['lng'],
                ];

            case 'current_location':

                $history = $device?->travelHistories()
                    ->latest('received_at')
                    ->first();

                if (! $history) {

                    throw ValidationException::withMessages([
                        'radius_source' => 'Lokasi terakhir perangkat tidak ditemukan.',
                    ]);
                }

                $location = $history->location;

                if (
                    empty($location) ||
                    !isset($location['lat']) ||
                    !isset($location['lng'])
                ) {

                    throw ValidationException::withMessages([
                        'radius_source' => 'Lokasi terakhir tidak valid.',
                    ]);
                }

                return [
                    'lat' => (float) $location['lat'],
                    'lng' => (float) $location['lng'],
                ];

            case 'manual':

                if (
                    ! isset($data['latitude']) ||
                    ! isset($data['longitude']) ||
                    $data['latitude'] === '' ||
                    $data['longitude'] === ''
                ) {

                    throw ValidationException::withMessages([
                        'radius_source' => 'Titik pusat manual belum ditentukan.',
                    ]);
                }

                return [
                    'lat' => (float) $data['latitude'],
                    'lng' => (float) $data['longitude'],
                ];

            default:

                throw ValidationException::withMessages([
                    'radius_source' => 'Sumber titik pusat tidak dikenali.',
                ]);
        }
    }

    /**
     * Bangun konfigurasi geofence administratif.
     */
    protected function buildAdministrativeConfig(
        array $data
    ): array {

        if (
            empty($data['geojson']) ||
            empty($data['display_name']) ||
            empty($data['administrative_type'])
        ) {

            throw ValidationException::withMessages([
                'geojson' => 'Data wilayah administratif tidak lengkap.',
            ]);
        }

        $geojson = is_string($data['geojson'])
            ? json_decode($data['geojson'], true)
            : $data['geojson'];

        if (
            json_last_error() !== JSON_ERROR_NONE ||
            !is_array($geojson)
        ) {

            throw ValidationException::withMessages([
                'geojson' => 'GeoJSON tidak valid.',
            ]);
        }

        if (
            isset($geojson['type']) &&
            $geojson['type'] === 'FeatureCollection'
        ) {

            $geojson = $geojson['features'][0]['geometry'] ?? null;
        }

        if (
            !is_array($geojson) ||
            !isset($geojson['type']) ||
            !in_array(
                $geojson['type'],
                ['Polygon', 'MultiPolygon']
            )
        ) {

            throw ValidationException::withMessages([
                'geojson' => 'GeoJSON harus berupa Polygon atau MultiPolygon.',
            ]);
        }

        $this->assertPolygonWithinLimits($geojson);

        return [

            'display_name' => $data['display_name'],

            'administrative_type' => $data['administrative_type'],

            'geometry' => $geojson,

        ];
    }

    /**
     * Bangun konfigurasi geofence custom polygon.
     */
    protected function buildCustomConfig(
        array $data
    ): array {

        if (empty($data['geojson'])) {

            throw ValidationException::withMessages([
                'geojson' => 'Poligon belum dibuat.',
            ]);
        }

        $geojson = is_string($data['geojson'])
            ? json_decode($data['geojson'], true)
            : $data['geojson'];

        if (
            json_last_error() !== JSON_ERROR_NONE ||
            !is_array($geojson)
        ) {

            throw ValidationException::withMessages([
                'geojson' => 'GeoJSON tidak valid.',
            ]);
        }

        /*
|--------------------------------------------------------------------------
| Feature -> Polygon
|--------------------------------------------------------------------------
*/

        if (
            isset($geojson['type']) &&
            $geojson['type'] === 'Feature'
        ) {

            $geojson = $geojson['geometry'] ?? [];
        }

        /*
|--------------------------------------------------------------------------
| Validation
|--------------------------------------------------------------------------
*/

        if (

            !isset($geojson['type']) ||

            !in_array(

                $geojson['type'],

                [

                    'Polygon',

                    'MultiPolygon'

                ]

            )

        ) {

            throw ValidationException::withMessages([

                'geojson' => 'Poligon tidak valid.',

            ]);
        }

        $this->assertPolygonWithinLimits($geojson);

        return [

            'geometry' => $geojson,

        ];
    }

    /**
     * Update geofence (nama, status, dan geometry).
     *
     * Tipe geofence tidak bisa diubah lewat edit -- hanya nama, status,
     * dan geometry (titik/radius untuk radius, wilayah/polygon untuk
     * administrative & custom) yang bisa diperbarui.
     */
    public function update(string $id, array $data): Geofence
    {
        return DB::transaction(function () use ($id, $data) {

            $geofence = $this->repository->findOwnedByUser(
                $id,
                Auth::id()
            );

            if (! $geofence) {

                throw ValidationException::withMessages([
                    'geofence' => 'Geofence tidak ditemukan.',
                ]);
            }

            $config = match ($geofence->type) {

                'radius' => $this->mergeRadiusConfig(
                    $geofence->config ?? [],
                    $data,
                    $geofence->device
                ),

                'administrative' => $this->mergeGeometryConfig(
                    $geofence->config ?? [],
                    $data
                ),

                'custom' => $this->mergeGeometryConfig(
                    $geofence->config ?? [],
                    $data
                ),

                default => $geofence->config ?? [],
            };

            $status = filter_var(
                $data['status'],
                FILTER_VALIDATE_BOOLEAN
            );

            $attributes = [

                'name' => $data['name'],

                'description' => $data['description'] ?? null,

                'status' => $status,

                'config' => $config,

            ];

            /*
            |--------------------------------------------------------------------------
            | Kalau area berubah bentuk (titik pusat / radius / polygon), atau
            | geofence baru diaktifkan kembali, status masuk/keluar yang lama
            | sudah tidak sahih. Dikembalikan ke NULL supaya payload GPS
            | berikutnya menetapkan baseline tanpa mengirim notifikasi palsu -
            | mis. "masuk area" padahal kendaraan tidak bergerak sama sekali,
            | hanya radiusnya yang diperbesar.
            |--------------------------------------------------------------------------
            */

            $areaChanged = ($geofence->config ?? []) != $config;

            $reactivated = $status && ! $geofence->status;

            if ($areaChanged || $reactivated) {

                $attributes['is_inside'] = null;

                $attributes['state_changed_at'] = null;

                /*
                | last_exit_notified_at SENGAJA tidak ikut dikosongkan.
                |
                | Kalau dikosongkan, mengedit geofence saat kendaraan sedang
                | berada di luar area akan menghentikan pengingat "masih di
                | luar" secara diam-diam - dan pengingat itu baru hidup lagi
                | setelah kendaraan masuk lalu keluar sekali penuh, yang bisa
                | saja tidak pernah terjadi.
                |
                | Penanda ini tetap dibersihkan secara otomatis begitu
                | baseline berikutnya menyimpulkan kendaraan ada di dalam
                | area (lihat GeofenceCheckerService::evaluate()).
                */
            }

            return $this->repository->update($geofence, $attributes);
        });
    }

    /**
     * Merge perubahan titik/radius ke config radius yang sudah ada.
     * Field yang tidak dikirim tetap memakai nilai lama.
     *
     * radius_source menentukan dari mana titik pusat baru diambil:
     *
     *   keep_current      -> pertahankan titik yang tersimpan
     *   home_location     -> ambil ulang dari Home Location device
     *   current_location  -> ambil ulang dari lokasi GPS terakhir
     *   manual            -> pakai latitude/longitude yang dikirim
     *
     * Titik pusat selalu di-resolve ulang di server supaya tidak ikut
     * basi kalau Home Location baru saja diubah tanpa reload halaman.
     */
    protected function mergeRadiusConfig(
        array $config,
        array $data,
        $device = null
    ): array {

        $source = $data['radius_source'] ?? null;

        if (
            $source !== null &&
            $source !== '' &&
            $source !== 'keep_current'
        ) {

            $config['center'] = $this->resolveRadiusCenter(
                $device,
                $source,
                $data
            );

            /*
            |--------------------------------------------------------------------------
            | Sumber ikut disimpan supaya halaman Ubah Radius bisa memberi
            | tahu pengguna titik pusatnya diambil dari mana. Titik itu
            | sendiri TIDAK pernah berpindah sendiri - memindahkan Lokasi
            | Rumah tidak menggeser geofence yang sudah dibuat.
            |--------------------------------------------------------------------------
            */

            $config['source'] = $source;

        } elseif (
            isset($data['latitude']) &&
            isset($data['longitude']) &&
            $data['latitude'] !== '' &&
            $data['longitude'] !== ''
        ) {

            /*
            |--------------------------------------------------------------------------
            | Kompatibilitas: request lama yang hanya mengirim latitude /
            | longitude tanpa radius_source tetap bisa menggeser titik.
            |--------------------------------------------------------------------------
            */

            $config['center'] = [

                'lat' => (float) $data['latitude'],

                'lng' => (float) $data['longitude'],

            ];
        }

        if (
            isset($data['radius']) &&
            $data['radius'] !== ''
        ) {

            $config['radius'] = (float) $data['radius'];
        }

        return $config;
    }

    /**
     * Merge perubahan wilayah/polygon (administrative & custom) ke
     * config yang sudah ada. Field yang tidak dikirim tetap memakai
     * geometry lama.
     */
    protected function mergeGeometryConfig(
        array $config,
        array $data
    ): array {

        if (! empty($data['geojson'])) {

            $config['geometry'] = $this->normalizeGeoJson(
                $data['geojson']
            );
        }

        if (! empty($data['display_name'])) {

            $config['display_name'] = $data['display_name'];
        }

        if (! empty($data['administrative_type'])) {

            $config['administrative_type'] = $data['administrative_type'];
        }

        return $config;
    }

    /**
     * Normalisasi GeoJSON (Feature / FeatureCollection -> Polygon geometry).
     */
    protected function normalizeGeoJson(
        string|array $geojson
    ): array {

        $decoded = is_string($geojson)
            ? json_decode($geojson, true)
            : $geojson;

        if (
            json_last_error() !== JSON_ERROR_NONE ||
            ! is_array($decoded)
        ) {

            throw ValidationException::withMessages([
                'geojson' => 'GeoJSON tidak valid.',
            ]);
        }

        if (($decoded['type'] ?? null) === 'FeatureCollection') {

            $decoded = $decoded['features'][0]['geometry'] ?? null;
        }

        if (($decoded['type'] ?? null) === 'Feature') {

            $decoded = $decoded['geometry'] ?? null;
        }

        if (
            ! is_array($decoded) ||
            ! in_array(
                $decoded['type'] ?? null,
                ['Polygon', 'MultiPolygon']
            )
        ) {

            throw ValidationException::withMessages([
                'geojson' => 'GeoJSON harus berupa Polygon atau MultiPolygon.',
            ]);
        }

        $this->assertPolygonWithinLimits($decoded);

        return $decoded;
    }

    /**
     * Cegah polygon yang terlalu kompleks (bisa dipakai untuk DoS lewat
     * perhitungan point-in-polygon yang berjalan sinkron untuk setiap
     * payload GPS yang masuk).
     */
    private function assertPolygonWithinLimits(array $geometry): void
    {
        $numbers = 0;

        $coordinates = $geometry['coordinates'] ?? [];

        array_walk_recursive(
            $coordinates,
            function ($value) use (&$numbers) {

                if (is_numeric($value)) {
                    $numbers++;
                }
            }
        );

        if (intdiv($numbers, 2) > 5000) {

            throw ValidationException::withMessages([
                'geojson' => 'Poligon terlalu kompleks (maksimal 5000 titik koordinat).',
            ]);
        }
    }

    /**
     * Update status geofence.
     */
    public function updateStatus(
        string $id,
        bool $status
    ): Geofence {

        $geofence = $this->repository->findOwnedByUser(
            $id,
            Auth::id()
        );

        if (! $geofence) {

            throw ValidationException::withMessages([
                'geofence' => 'Geofence tidak ditemukan.',
            ]);
        }

        return DB::transaction(function () use (
            $geofence,
            $status
        ) {

            /*
            |--------------------------------------------------------------------------
            | Geofence yang dinonaktifkan berhenti dievaluasi, jadi statusnya
            | membeku di nilai terakhir. Saat diaktifkan lagi, status itu bisa
            | sudah tidak sesuai kenyataan - reset ke NULL supaya dievaluasi
            | ulang dari nol tanpa notifikasi palsu.
            |--------------------------------------------------------------------------
            */

            if ($status && ! $geofence->status) {

                /*
                | last_exit_notified_at dipertahankan dengan alasan yang sama
                | seperti pada update() - lihat catatan di sana.
                */

                $geofence->forceFill([
                    'is_inside' => null,
                    'state_changed_at' => null,
                ])->save();
            }

            return $this->repository->updateStatus(
                $geofence,
                $status
            );
        });
    }

    /**
     * Hapus satu geofence.
     */
    public function destroy(
        string $id
    ): void {

        $geofence = $this->repository->findOwnedByUser(
            $id,
            Auth::id()
        );

        if (! $geofence) {

            throw ValidationException::withMessages([
                'geofence' => 'Geofence tidak ditemukan.',
            ]);
        }

        DB::transaction(function () use (
            $geofence
        ) {

            $this->repository->delete(
                $geofence
            );
        });
    }

    /**
     * Hapus banyak geofence.
     */
    public function destroyMany(
        array $ids
    ): int {

        $ownedIds = [];

        foreach ($ids as $id) {

            $geofence = $this->repository->findOwnedByUser(
                $id,
                Auth::id()
            );

            if ($geofence) {

                $ownedIds[] = $geofence->id;
            }
        }

        if (empty($ownedIds)) {

            throw ValidationException::withMessages([
                'ids' => 'Tidak ada geofence yang dapat dihapus.',
            ]);
        }

        return DB::transaction(function () use (
            $ownedIds
        ) {

            return $this->repository->deleteMany(
                $ownedIds
            );
        });
    }

    public function getByDevice(
        string $deviceId
    ) {
        $device = $this->repository->findDeviceByUser(
            $deviceId,
            Auth::id()
        );

        if (! $device) {

            throw ValidationException::withMessages([
                'device_id' => 'Device tidak ditemukan.',
            ]);
        }

        return $this->repository->getByDevice(
            $device->id
        );
    }
    public function all()
    {
        return $this->repository->getByUser(
            Auth::id()
        );
    }
}
