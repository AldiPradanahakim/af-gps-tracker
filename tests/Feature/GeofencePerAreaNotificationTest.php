<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\Geofence;
use App\Models\GeofenceHistory;
use App\Models\Notification;
use App\Models\User;
use App\Services\Geofence\GeofenceCheckerService;
use App\Services\Geofence\GeofenceEventService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Setiap geofence aktif punya status masuk/keluar sendiri, sehingga
 * keluar dari dua area menghasilkan dua notifikasi terpisah - dan area
 * yang belum dilewati tidak ikut berbunyi.
 */
class GeofencePerAreaNotificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Titik acuan: pusat area.
     */
    private const CENTER_LAT = -6.90;

    private const CENTER_LNG = 107.60;

    private function makeDevice(array $attributes = []): Device
    {
        $user = User::factory()->create(['is_admin' => false]);

        return Device::create(array_merge([
            'user_id' => $user->id,
            'device_id' => 'GPS-EVT-0001',
            'device_password' => 'secret123',
            'is_active' => true,
            'activated_at' => now(),
        ], $attributes));
    }

    private function makeRadius(Device $device, float $radiusMeter, string $name): Geofence
    {
        return Geofence::create([
            'device_id' => $device->id,
            'name' => $name,
            'type' => 'radius',
            'status' => true,
            'config' => [
                'center' => ['lat' => self::CENTER_LAT, 'lng' => self::CENTER_LNG],
                'radius' => $radiusMeter,
                'unit' => 'meter',
                'source' => 'manual',
            ],
        ]);
    }

    /**
     * Polygon persegi mengelilingi pusat, setengah-sisi = $halfDegrees.
     */
    private function makePolygon(Device $device, float $halfDegrees, string $name): Geofence
    {
        $minLat = self::CENTER_LAT - $halfDegrees;
        $maxLat = self::CENTER_LAT + $halfDegrees;
        $minLng = self::CENTER_LNG - $halfDegrees;
        $maxLng = self::CENTER_LNG + $halfDegrees;

        return Geofence::create([
            'device_id' => $device->id,
            'name' => $name,
            'type' => 'custom',
            'status' => true,
            'config' => [
                'geometry' => [
                    'type' => 'Polygon',
                    'coordinates' => [[
                        [$minLng, $minLat],
                        [$maxLng, $minLat],
                        [$maxLng, $maxLat],
                        [$minLng, $maxLat],
                        [$minLng, $minLat],
                    ]],
                ],
            ],
        ]);
    }

    /**
     * Jalankan satu siklus: cek geofence lalu proses kejadiannya.
     */
    private function pushLocation(Device $device, float $lat, float $lng): array
    {
        $device->unsetRelation('geofences');

        $payload = ['lat' => $lat, 'lng' => $lng];

        $result = app(GeofenceCheckerService::class)->process(
            $device->fresh(),
            $payload
        );

        app(GeofenceEventService::class)->handle(
            $device->fresh(),
            $result,
            $payload,
            'Jalan Uji No. 1'
        );

        return $result;
    }

    public function test_first_evaluation_only_sets_baseline_without_notification(): void
    {
        $device = $this->makeDevice();

        $geofence = $this->makeRadius($device, 500, 'Rumah');

        $this->pushLocation($device, self::CENTER_LAT, self::CENTER_LNG);

        $this->assertTrue($geofence->fresh()->is_inside);

        $this->assertSame(0, Notification::count());

        $this->assertSame(0, GeofenceHistory::count());
    }

    public function test_leaving_one_area_while_still_inside_another_sends_one_notification(): void
    {
        $device = $this->makeDevice();

        // Radius kecil (200 m) di dalam polygon besar (~2,2 km setengah-sisi).
        $small = $this->makeRadius($device, 200, 'Radius Rumah');

        $big = $this->makePolygon($device, 0.02, 'Polygon Komplek');

        // Baseline: di pusat, di dalam keduanya.
        $this->pushLocation($device, self::CENTER_LAT, self::CENTER_LNG);

        $this->assertSame(0, Notification::count());

        // Bergeser ~0,005 derajat (~550 m): keluar radius, masih di polygon.
        $this->pushLocation($device, self::CENTER_LAT + 0.005, self::CENTER_LNG);

        $this->assertFalse($small->fresh()->is_inside);
        $this->assertTrue($big->fresh()->is_inside);

        $notifications = Notification::all();

        $this->assertCount(1, $notifications);

        $this->assertSame('geofence_exit', $notifications->first()->type);

        $this->assertSame(
            $small->id,
            $notifications->first()->geofence_id
        );

        $this->assertStringContainsString(
            'Radius Rumah',
            $notifications->first()->data['message']
        );

        // Riwayat: satu baris keluar, untuk geofence yang benar.
        $this->assertCount(1, GeofenceHistory::all());

        $this->assertSame('exit', GeofenceHistory::first()->event);

        $this->assertSame('Radius Rumah', GeofenceHistory::first()->geofence_name);
    }

    public function test_leaving_two_areas_sends_two_separate_notifications(): void
    {
        $device = $this->makeDevice();

        $small = $this->makeRadius($device, 200, 'Radius Rumah');

        $big = $this->makePolygon($device, 0.02, 'Polygon Komplek');

        $this->pushLocation($device, self::CENTER_LAT, self::CENTER_LNG);

        // Bergeser jauh (~0,05 derajat, ~5,5 km): keluar dari keduanya.
        $this->pushLocation($device, self::CENTER_LAT + 0.05, self::CENTER_LNG);

        $this->assertFalse($small->fresh()->is_inside);
        $this->assertFalse($big->fresh()->is_inside);

        $notifications = Notification::where('type', 'geofence_exit')->get();

        $this->assertCount(2, $notifications);

        $this->assertEqualsCanonicalizing(
            [$small->id, $big->id],
            $notifications->pluck('geofence_id')->all()
        );

        $this->assertCount(2, GeofenceHistory::where('event', 'exit')->get());
    }

    public function test_returning_into_one_area_sends_enter_notification_with_duration(): void
    {
        $device = $this->makeDevice();

        $geofence = $this->makeRadius($device, 200, 'Radius Rumah');

        Carbon::setTestNow('2026-09-14 08:00:00');

        $this->pushLocation($device, self::CENTER_LAT, self::CENTER_LNG);

        Carbon::setTestNow('2026-09-14 08:10:00');

        $this->pushLocation($device, self::CENTER_LAT + 0.05, self::CENTER_LNG);

        Carbon::setTestNow('2026-09-14 08:40:00');

        $this->pushLocation($device, self::CENTER_LAT, self::CENTER_LNG);

        Carbon::setTestNow();

        $this->assertTrue($geofence->fresh()->is_inside);

        $this->assertSame(
            1,
            Notification::where('type', 'geofence_enter')->count()
        );

        $enter = GeofenceHistory::where('event', 'enter')->first();

        $this->assertNotNull($enter);

        // 08:10 keluar -> 08:40 masuk = 30 menit di luar area.
        $this->assertSame(1800, $enter->duration_seconds);
    }

    public function test_no_repeat_notification_while_reminder_is_disabled(): void
    {
        $device = $this->makeDevice();

        $this->makeRadius($device, 200, 'Radius Rumah');

        $this->pushLocation($device, self::CENTER_LAT, self::CENTER_LNG);

        $this->pushLocation($device, self::CENTER_LAT + 0.05, self::CENTER_LNG);

        $this->assertSame(1, Notification::count());

        // Payload berikutnya, masih di luar - tanpa pengingat tidak menambah apa pun.
        $this->pushLocation($device, self::CENTER_LAT + 0.06, self::CENTER_LNG);

        $this->assertSame(1, Notification::count());
    }

    public function test_repeat_reminder_fires_only_after_the_configured_interval(): void
    {
        $device = $this->makeDevice([
            'geofence_setting' => [
                'repeat_enabled' => true,
                'repeat_minutes' => 15,
            ],
        ]);

        $geofence = $this->makeRadius($device, 200, 'Radius Rumah');

        Carbon::setTestNow('2026-09-14 08:00:00');

        $this->pushLocation($device, self::CENTER_LAT, self::CENTER_LNG);

        Carbon::setTestNow('2026-09-14 08:05:00');

        $this->pushLocation($device, self::CENTER_LAT + 0.05, self::CENTER_LNG);

        $this->assertSame(1, Notification::count());

        // Belum 15 menit sejak notifikasi keluar -> belum ada pengingat.
        Carbon::setTestNow('2026-09-14 08:15:00');

        $this->pushLocation($device, self::CENTER_LAT + 0.05, self::CENTER_LNG);

        $this->assertSame(1, Notification::count());

        // Sudah lewat 15 menit -> satu pengingat.
        Carbon::setTestNow('2026-09-14 08:21:00');

        $this->pushLocation($device, self::CENTER_LAT + 0.05, self::CENTER_LNG);

        $this->assertSame(2, Notification::count());

        $reminder = Notification::latest('created_at')->first();

        $this->assertTrue($reminder->data['repeat']);

        $this->assertSame('Masih di Luar Geofence', $reminder->data['title']);

        // Pengingat tidak menambah baris riwayat - riwayat hanya perpindahan.
        $this->assertSame(1, GeofenceHistory::count());

        // Masuk kembali mengakhiri periode di luar dan mereset penanda.
        Carbon::setTestNow('2026-09-14 08:30:00');

        $this->pushLocation($device, self::CENTER_LAT, self::CENTER_LNG);

        Carbon::setTestNow();

        $this->assertNull($geofence->fresh()->last_exit_notified_at);
    }

    /**
     * Antara pengecekan geofence dan pengiriman notifikasi masih ada
     * broadcast realtime dan beberapa penulisan database. Kalau salah
     * satunya gagal, payload berhenti di tengah - dan status geofence
     * tidak boleh terlanjur tersimpan, karena kejadiannya akan hilang.
     */
    public function test_interrupted_payload_does_not_lose_the_exit_event(): void
    {
        $device = $this->makeDevice();

        $geofence = $this->makeRadius($device, 200, 'Radius Rumah');

        $this->pushLocation($device, self::CENTER_LAT, self::CENTER_LNG);

        // Payload yang terputus: pengecekan jalan, efeknya tidak pernah dijalankan.
        $device->unsetRelation('geofences');

        app(GeofenceCheckerService::class)->process(
            $device->fresh(),
            ['lat' => self::CENTER_LAT + 0.05, 'lng' => self::CENTER_LNG]
        );

        $this->assertTrue(
            $geofence->fresh()->is_inside,
            'Status tidak boleh berpindah sebelum notifikasi tercatat.'
        );

        $this->assertSame(0, Notification::count());

        // Payload berikutnya harus tetap mengenali kejadian keluar tadi.
        $this->pushLocation($device, self::CENTER_LAT + 0.05, self::CENTER_LNG);

        $this->assertFalse($geofence->fresh()->is_inside);

        $this->assertSame(
            1,
            Notification::where('type', 'geofence_exit')->count()
        );

        $this->assertSame(1, GeofenceHistory::where('event', 'exit')->count());
    }

    /**
     * Mengedit geofence saat kendaraan sedang di luar area tidak boleh
     * mematikan pengingat "masih di luar" secara diam-diam.
     */
    public function test_editing_a_geofence_keeps_the_still_outside_reminder_alive(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $device = Device::create([
            'user_id' => $user->id,
            'device_id' => 'GPS-EVT-0003',
            'device_password' => 'secret123',
            'is_active' => true,
            'activated_at' => now(),
            'geofence_setting' => [
                'repeat_enabled' => true,
                'repeat_minutes' => 15,
            ],
        ]);

        $geofence = $this->makeRadius($device, 200, 'Radius Rumah');

        Carbon::setTestNow('2026-09-14 08:00:00');

        $this->pushLocation($device, self::CENTER_LAT, self::CENTER_LNG);

        Carbon::setTestNow('2026-09-14 08:05:00');

        $this->pushLocation($device, self::CENTER_LAT + 0.05, self::CENTER_LNG);

        $this->assertSame(1, Notification::count());

        // Pemilik mengubah radius saat kendaraan masih di luar area.
        Carbon::setTestNow('2026-09-14 08:10:00');

        $this->actingAs($user)
            ->patchJson(route('geofences.update', $geofence), [
                'name' => 'Radius Rumah',
                'status' => 1,
                'radius' => 250,
                'radius_source' => 'keep_current',
            ])
            ->assertOk();

        $this->assertNull($geofence->fresh()->is_inside);

        $this->assertNotNull(
            $geofence->fresh()->last_exit_notified_at,
            'Penanda pengingat tidak boleh ikut terhapus saat geofence diedit.'
        );

        // Baseline ulang, masih di luar.
        Carbon::setTestNow('2026-09-14 08:12:00');

        $this->pushLocation($device, self::CENTER_LAT + 0.05, self::CENTER_LNG);

        // Jeda terlampaui - pengingat harus tetap berjalan.
        Carbon::setTestNow('2026-09-14 08:25:00');

        $this->pushLocation($device, self::CENTER_LAT + 0.05, self::CENTER_LNG);

        Carbon::setTestNow();

        $this->assertSame(2, Notification::count());

        $this->assertTrue(Notification::latest('created_at')->first()->data['repeat']);
    }

    /**
     * Geofence dengan konfigurasi rusak tidak boleh mengunci baseline
     * "di luar area" - begitu diperbaiki, kendaraan yang sejak awal diam
     * di dalam area akan dikira baru masuk.
     */
    public function test_unreadable_geofence_config_does_not_lock_in_a_wrong_baseline(): void
    {
        $device = $this->makeDevice();

        $geofence = Geofence::create([
            'device_id' => $device->id,
            'name' => 'Poligon Rusak',
            'type' => 'custom',
            'status' => true,
            'config' => [
                'geometry' => ['type' => 'Polygon'],
            ],
        ]);

        $this->pushLocation($device, self::CENTER_LAT, self::CENTER_LNG);

        $this->assertNull(
            $geofence->fresh()->is_inside,
            'Baseline dari pengecekan yang gagal tidak boleh disimpan.'
        );

        $this->assertSame(0, Notification::count());

        // Konfigurasi diperbaiki - baseline pertama yang sah menyimpulkan
        // kendaraan ada DI DALAM area, jadi tidak ada notifikasi "masuk".
        $geofence->forceFill([
            'config' => [
                'geometry' => [
                    'type' => 'Polygon',
                    'coordinates' => [[
                        [self::CENTER_LNG - 0.02, self::CENTER_LAT - 0.02],
                        [self::CENTER_LNG + 0.02, self::CENTER_LAT - 0.02],
                        [self::CENTER_LNG + 0.02, self::CENTER_LAT + 0.02],
                        [self::CENTER_LNG - 0.02, self::CENTER_LAT + 0.02],
                        [self::CENTER_LNG - 0.02, self::CENTER_LAT - 0.02],
                    ]],
                ],
            ],
        ])->save();

        $this->pushLocation($device, self::CENTER_LAT, self::CENTER_LNG);

        $this->assertTrue($geofence->fresh()->is_inside);

        $this->assertSame(0, Notification::count());
    }

    public function test_changing_the_area_resets_state_so_no_false_enter_notification(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $device = Device::create([
            'user_id' => $user->id,
            'device_id' => 'GPS-EVT-0002',
            'device_password' => 'secret123',
            'is_active' => true,
            'activated_at' => now(),
        ]);

        $geofence = $this->makeRadius($device, 200, 'Radius Rumah');

        // Baseline di dalam, lalu keluar.
        $this->pushLocation($device, self::CENTER_LAT, self::CENTER_LNG);

        $this->pushLocation($device, self::CENTER_LAT + 0.005, self::CENTER_LNG);

        $this->assertSame(1, Notification::count());

        // Radius diperbesar sehingga titik yang sama kini di dalam area.
        $this->actingAs($user)
            ->patchJson(route('geofences.update', $geofence), [
                'name' => 'Radius Rumah',
                'status' => 1,
                'radius' => 5000,
                'radius_source' => 'keep_current',
            ])
            ->assertOk();

        $this->assertNull($geofence->fresh()->is_inside);

        // Payload berikutnya hanya menetapkan baseline - bukan "masuk area".
        $this->pushLocation($device, self::CENTER_LAT + 0.005, self::CENTER_LNG);

        $this->assertTrue($geofence->fresh()->is_inside);

        $this->assertSame(
            0,
            Notification::where('type', 'geofence_enter')->count()
        );
    }
}
