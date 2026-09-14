<?php

namespace App\Services\Geofence;

use App\Models\Device;
use App\Models\Geofence;
use Illuminate\Support\Collection;

class GeofenceCheckerService
{
    public function __construct(
        protected RadiusGeofenceService $radiusGeofenceService,
        protected PolygonGeofenceService $polygonGeofenceService,
    ) {}

    /**
     * Evaluasi SELURUH geofence aktif milik device.
     *
     * Setiap geofence punya statusnya sendiri (geofences.is_inside),
     * sehingga kendaraan yang keluar dari Radius tetapi masih berada di
     * dalam Administratif menghasilkan tepat satu kejadian "keluar" -
     * dan keluar dari dua area menghasilkan dua kejadian terpisah.
     *
     * Return:
     * [
     *      'results' => [
     *          [
     *              'geofence' => Geofence,
     *              'inside' => bool,
     *              'previous_inside' => ?bool,   null = belum pernah dicek
     *              'has_changed' => bool,
     *              'entered' => bool,
     *              'exited' => bool,
     *              'baseline' => bool,           evaluasi pertama
     *              'evaluation_failed' => bool,  config tidak terbaca
     *              'previous_state_changed_at' => ?Carbon,
     *          ],
     *          ...
     *      ],
     *
     *      // Ringkasan lintas geofence (dipakai payload realtime).
     *      'geofence' => ?Geofence,
     *      'inside' => bool,
     *      'previous_inside' => bool,
     *      'has_changed' => bool,
     *      'entered' => bool,
     *      'exited' => bool,
     * ]
     */
    public function process(
        Device $device,
        array $payload
    ): array {

        $geofences = $this->getActiveGeofences($device);

        if ($geofences->isEmpty()) {

            /*
            | Geofence terakhir baru saja dihapus/dinonaktifkan: penanda
            | ringkasan di device ikut dibersihkan supaya tidak tertinggal
            | "di dalam area" untuk area yang sudah tidak ada.
            */

            if ($device->is_inside_geofence) {

                $device->update([
                    'is_inside_geofence' => false,
                ]);
            }

            return $this->emptyResult();
        }

        $latitude = (float) $payload['lat'];

        $longitude = (float) $payload['lng'];

        $results = [];

        foreach ($geofences as $geofence) {

            $results[] = $this->evaluate(
                $geofence,
                $latitude,
                $longitude
            );
        }

        return $this->summarize(
            $device,
            $results
        );
    }

    /**
     * Evaluasi satu geofence.
     *
     * PENTING: status perpindahan (masuk/keluar) sengaja TIDAK disimpan
     * di sini. Penyimpanannya dilakukan GeofenceEventService setelah
     * riwayat dan notifikasi berhasil dibuat.
     *
     * Alasannya: antara pengecekan ini dan pengiriman notifikasi masih
     * ada broadcast realtime, reverse geocoding, dan beberapa penulisan
     * database. Kalau salah satunya gagal (mis. server realtime sedang
     * mati), payload dihentikan di tengah jalan. Jika status sudah
     * terlanjur disimpan, payload berikutnya menganggap perpindahan itu
     * sudah diproses - dan kejadian "keluar geofence" hilang selamanya
     * tanpa pernah memberi tahu siapa pun.
     *
     * Yang disimpan di sini hanyalah baseline (evaluasi pertama), karena
     * baseline tidak menghasilkan notifikasi apa pun.
     *
     * @return array<string, mixed>
     */
    protected function evaluate(
        Geofence $geofence,
        float $latitude,
        float $longitude
    ): array {

        $previousInside = $geofence->is_inside;

        $previousStateChangedAt = $geofence->state_changed_at;

        /*
        |--------------------------------------------------------------------------
        | Geofence config yang rusak (mis. hasil migrasi data lama) tidak
        | boleh menghentikan seluruh proses ingest GPS. Jika pengecekan
        | gagal, anggap status tidak berubah untuk siklus ini.
        |--------------------------------------------------------------------------
        */

        $evaluationFailed = false;

        try {

            $inside = $this->isInside(
                $geofence,
                $latitude,
                $longitude
            );
        } catch (\Throwable $exception) {

            report($exception);

            $evaluationFailed = true;

            $inside = (bool) $previousInside;
        }

        /*
        |--------------------------------------------------------------------------
        | Evaluasi pertama (is_inside masih NULL) hanya menetapkan baseline.
        | Tanpa ini, kendaraan yang memang sedang berada di dalam area akan
        | dianggap "baru masuk" dan mengirim notifikasi palsu - baik setelah
        | migrasi maupun sesaat setelah geofence baru dibuat.
        |--------------------------------------------------------------------------
        */

        $isBaseline = $previousInside === null;

        $entered = ! $isBaseline && ! $previousInside && $inside;

        $exited = ! $isBaseline && $previousInside && ! $inside;

        $hasChanged = $entered || $exited;

        /*
        |--------------------------------------------------------------------------
        | Baseline dari pengecekan yang GAGAL tidak boleh disimpan. Kalau
        | disimpan, konfigurasi yang sempat rusak akan mengunci status
        | "di luar area" - lalu begitu konfigurasinya diperbaiki, kendaraan
        | yang sejak awal diam di dalam area akan dikira baru saja masuk.
        |--------------------------------------------------------------------------
        */

        if ($isBaseline && ! $evaluationFailed) {

            $attributes = [
                'is_inside' => $inside,
            ];

            /*
            | Baseline "di dalam area" mengakhiri periode di luar, jadi
            | penanda pengingat ikut direset.
            */

            if ($inside) {

                $attributes['last_exit_notified_at'] = null;
            }

            $geofence->forceFill($attributes)->save();
        }

        return [

            'geofence' => $geofence,

            'inside' => $inside,

            'previous_inside' => $previousInside,

            'has_changed' => $hasChanged,

            'entered' => $entered,

            'exited' => $exited,

            'baseline' => $isBaseline,

            'evaluation_failed' => $evaluationFailed,

            'previous_state_changed_at' => $previousStateChangedAt,

        ];
    }

