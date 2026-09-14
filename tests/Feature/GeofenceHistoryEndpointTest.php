<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\Geofence;
use App\Models\GeofenceHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Endpoint riwayat geofence dan pengaturan pengingat "masih di luar area".
 */
class GeofenceHistoryEndpointTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{0: User, 1: Device, 2: Geofence}
     */
    private function makeFixture(): array
    {
        $user = User::factory()->create(['is_admin' => false]);

        $device = Device::create([
            'user_id' => $user->id,
            'device_id' => 'GPS-HIST-0001',
            'device_password' => 'secret123',
            'is_active' => true,
            'activated_at' => now(),
        ]);

        $geofence = Geofence::create([
            'device_id' => $device->id,
            'name' => 'Radius Rumah',
            'type' => 'radius',
            'status' => true,
            'config' => [
                'center' => ['lat' => -6.9, 'lng' => 107.6],
                'radius' => 300.0,
                'unit' => 'meter',
                'source' => 'manual',
            ],
        ]);

        return [$user, $device, $geofence];
    }

    private function makeHistory(
        Device $device,
        Geofence $geofence,
        string $event,
        string $occurredAt,
        ?int $durationSeconds = null
    ): GeofenceHistory {

        return GeofenceHistory::create([
            'device_id' => $device->id,
            'geofence_id' => $geofence->id,
            'geofence_name' => $geofence->name,
            'geofence_type' => $geofence->type,
            'event' => $event,
            'location' => ['lat' => -6.9, 'lng' => 107.6],
            'search_address' => 'Jalan Uji No. 1',
            'occurred_at' => Carbon::parse($occurredAt),
            'duration_seconds' => $durationSeconds,
        ]);
    }

    public function test_history_endpoint_returns_items_newest_first_with_summary(): void
    {
        [$user, $device, $geofence] = $this->makeFixture();

        $this->makeHistory($device, $geofence, 'exit', '2026-09-13 08:00:00', 3600);
        $this->makeHistory($device, $geofence, 'enter', '2026-09-13 09:30:00', 5400);
        $this->makeHistory($device, $geofence, 'exit', now()->toDateTimeString(), 600);

        $response = $this->actingAs($user)
            ->getJson(route('vehicles.geofence-history', $device))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(3, 'data');

        $data = $response->json('data');

        $this->assertSame('exit', $data[0]['event']);
        $this->assertSame('Radius', $data[0]['geofence_type_label']);
        $this->assertSame('Keluar', $data[0]['event_label']);

        // Terbaru lebih dulu.
        $this->assertTrue($data[0]['occurred_at'] >= $data[1]['occurred_at']);

        $summary = $response->json('summary');

        $this->assertSame(3, $summary['total']);
        $this->assertSame(2, $summary['total_exit']);
        $this->assertSame(1, $summary['total_enter']);
        $this->assertSame(1, $summary['today']);

        // Harus berupa string tanggal, bukan objek kosong hasil optional().
        $this->assertIsString($summary['last_occurred_at']);
    }

    public function test_history_summary_reports_null_last_event_when_empty(): void
    {
        [$user, $device] = $this->makeFixture();

        $this->actingAs($user)
            ->getJson(route('vehicles.geofence-history', $device))
            ->assertOk()
            ->assertJsonPath('summary.total', 0)
            ->assertJsonPath('summary.last_occurred_at', null);
    }

    public function test_history_endpoint_filters_by_event_and_date(): void
    {
        [$user, $device, $geofence] = $this->makeFixture();

        $this->makeHistory($device, $geofence, 'exit', '2026-09-10 08:00:00');
        $this->makeHistory($device, $geofence, 'enter', '2026-09-12 08:00:00');
        $this->makeHistory($device, $geofence, 'exit', '2026-09-12 10:00:00');

        $this->actingAs($user)
            ->getJson(route('vehicles.geofence-history', $device) . '?event=exit')
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $this->actingAs($user)
            ->getJson(
                route('vehicles.geofence-history', $device)
                . '?start_date=2026-09-12&end_date=2026-09-12'
            )
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_history_endpoint_rejects_another_users_device(): void
    {
        [, $device] = $this->makeFixture();

        $stranger = User::factory()->create(['is_admin' => false]);

        $this->actingAs($stranger)
            ->getJson(route('vehicles.geofence-history', $device))
            ->assertForbidden();
    }

    public function test_repeat_setting_can_be_saved_and_is_bounded(): void
    {
        [$user, $device] = $this->makeFixture();

        $this->actingAs($user)
            ->patchJson(route('vehicles.geofence-setting.update', $device), [
                'repeat_enabled' => true,
                'repeat_minutes' => 30,
            ])
            ->assertOk()
            ->assertJsonPath('data.repeat_enabled', true)
            ->assertJsonPath('data.repeat_minutes', 30);

        $this->assertSame(
            ['repeat_enabled' => true, 'repeat_minutes' => 30],
            $device->fresh()->geofence_setting
        );

        // Jeda terlalu rapat ditolak - menjaga kuota email/WhatsApp.
        $this->actingAs($user)
            ->patchJson(route('vehicles.geofence-setting.update', $device), [
                'repeat_enabled' => true,
                'repeat_minutes' => 1,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('repeat_minutes');
    }

    public function test_geofence_section_renders_reminder_toggle_and_history_panel(): void
    {
        [$user, $device] = $this->makeFixture();

        $device->vehicle()->create([
            'vehicle_name' => 'Beat Uji',
            'vehicle_type' => 'motorcycle',
            'plate_number' => 'D 1234 XX',
            'marker_icon' => 'motorcycle',
            'marker_color' => 'blue',
        ]);

        $this->actingAs($user)
            ->get(route('vehicles.show', $device))
            ->assertOk()
            ->assertSee('geofenceRepeatEnabled')
            ->assertSee('geofenceRepeatMinutes')
            ->assertSee('geofenceHistoryList')
            ->assertSee('Pengingat Keluar Geofence')
            ->assertSee('Riwayat Masuk &amp; Keluar Geofence', escape: false);
    }

    public function test_repeat_setting_rejects_another_users_device(): void
    {
        [, $device] = $this->makeFixture();

        $stranger = User::factory()->create(['is_admin' => false]);

        $this->actingAs($stranger)
            ->patchJson(route('vehicles.geofence-setting.update', $device), [
                'repeat_enabled' => true,
                'repeat_minutes' => 30,
            ])
            ->assertForbidden();
    }
}
