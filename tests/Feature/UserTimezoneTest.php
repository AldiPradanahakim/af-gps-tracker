<?php

namespace Tests\Feature;

use App\Helpers\AppTime;
use App\Models\Device;
use App\Models\Notification;
use App\Models\TravelHistory;
use App\Models\User;
use App\Services\Notification\NotificationPresenter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Zona waktu tampilan mengikuti pilihan MASING-MASING pengguna
 * (WIB/WITA/WIT), bukan zona waktu server - berlaku untuk halaman,
 * export PDF, dan notifikasi Email/WhatsApp.
 */
class UserTimezoneTest extends TestCase
{
    use RefreshDatabase;

    private function userWithTimezone(?string $timezone): User
    {
        return User::factory()->create([
            'is_admin' => false,
            'timezone' => $timezone,
        ]);
    }

    public function test_user_without_choice_falls_back_to_app_default(): void
    {
        $user = $this->userWithTimezone(null);

        $this->assertSame('Asia/Jakarta', $user->displayTimezone());
        $this->assertSame('WIB', AppTime::timezoneLabel($user));
    }

    public function test_invalid_stored_timezone_falls_back_instead_of_crashing(): void
    {
        $user = $this->userWithTimezone('Mars/Olympus');

        $this->assertSame('Asia/Jakarta', $user->displayTimezone());
    }

    public function test_same_instant_renders_in_each_user_timezone(): void
    {
        $moment = Carbon::parse('2026-09-09 14:52:00', config('app.timezone'));

        $expected = [
            'Asia/Jakarta' => '09/09/2026 14:52 WIB',
            'Asia/Makassar' => '09/09/2026 15:52 WITA',
            'Asia/Jayapura' => '09/09/2026 16:52 WIT',
        ];

        foreach ($expected as $timezone => $label) {

            $user = $this->userWithTimezone($timezone);

            $this->assertSame(
                $label,
                AppTime::format($moment, 'd/m/Y H:i', user: $user),
                "Zona {$timezone} salah"
            );
        }
    }

    public function test_notification_uses_recipient_timezone_not_server(): void
    {
        $user = $this->userWithTimezone('Asia/Jayapura');

        $device = Device::create([
            'user_id' => $user->id,
            'device_id' => 'GPS-TZ-0001',
            'device_password' => 'secret123',
            'is_active' => true,
            'activated_at' => now(),
        ]);

        $notification = Notification::create([
            'device_id' => $device->id,
            'type' => 'low_battery',
            'data' => ['title' => 'Baterai Perangkat Lemah'],
            'status' => 'sent',
        ]);

        $notification->created_at = Carbon::parse('2026-09-09 14:52:00', config('app.timezone'));
        $notification->saveQuietly();

        $notification->load('device.user');

        $this->assertSame(
            '09 September 2026, 16:52 WIT',
            NotificationPresenter::formatDateTime(
                $notification->created_at,
                NotificationPresenter::recipient($notification)
            )
        );
    }

    public function test_today_boundary_follows_the_logged_in_user_timezone(): void
    {
        /*
        | 09/09/2026 00:30 WIT = 08/09/2026 22:30 WIB. Titik ini "hari
        | ini" bagi pengguna di Papua, tapi "kemarin" bagi pengguna WIB -
        | batas harinya harus ikut zona pengguna, bukan dipaku ke WIB.
        */
        $wit = $this->userWithTimezone('Asia/Jayapura');
        $wib = $this->userWithTimezone('Asia/Jakarta');

        Carbon::setTestNow(
            Carbon::parse('2026-09-09 01:00:00', 'Asia/Jayapura')
        );

        $this->actingAs($wit);
        $startWit = AppTime::startOfDay();

        $this->actingAs($wib);
        $startWib = AppTime::startOfDay();

        // Pada saat itu di Papua sudah 9 September, sementara di Jawa
        // masih 8 September - jadi "hari ini" keduanya memang berbeda.
        $this->assertSame(
            '2026-09-09',
            $startWit->copy()->timezone('Asia/Jayapura')->toDateString()
        );

        $this->assertSame(
            '2026-09-08',
            $startWib->copy()->timezone('Asia/Jakarta')->toDateString()
        );

        $this->assertTrue(
            $startWit->greaterThan($startWib),
            'Awal hari WIT harus jatuh setelah awal hari WIB pada momen ini'
        );

        Carbon::setTestNow();
    }