    /**
     * Bangun ringkasan lintas geofence dan sinkronkan kolom ringkasan
     * lama (devices.is_inside_geofence) yang masih dipakai payload
     * realtime. Kolom itu kini berarti: "berada di dalam MINIMAL satu
     * geofence aktif".
     *
     * @param  array<int, array<string, mixed>>  $results
     * @return array<string, mixed>
     */
    protected function summarize(
        Device $device,
        array $results
    ): array {

        $insideAny = false;

        $enteredAny = false;

        $exitedAny = false;

        foreach ($results as $result) {

            $insideAny = $insideAny || $result['inside'];

            $enteredAny = $enteredAny || $result['entered'];

            $exitedAny = $exitedAny || $result['exited'];
        }

        $previousInsideAny = (bool) $device->is_inside_geofence;

        if ($previousInsideAny !== $insideAny) {

            $device->update([
                'is_inside_geofence' => $insideAny,
            ]);
        }

        return [

            'results' => $results,

            'geofence' => $results[0]['geofence'] ?? null,

            'inside' => $insideAny,

            'previous_inside' => $previousInsideAny,

            'has_changed' => $previousInsideAny !== $insideAny,

            'entered' => $enteredAny,

            'exited' => $exitedAny,

        ];
    }

    /**
     * Hasil kosong ketika device belum punya geofence aktif.
     *
     * @return array<string, mixed>
     */
    protected function emptyResult(): array
    {
        return [

            'results' => [],

            'geofence' => null,

            'inside' => false,

            'previous_inside' => false,

            'has_changed' => false,

            'entered' => false,

            'exited' => false,

        ];
    }

    /**
     * Determine whether GPS coordinate
     * is inside geofence.
     */
    public function isInside(
        Geofence $geofence,
        float $latitude,
        float $longitude
    ): bool {

        return match ($geofence->type) {

            'radius'

            =>

            $this->radiusGeofenceService
                ->contains(

                    $geofence,

                    $latitude,

                    $longitude

                ),

            'administrative',

            'custom'

            =>

            $this->polygonGeofenceService
                ->contains(

                    $geofence,

                    $latitude,

                    $longitude

                ),

            default => false,
        };
    }

    /**
     * Seluruh geofence aktif milik device.
     *
     * BR-01 membatasi satu device maksimal satu geofence per tipe,
     * jadi koleksi ini paling banyak berisi tiga baris.
     *
     * @return Collection<int, Geofence>
     */
    protected function getActiveGeofences(
        Device $device
    ): Collection {

        if (! $device->relationLoaded('geofences')) {

            $device->load('geofences');
        }

        return $device->geofences

            ->where('status', true)

            ->values();
    }

    /**
     * Check whether device has active geofence.
     */
    public function hasActiveGeofence(
        Device $device
    ): bool {

        return $this->getActiveGeofences($device)->isNotEmpty();
    }

    /**
     * Return active geofence.
     */
    public function activeGeofence(
        Device $device
    ): ?Geofence {

        return $this->getActiveGeofences($device)->first();
    }
}
