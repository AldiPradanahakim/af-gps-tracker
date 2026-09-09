<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Smoke test: memastikan setiap halaman utama benar-benar ter-render
 * (bukan 500) setelah perubahan branding, komponen syarat kata sandi,
 * dan alur onboarding di halaman Utama.
 */
class SmokeRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_auth_pages_render(): void
    {
        $this->get('/login')->assertOk()->assertSee('AF GPS TRACKER');

        $this->get('/forgot-password')->assertOk()->assertSee('AF GPS TRACKER');

        $this->get('/devices/activate')->assertOk()->assertSee('AF GPS TRACKER');
    }

    public function test_home_renders_and_prompts_device_activation_when_user_has_no_device(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get('/home')
            ->assertOk()
            ->assertSee('AF GPS TRACKER')
            ->assertSee('Aktivasi Perangkat')
            ->assertSee('"device"', escape: false);
    }

    public function test_dashboard_sends_existing_user_to_home_not_registration(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect(route('home'));
    }

    public function test_logged_in_user_cannot_reach_guest_activation_page(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get('/devices/activate')
            ->assertRedirect(route('home'));
    }

    public function test_home_prompts_vehicle_information_when_device_has_no_vehicle(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        Device::create([
            'user_id' => $user->id,
            'device_id' => 'GPS-TEST-0001',
            'device_password' => 'secret123',
            'is_active' => true,
            'activated_at' => now(),
        ]);

        $this->actingAs($user)
            ->get('/home')
            ->assertOk()
            ->assertSee('"vehicle"', escape: false)
            ->assertSee('Lengkapi Informasi Kendaraan');
    }

    public function test_vehicle_detail_page_renders_with_brand_and_refresh_buttons(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $device = Device::create([
            'user_id' => $user->id,
            'device_id' => 'GPS-TEST-0002',
            'device_password' => 'secret123',
            'is_active' => true,
            'activated_at' => now(),
        ]);

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
            ->assertSee('AF GPS TRACKER')

            // Tombol refresh di tiga section (item 3).
            ->assertSee('refreshVehicleActivity')
            ->assertSee('historyRefreshButton')
            ->assertSee('refreshStopHistory');
    }

    public function test_password_requirements_are_shown_on_every_password_form(): void
    {
        $this->get('/reset-password/dummy-token')
            ->assertOk()
            ->assertSee('Kata sandi harus memuat:')
            ->assertSee('Karakter khusus');

        // Modal profil admin (satu-satunya tempat admin mengubah akunnya)
        // ikut memuat syarat kata sandi yang sama.
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Kata sandi harus memuat:');
    }

    public function test_admin_profile_is_read_only_until_edit_is_pressed(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertOk()

            // Mode baca: input terkunci sampai "Edit Profil" ditekan.
            ->assertSee(':disabled="!editingProfile"', escape: false)
            ->assertSee('Edit Profil')

            // Ganti kata sandi terpisah & opsional.
            ->assertSee('Ubah Kata Sandi')
            ->assertSee('lewati jika hanya mengubah nama atau email', escape: false);

        // Halaman profil admin berdiri sendiri sudah tidak ada lagi.
        $this->actingAs($admin)
            ->get('/admin/profile')
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_can_update_name_only_without_touching_password(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'name' => 'Administrator',
        ]);

        $this->actingAs($admin)
            ->patch('/admin/profile', [
                'name' => 'Admin Baru',
                'email' => $admin->email,
            ])
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHasNoErrors();

        $this->assertSame('Admin Baru', $admin->fresh()->name);
    }

    public function test_admin_profile_no_longer_appears_in_sidebar_navigation(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        // Menu sidebar hanya tiga: Statistik, Perangkat, Pengguna.
        $response->assertOk()
            ->assertSee('Manajemen Perangkat')
            ->assertSee('Manajemen Pengguna')

            // Tombol profil di sidebar (yang juga menutup menu mobile)
            // sudah dihapus - profil hanya lewat dropdown kanan atas.
            ->assertDontSee(
                'openProfileModal = true; mobileMenuOpen = false',
                escape: false
            );
    }

    public function test_admin_dashboard_shows_editable_profile(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('AF GPS TRACKER')

            // Profil admin hanya lewat dropdown kanan atas - tidak ada
            // kartu profil di badan dashboard, dan tidak ada menu di
            // sidebar.
            ->assertSee('Profil Admin')
            ->assertSee('Edit Profil');
    }
}