    /**
     * Regresi: Blade memanggil AppTime dengan \Carbon\Carbon polos hasil
     * ::parse(), sedangkan kolom timestamp Eloquent memberi
     * Illuminate\Support\Carbon. Keduanya CarbonInterface - kalau
     * AppTime hanya menerima subclass Illuminate, halaman Utama langsung
     * 500 (TypeError) begitu ada kendaraan yang punya lokasi terakhir.
     */
    public function test_app_time_accepts_both_carbon_implementations(): void
    {
        $moment = '2026-09-08 08:34:11';

        $this->assertSame(
            AppTime::format(\Illuminate\Support\Carbon::parse($moment), 'd/m/Y H:i:s'),
            AppTime::format(\Carbon\Carbon::parse($moment), 'd/m/Y H:i:s')
        );

        $this->assertSame(
            '2026-09-08 00:00:00',
            AppTime::startOfDay(\Carbon\Carbon::parse($moment))->toDateTimeString()
        );
    }

    public function test_home_page_renders_for_user_with_vehicle_and_last_location(): void
    {
        $user = $this->userWithTimezone('Asia/Makassar');

        $device = Device::create([
            'user_id' => $user->id,
            'device_id' => 'GPS-TZ-0003',
            'device_password' => 'secret123',
            'is_active' => true,
            'activated_at' => now(),
        ]);

        $device->vehicle()->create([
            'vehicle_name' => 'Beat Sidebar',
            'vehicle_type' => 'motorcycle',
            'plate_number' => 'DD 9 SB',
            'marker_icon' => 'motorcycle',
            'marker_color' => 'blue',
        ]);

        $deviceLog = $device->deviceLogs()->create([
            'message_id' => 90002,
            'payload' => ['lat' => -5.14, 'lng' => 119.42, 'speed' => 12],
            'status' => 'valid',
            'received_at' => Carbon::parse('2026-09-08 08:34:11', config('app.timezone')),
        ]);

        TravelHistory::create([
            'device_log_id' => $deviceLog->id,
            'device_id' => $device->id,
            'location' => ['lat' => -5.14, 'lng' => 119.42, 'speed' => 12],
            'search_address' => 'Jalan Uji, Makassar',
            'received_at' => Carbon::parse('2026-09-08 08:34:11', config('app.timezone')),
        ]);

        $this->actingAs($user)
            ->get('/home')
            ->assertOk()
            ->assertSee('Beat Sidebar')

            // Jam terakhir di kartu sidebar ikut zona pengguna (WITA).
            ->assertSee('09:34:11 WITA');
    }

    public function test_pdf_export_timestamps_follow_the_users_timezone(): void
    {
        $user = $this->userWithTimezone('Asia/Makassar');

        $device = Device::create([
            'user_id' => $user->id,
            'device_id' => 'GPS-TZ-0002',
            'device_password' => 'secret123',
            'is_active' => true,
            'activated_at' => now(),
        ]);

        $device->vehicle()->create([
            'vehicle_name' => 'Uji Zona',
            'vehicle_type' => 'car',
            'plate_number' => 'DD 1 TZ',
            'marker_icon' => 'car',
            'marker_color' => 'blue',
        ]);

        $deviceLog = $device->deviceLogs()->create([
            'message_id' => 90001,
            'payload' => ['lat' => -5.14, 'lng' => 119.42, 'speed' => 30],
            'status' => 'valid',
            'received_at' => Carbon::parse('2026-09-08 08:34:11', config('app.timezone')),
        ]);

        TravelHistory::create([
            'device_log_id' => $deviceLog->id,
            'device_id' => $device->id,
            'location' => ['lat' => -5.14, 'lng' => 119.42, 'speed' => 30],
            'search_address' => 'Jalan Uji, Makassar',
            'received_at' => Carbon::parse('2026-09-08 08:34:11', config('app.timezone')),
        ]);

        $this->actingAs($user);

        $service = app(\App\Services\Vehicle\PdfExportService::class);
        $format = new \ReflectionMethod($service, 'formatTimestamp');
        $format->setAccessible(true);

        $this->assertSame(
            '08/09/2026 09:34 WITA',
            $format->invoke($service, '2026-09-08 08:34:11')
        );

        // Dan dokumennya memang berhasil dirender sampai selesai.
        $this->assertNotEmpty(
            $service->travelHistory($device, '2026-09-08', '2026-09-08')->output()
        );
    }
}
