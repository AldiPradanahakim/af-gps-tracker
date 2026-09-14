<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * overspeed_active menandai "sudah dinotifikasi untuk episode melaju
 * berlebih yang sedang berjalan". Penanda itu membeku saat fitur
 * dimatikan, jadi harus direset ketika fitur dinyalakan lagi atau
 * batas kecepatannya diubah - kalau tidak, notifikasi berikutnya
 * tidak pernah terkirim sampai kecepatan sempat turun dulu.
 */
class SpeedSettingResetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{0: User, 1: Device}
     */
    private function makeFixture(array $speedSetting): array
    {
        $user = User::factory()->create(['is_admin' => false]);

        $device = Device::create([
            'user_id' => $user->id,
            'device_id' => 'GPS-SPD-0001',
            'device_password' => 'secret123',
            'is_active' => true,
            'activated_at' => now(),
            'speed_setting' => $speedSetting,
            'overspeed_active' => true,
        ]);

        return [$user, $device];
    }

    public function test_reenabling_the_feature_resets_the_overspeed_flag(): void
    {
        [$user, $device] = $this->makeFixture([
            'enabled' => false,
            'limit_kmh' => 80,
            'email_notification' => false,
            'whatsapp_notification' => false,
        ]);

        $this->actingAs($user)
            ->patchJson(route('vehicles.speed-setting.update', $device), [
                'enabled' => true,
                'limit_kmh' => 80,
                'email_notification' => false,
                'whatsapp_notification' => false,
            ])
            ->assertOk();

        $this->assertFalse($device->fresh()->overspeed_active);
    }

    public function test_changing_the_limit_resets_the_overspeed_flag(): void
    {
        [$user, $device] = $this->makeFixture([
            'enabled' => true,
            'limit_kmh' => 80,
            'email_notification' => false,
            'whatsapp_notification' => false,
        ]);

        $this->actingAs($user)
            ->patchJson(route('vehicles.speed-setting.update', $device), [
                'enabled' => true,
                'limit_kmh' => 100,
                'email_notification' => false,
                'whatsapp_notification' => false,
            ])
            ->assertOk();

        $this->assertFalse($device->fresh()->overspeed_active);
    }

    public function test_changing_only_the_channels_keeps_the_overspeed_flag(): void
    {
        [$user, $device] = $this->makeFixture([
            'enabled' => true,
            'limit_kmh' => 80,
            'email_notification' => false,
            'whatsapp_notification' => false,
        ]);

        // Batas dan status tidak berubah, jadi episode yang sedang
        // berjalan tetap dianggap sudah dinotifikasi - tidak ada
        // notifikasi ganda untuk kejadian yang sama.
        $this->actingAs($user)
            ->patchJson(route('vehicles.speed-setting.update', $device), [
                'enabled' => true,
                'limit_kmh' => 80,
                'email_notification' => true,
                'whatsapp_notification' => false,
            ])
            ->assertOk();

        $this->assertTrue($device->fresh()->overspeed_active);
    }
}
