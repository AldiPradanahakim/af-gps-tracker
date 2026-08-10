<?php

namespace App\Services\Geofence;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Throwable;

class ReverseGeocodingService
{
    /**
     * Nominatim Reverse Geocoding API (fallback gratis, dipakai kalau
     * LOCATIONIQ_API_KEY belum diisi).
     */
    protected string $nominatimEndpoint =
    'https://nominatim.openstreetmap.org/reverse';

    /**
     * LocationIQ Reverse Geocoding API (dipakai kalau LOCATIONIQ_API_KEY
     * terisi - limit jauh lebih longgar daripada Nominatim gratis, dan
     * datanya lebih lengkap untuk tempat kecil di Indonesia).
     */
    protected string $locationIqEndpoint =
    'https://us1.locationiq.com/v1/reverse';

    /**
     * Presisi pembulatan koordinat untuk cache key (jumlah digit desimal).
     *
     * 4 digit desimal ~= 11 meter di garis khatulistiwa. Dipilih supaya
     * kendaraan yang diam/parkir atau bergerak pelan (ping GPS setiap
     * detik dengan drift GPS beberapa meter) memakai alamat yang sama
     * dari cache, tanpa menggabungkan titik-titik yang benar-benar
     * berbeda (mis. dua sisi jalan/persimpangan yang berdekatan).
     */
    protected int $coordinatePrecision = 4;

    /**
     * TTL cache alamat hasil reverse geocoding.
     *
     * Alamat jalan untuk koordinat tetap pada dasarnya permanen, jadi
     * TTL panjang (30 hari) aman dan sangat mengurangi jumlah request
     * ke Nominatim untuk lokasi yang sering dikunjungi (rumah, kantor,
     * rute rutin).
     */
    protected int $cacheTtlDays = 30;

    /**
     * Prefix key cache alamat.
     */
    protected string $cacheKeyPrefix = 'reverse-geocode:';

    /**
     * Cache key penanda waktu request terakhir ke Nominatim (dipakai
     * lintas seluruh aplikasi, bukan per device, supaya throttle-nya
     * benar-benar global).
     */
    protected string $rateLimitKey = 'reverse-geocode:last-request-at';

    /**
     * Jarak minimum antar request ke Nominatim (mikrodetik).
     *
     * Kebijakan Nominatim membatasi maksimal 1 request/detik. Dibuat
     * sedikit di atas 1 detik penuh untuk memberi jeda aman.
     */
    protected int $minIntervalMicroseconds = 1_100_000;

    /**
     * Jarak minimum antar request ke LocationIQ (mikrodetik).
     *
     * Free tier LocationIQ mengizinkan ~2 request/detik - tetap
     * di-throttle (bukan dihilangkan) supaya tidak pernah melanggar
     * limit walau banyak device mengirim ping bersamaan.
     */
    protected int $locationIqMinIntervalMicroseconds = 600_000;

    /**
     * Batas atas total waktu tunggu saat throttling (mikrodetik).
     *
     * Dipanggil secara sinkron di dalam pipeline ingest GPS, jadi wait
     * dibatasi (bounded) supaya tidak pernah menggantung tanpa batas
     * ketika banyak request menumpuk bersamaan.
     */
    protected int $maxWaitMicroseconds = 5_000_000;

    /**
     * Convert latitude & longitude into address.
     *
     * @throws InvalidArgumentException
     */
    public function search(
        float $latitude,
        float $longitude
    ): string {

        $this->validateCoordinate(
            $latitude,
            $longitude
        );

        $cacheKey = $this->cacheKeyPrefix . $this->roundCoordinate($latitude)
            . ':' . $this->roundCoordinate($longitude);

        $cached = Cache::get($cacheKey);

        if ($cached !== null) {
            return $cached;
        }

        $address = $this->fetchAddress(
            $latitude,
            $longitude
        );

        /*
        |--------------------------------------------------------------------------
        | Hanya cache hasil yang valid
        |--------------------------------------------------------------------------
        |
        | Placeholder 'Alamat tidak ditemukan' sengaja TIDAK di-cache
        | supaya kegagalan sementara (timeout, Nominatim down/blocked)
        | tidak "membekukan" alamat sebuah koordinat menjadi placeholder
        | selama 30 hari - percobaan berikutnya harus tetap mencoba
        | Nominatim lagi (tetap lewat throttle global yang sama).
        |--------------------------------------------------------------------------
        */

        if ($address !== 'Alamat tidak ditemukan') {

            Cache::put(
                $cacheKey,
                $address,
                now()->addDays($this->cacheTtlDays)
            );
        }

        return $address;
    }

