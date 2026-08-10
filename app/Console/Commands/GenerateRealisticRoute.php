<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\DeviceLog;
use App\Models\TravelHistory;
use App\Services\Geofence\ReverseGeocodingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GenerateRealisticRoute extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'route:generate
        {device? : UUID atau device_id kendaraan. Kosongkan untuk memakai kendaraan pertama}
        {--to=Gedung Sate, Bandung : Nama tujuan (dicari lewat Nominatim)}
        {--points=60 : Jumlah titik GPS yang dibuat sepanjang rute}';

    /**
     * The console command description.
     */
    protected $description = 'Buat data riwayat perjalanan REALISTIS (mengikuti jalan asli, bukan garis lurus) dari home_location device menuju sebuah tujuan, untuk demo Riwayat Perjalanan/Playback/Route.';

    public function handle(
        ReverseGeocodingService $reverseGeocodingService
    ): int {

        $device = $this->resolveDevice();

        if (! $device) {

            $this->error('Kendaraan tidak ditemukan.');

            return self::FAILURE;
        }

        $origin = [

            'lat' => (float) data_get($device->home_location, 'lat'),

            'lng' => (float) data_get($device->home_location, 'lng'),

        ];

        if (! $origin['lat'] || ! $origin['lng']) {

            $this->error('Device belum punya home_location. Set dulu lewat halaman Detail Kendaraan.');

            return self::FAILURE;
        }

        $this->info("Asal (Rumah): {$origin['lat']}, {$origin['lng']}");

        $destinationKeyword = $this->option('to');

        $destination = $this->geocode($destinationKeyword);

        if (! $destination) {

            $this->error("Tujuan \"{$destinationKeyword}\" tidak ditemukan.");

            return self::FAILURE;
        }

        $this->info("Tujuan ({$destinationKeyword}): {$destination['lat']}, {$destination['lng']}");

        $geometry = $this->fetchRoute($origin, $destination);

        if (! $geometry) {

            $this->error('Gagal mengambil rute dari OSRM. Coba lagi atau cek koneksi internet.');

            return self::FAILURE;
        }

        $this->info(count($geometry) . ' titik mentah dari OSRM, di-downsample...');

        $points = $this->downsample(

            $geometry,

            (int) $this->option('points')

        );

        $this->info(count($points) . ' titik akan dibuat (mengikuti jalan asli).');

        $rows = $this->buildRows($points, $reverseGeocodingService);

        DB::transaction(function () use ($device, $rows) {

            foreach ($rows as $row) {

                $deviceLog = DeviceLog::create([

                    'device_id' => $device->id,

                    'message_id' => $row['message_id'],

                    'payload' => $row['payload'],

                    'status' => 'valid',

                    'received_at' => $row['received_at'],

                ]);

                TravelHistory::create([

                    'device_log_id' => $deviceLog->id,

                    'device_id' => $device->id,

                    'location' => $row['payload'],

                    'search_address' => $row['address'],

                    'received_at' => $row['received_at'],

                ]);
            }
        });

        $this->newLine();

        $this->info(count($rows) . ' titik riwayat perjalanan berhasil dibuat, mengikuti jalan asli dari rumah menuju ' . $destinationKeyword . '.');

        return self::SUCCESS;
    }

    /**
     * Cari device.
     */
    protected function resolveDevice(): ?Device
    {
        $identifier = $this->argument('device');

        if (! $identifier) {

            return Device::query()->first();
        }

        return Device::query()
            ->where('id', $identifier)
            ->orWhere('device_id', $identifier)
            ->first();
    }

    /**
     * Geocode nama tempat -> koordinat (LocationIQ kalau
     * LOCATIONIQ_API_KEY terisi, fallback ke Nominatim gratis).
     */
    protected function geocode(string $keyword): ?array
    {
        $locationIqKey = config('services.locationiq.key');

        $response = $locationIqKey

            ? Http::timeout(10)->get('https://us1.locationiq.com/v1/search', [

                'key' => $locationIqKey,

                'q' => $keyword,

                'format' => 'json',

                'limit' => 1,

                'countrycodes' => 'id',

            ])

            : Http::timeout(10)

                ->withHeaders(['User-Agent' => config('app.name') . '/1.0'])

                ->get('https://nominatim.openstreetmap.org/search', [

                    'q' => $keyword,

                    'format' => 'jsonv2',

                    'limit' => 1,

                    'countrycodes' => 'id',

                ]);

        $result = $response->json()[0] ?? null;

        if (! $result) {

            return null;
        }

        return [

            'lat' => (float) $result['lat'],

            'lng' => (float) $result['lon'],

        ];
    }

    /**
     * Ambil geometri rute (mengikuti jalan asli) dari OSRM routing service.
     *
     * @return array<int, array{lat: float, lng: float}>|null
     */
    protected function fetchRoute(array $origin, array $destination): ?array
    {
        $coordinates = "{$origin['lng']},{$origin['lat']};{$destination['lng']},{$destination['lat']}";

        $response = Http::timeout(20)

            ->get("https://router.project-osrm.org/route/v1/driving/{$coordinates}", [

                'overview' => 'full',

                'geometries' => 'geojson',

            ]);

        if (! $response->successful()) {

            return null;
        }

        $body = $response->json();

        if (($body['code'] ?? null) !== 'Ok' || empty($body['routes'][0])) {

            return null;
        }

        $coordinatesGeoJson = $body['routes'][0]['geometry']['coordinates'] ?? [];

        return array_map(

            fn ($coordinate) => ['lat' => (float) $coordinate[1], 'lng' => (float) $coordinate[0]],

            $coordinatesGeoJson

        );
    }

    /**
     * Downsample titik rute secara merata ke jumlah maksimal tertentu.
     *
     * @param  array<int, array{lat: float, lng: float}>  $points
     * @return array<int, array{lat: float, lng: float}>
     */
    protected function downsample(array $points, int $maxPoints): array
    {
        $total = count($points);

        if ($total <= $maxPoints) {

            return $points;
        }

        $step = $total / $maxPoints;

        $sampled = [];

        for ($i = 0; $i < $maxPoints; $i++) {

            $sampled[] = $points[(int) floor($i * $step)];
        }

        $lastIndex = $total - 1;

        if (end($sampled) !== $points[$lastIndex]) {

            $sampled[] = $points[$lastIndex];
        }

        return $sampled;
    }

    /**
     * Bangun baris siap-insert: timestamp bertahap, kecepatan & heading
     * dihitung dari jarak antar titik asli, alamat direverse-geocode
     * sungguhan per titik (bukan ditulis manual) - supaya data selalu
     * benar dan konsisten dengan pipeline GPS produksi.
     */
    protected function buildRows(
        array $points,
        ReverseGeocodingService $reverseGeocodingService
    ): array {

        $total = count($points);

        // Mulai dari 45 menit lalu, berakhir mendekati sekarang.
        $time = now()->subMinutes(45);

        $rows = [];

        $bar = $this->output->createProgressBar($total);

        $bar->start();

        foreach ($points as $index => $point) {

            $previous = $points[$index - 1] ?? null;

            $distanceMeters = $previous
                ? $this->haversineMeters($previous, $point)
                : 0;

            // Kecepatan realistis kota: 10-40 km/jam, kadang berhenti sebentar.
            $speed = match (true) {

                $index === 0, $index === $total - 1 => 0,

                $index % 11 === 0 => 0,

                default => random_int(15, 40),

            };

            $heading = $previous
                ? $this->bearing($previous, $point)
                : 0;

            // Jeda waktu proporsional jarak (kecepatan minimal 5 km/jam
            // supaya tidak pernah dibagi nol / delta waktu masuk akal).
            $effectiveSpeedKmh = max($speed, 5);

            $seconds = $distanceMeters > 0
                ? (int) round(($distanceMeters / 1000) / $effectiveSpeedKmh * 3600)
                : 3;

            $time = $time->copy()->addSeconds(max($seconds, 2));

            $address = $reverseGeocodingService->search(

                $point['lat'],

                $point['lng']

            );

            $rows[] = [

                'message_id' => random_int(100000000, 999999999),

                'received_at' => $time,

                'address' => $address,

                'payload' => [

                    'lat' => $point['lat'],

                    'lng' => $point['lng'],

                    'speed' => $speed,

                    'heading' => $heading,

                    'battery' => random_int(70, 95),

                    'satellite' => random_int(8, 14),

                ],

            ];

            $bar->advance();
        }

        $bar->finish();

        $this->newLine();

        return $rows;
    }

    /**
     * Jarak antar 2 titik (meter) - formula haversine.
     */
    protected function haversineMeters(array $from, array $to): float
    {
        $earthRadius = 6371000;

        $latDelta = deg2rad($to['lat'] - $from['lat']);

        $lngDelta = deg2rad($to['lng'] - $from['lng']);

        $a = sin($latDelta / 2) ** 2

            + cos(deg2rad($from['lat'])) * cos(deg2rad($to['lat']))

            * sin($lngDelta / 2) ** 2;

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    /**
     * Arah hadap (derajat, 0-360) dari satu titik ke titik berikutnya.
     */
    protected function bearing(array $from, array $to): float
    {
        $lat1 = deg2rad($from['lat']);

        $lat2 = deg2rad($to['lat']);

        $lngDelta = deg2rad($to['lng'] - $from['lng']);

        $x = sin($lngDelta) * cos($lat2);

        $y = cos($lat1) * sin($lat2) - sin($lat1) * cos($lat2) * cos($lngDelta);

        return fmod(rad2deg(atan2($x, $y)) + 360, 360);
    }
}
