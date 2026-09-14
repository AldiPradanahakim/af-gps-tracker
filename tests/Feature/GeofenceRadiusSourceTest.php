<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\Geofence;
use App\Models\User;
use App\Repositories\HomeLocationRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Titik pusat geofence radius selalu di-resolve ulang di server dari
 * sumber yang dipilih (Home Location / GPS terakhir), bukan dari
 * koordinat snapshot yang dikirim browser.
 *
 * Ini yang membuat "edit radius -> pilih Home Location" tetap bekerja
 * walaupun Home Location baru saja diubah tanpa reload halaman.
 */
class GeofenceRadiusSourceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{0: User, 1: Device}
     */
    private function makeUserWithDevice(?array $homeLocation = null): array
    {
        $user = User::factory()->create(['is_admin' => false]);

        $device = Device::create([
            'user_id' => $user->id,
            'device_id' => 'GPS-GEO-0001',
            'device_password' => 'secret123',
            'is_active' => true,
            'activated_at' => now(),
            'home_location' => $homeLocation,
        ]);

        return [$user, $device];
    }

    private function makeRadiusGeofence(Device $device, array $config): Geofence
    {
        return Geofence::create([
            'device_id' => $device->id,
            'name' => 'Area',
            'type' => 'radius',
            'status' => true,
            'config' => $config,
        ]);
    }

    public function test_edit_radius_with_home_location_source_uses_latest_home_location(): void
    {
        [$user, $device] = $this->makeUserWithDevice([
            'lat' => -6.9,
            'lng' => 107.6,
            'display_name' => 'Rumah Lama',
        ]);

        $geofence = $this->makeRadiusGeofence($device, [
            'center' => ['lat' => -6.9, 'lng' => 107.6],
            'radius' => 500.0,
            'unit' => 'meter',
            'source' => 'home_location',
        ]);

        // Home Location dipindah lewat halaman Home Location.
        $device->update([
            'home_location' => [
                'lat' => -6.95,
                'lng' => 107.65,
                'display_name' => 'Rumah Baru',
            ],
        ]);

        $this->actingAs($user)
            ->patchJson(route('geofences.update', $geofence), [
                'name' => 'Area',
                'status' => 1,
                'radius' => 800,
                'radius_source' => 'home_location',
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $config = $geofence->fresh()->config;

        $this->assertSame(-6.95, $config['center']['lat']);
        $this->assertSame(107.65, $config['center']['lng']);
        $this->assertEquals(800.0, $config['radius']);
        $this->assertSame('home_location', $config['source']);
    }

    public function test_edit_radius_keeping_current_point_does_not_move_center(): void
    {
        [$user, $device] = $this->makeUserWithDevice([
            'lat' => -6.95,
            'lng' => 107.65,
            'display_name' => 'Rumah',
        ]);

        $geofence = $this->makeRadiusGeofence($device, [
            'center' => ['lat' => -6.2, 'lng' => 106.8],
            'radius' => 500.0,
            'unit' => 'meter',
            'source' => 'manual',
        ]);

        $this->actingAs($user)
            ->patchJson(route('geofences.update', $geofence), [
                'name' => 'Area',
                'status' => 1,
                'radius' => 1200,
                'radius_source' => 'keep_current',
            ])
            ->assertOk();

        $config = $geofence->fresh()->config;

        $this->assertSame(-6.2, $config['center']['lat']);
        $this->assertSame(106.8, $config['center']['lng']);
        $this->assertEquals(1200.0, $config['radius']);
        $this->assertSame('manual', $config['source']);
    }

    public function test_switching_source_to_home_location_also_records_the_new_source(): void
    {
        [$user, $device] = $this->makeUserWithDevice([
            'lat' => -6.95,
            'lng' => 107.65,
            'display_name' => 'Rumah',
        ]);

        $geofence = $this->makeRadiusGeofence($device, [
            'center' => ['lat' => -6.2, 'lng' => 106.8],
            'radius' => 500.0,
            'unit' => 'meter',
            'source' => 'manual',
        ]);

        $this->actingAs($user)
            ->patchJson(route('geofences.update', $geofence), [
                'name' => 'Area',
                'status' => 1,
                'radius' => 500,
                'radius_source' => 'home_location',
            ])
            ->assertOk();

        $this->assertSame('home_location', $geofence->fresh()->config['source']);

        $this->assertEquals(-6.95, $geofence->fresh()->config['center']['lat']);
    }

    public function test_moving_home_location_does_not_move_an_existing_radius(): void
    {
        [, $device] = $this->makeUserWithDevice([
            'lat' => -6.95,
            'lng' => 107.65,
            'display_name' => 'Rumah',
        ]);

        $geofence = $this->makeRadiusGeofence($device, [
            'center' => ['lat' => -6.95, 'lng' => 107.65],
            'radius' => 500.0,
            'unit' => 'meter',
            'source' => 'home_location',
        ]);

        /*
        | Geofence adalah area pengawasan yang sudah disetujui pemilik.
        | Memindah Lokasi Rumah TIDAK boleh menggesernya diam-diam -
        | kendaraan bisa mendadak dianggap keluar area tanpa bergerak.
        */
        app(HomeLocationRepository::class)->save($device->id, [
            'latitude' => -7.0,
            'longitude' => 107.7,
            'display_name' => 'Rumah Pindah',
        ]);

        $config = $geofence->fresh()->config;

        $this->assertEquals(-6.95, $config['center']['lat']);
        $this->assertEquals(107.65, $config['center']['lng']);
    }

    public function test_editing_and_reselecting_home_location_moves_the_radius(): void
    {
        [$user, $device] = $this->makeUserWithDevice([
            'lat' => -6.95,
            'lng' => 107.65,
            'display_name' => 'Rumah',
        ]);

        $geofence = $this->makeRadiusGeofence($device, [
            'center' => ['lat' => -6.95, 'lng' => 107.65],
            'radius' => 500.0,
            'unit' => 'meter',
            'source' => 'home_location',
        ]);

        app(HomeLocationRepository::class)->save($device->id, [
            'latitude' => -7.0,
            'longitude' => 107.7,
            'display_name' => 'Rumah Pindah',
        ]);

        // Titik baru baru dipakai setelah pengguna memilih ulang sumbernya.
        $this->actingAs($user)
            ->patchJson(route('geofences.update', $geofence), [
                'name' => 'Area',
                'status' => 1,
                'radius' => 500,
                'radius_source' => 'home_location',
            ])
            ->assertOk();

        $config = $geofence->fresh()->config;

        $this->assertEquals(-7.0, $config['center']['lat']);
        $this->assertEquals(107.7, $config['center']['lng']);
    }

    public function test_edit_radius_without_home_location_returns_clear_error(): void
    {
        [$user, $device] = $this->makeUserWithDevice(null);

        $geofence = $this->makeRadiusGeofence($device, [
            'center' => ['lat' => -6.2, 'lng' => 106.8],
            'radius' => 500.0,
            'unit' => 'meter',
            'source' => 'manual',
        ]);

        $this->actingAs($user)
            ->patchJson(route('geofences.update', $geofence), [
                'name' => 'Area',
                'status' => 1,
                'radius' => 500,
                'radius_source' => 'home_location',
            ])
            ->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonStructure(['errors' => ['radius_source']]);
    }
}