    /**
     * Bulatkan koordinat ke presisi cache yang sudah ditentukan.
     */
    protected function roundCoordinate(
        float $coordinate
    ): string {

        return number_format(
            $coordinate,
            $this->coordinatePrecision,
            '.',
            ''
        );
    }

    /**
     * Panggil provider geocoding (LocationIQ kalau API key terisi,
     * fallback ke Nominatim gratis), dengan throttle global sesuai
     * limit masing-masing provider.
     */
    protected function fetchAddress(
        float $latitude,
        float $longitude
    ): string {

        try {

            $usingLocationIq = $this->usingLocationIq();

            $this->throttle(
                $usingLocationIq
                    ? $this->locationIqMinIntervalMicroseconds
                    : $this->minIntervalMicroseconds
            );

            $response = $usingLocationIq

                ? Http::timeout(10)->get($this->locationIqEndpoint, [

                    'key' => config('services.locationiq.key'),

                    'format' => 'json',

                    'lat' => $latitude,

                    'lon' => $longitude,

                    'addressdetails' => 1,

                ])

                : Http::timeout(10)

                    ->withHeaders([

                        'User-Agent' => config('app.name') . '/1.0',

                        'Accept' => 'application/json',

                    ])

                    ->get($this->nominatimEndpoint, [

                        'format' => 'jsonv2',

                        'lat' => $latitude,

                        'lon' => $longitude,

                        'addressdetails' => 1,

                    ]);

            $response->throw();

            return $this->extractAddress(

                $response->json()

            );
        } catch (RequestException $exception) {

            report($exception);

            return 'Alamat tidak ditemukan';
        } catch (Throwable $exception) {

            report($exception);

            return 'Alamat tidak ditemukan';
        }
    }

    /**
     * Apakah LocationIQ dikonfigurasi (LOCATIONIQ_API_KEY terisi).
     */
    protected function usingLocationIq(): bool
    {
        return filled(
            config('services.locationiq.key')
        );
    }

    /**
     * Pastikan jeda antar request ke Nominatim mematuhi batas 1
     * request/detik, secara global untuk seluruh aplikasi.
     *
     * Memakai Cache::lock sebagai mutex singkat supaya beberapa
     * request yang datang bersamaan (mis. beberapa device mengirim
     * ping di detik yang sama) antre menunggu giliran, bukan
     * memukul Nominatim secara paralel. Wait dibatasi (bounded)
     * dengan $maxWaitMicroseconds supaya panggilan sinkron di
     * pipeline ingest GPS tidak pernah menggantung tanpa batas.
     */
    protected function throttle(int $minIntervalMicroseconds): void
    {
        $lock = Cache::lock(
            $this->rateLimitKey . ':lock',
            5
        );

        $lockAcquired = $lock->block(5);

        if (! $lockAcquired) {

            // Tidak berhasil mendapat lock dalam batas waktu - lanjut
            // saja tanpa throttle tambahan daripada menggantung lebih
            // lama. Provider tetap akan menolak request yang benar-
            // benar melanggar rate limit, dan itu ditangani sebagai
            // kegagalan biasa oleh try/catch di fetchAddress().
            return;
        }

        try {

            $lastRequestAt = Cache::get($this->rateLimitKey);

            $now = microtime(true);

            if ($lastRequestAt !== null) {

                $elapsedMicroseconds = (int) round(
                    ($now - $lastRequestAt) * 1_000_000
                );

                $waitMicroseconds = min(
                    max(
                        $minIntervalMicroseconds - $elapsedMicroseconds,
                        0
                    ),
                    $this->maxWaitMicroseconds
                );

                if ($waitMicroseconds > 0) {
                    usleep($waitMicroseconds);
                }
            }

            Cache::put(
                $this->rateLimitKey,
                microtime(true),
                now()->addMinutes(5)
            );

        } finally {

            $lock->release();
        }
    }

    /**
     * Validate latitude and longitude.
     *
     * @throws InvalidArgumentException
     */
    protected function validateCoordinate(
        float $latitude,
        float $longitude
    ): void {

        if (
            $latitude < -90 ||
            $latitude > 90
        ) {

            throw new InvalidArgumentException(
                'Invalid latitude.'
            );
        }

        if (
            $longitude < -180 ||
            $longitude > 180
        ) {

            throw new InvalidArgumentException(
                'Invalid longitude.'
            );
        }
    }

    /**
     * Extract readable address.
     */
    protected function extractAddress(
        array $result
    ): string {

        return $result['display_name']
            ?? 'Alamat tidak ditemukan';
    }
}
