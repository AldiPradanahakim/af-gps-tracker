<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\DeviceLog;
use App\Models\Geofence;
use App\Models\Notification;
use App\Models\StopHistory;
use App\Models\TravelHistory;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoAccountSeeder extends Seeder
{
    /**
     * Device credentials used for the activation step.
     * Reported back to the user so they can log in / re-run the
     * activation flow through the UI if they want to see it live.
     */
    protected string $deviceId = 'GPS-AF-0001';

    protected string $devicePassword = 'device1234';

    protected string $homeDisplayName = 'Jl. MH Thamrin, Jakarta Pusat, DKI Jakarta';

    protected float $homeLat = -6.19524;

    protected float $homeLng = 106.82301;

    public function run(): void
    {
        DB::transaction(function () {

            /*
            |--------------------------------------------------------------------------
            | 1. Device pre-provisioned (mimics a physical GPS unit in stock,
            |    exactly like what ActivateDeviceService expects to find by
            |    device_id + device_password before it can be claimed by a user)
            |--------------------------------------------------------------------------
            */

            $device = Device::updateOrCreate(
                ['device_id' => $this->deviceId],
                [
                    'device_password' => $this->devicePassword,
                    'user_id' => null,
                    'is_active' => false,
                    'is_inside_geofence' => false,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | 2. User account (mimics ProfileService::createUser)
            |--------------------------------------------------------------------------
            */

            $user = User::updateOrCreate(
                ['email' => 'aldipradanahakim329@gmail.com'],
                [
                    'name' => 'Aldi Pradana Hakim',
                    'phone' => '081234567890',
                    'password' => Hash::make('Aldi#1234'),
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | 3. Activate the device for this user (mimics what ProfileService
            |    does right after device activation succeeds)
            |--------------------------------------------------------------------------
            */

            $device->update([
                'user_id' => $user->id,
                'is_active' => true,
                'activated_at' => now(),
                'last_heartbeat' => now(),
                'home_location' => [
                    'lat' => $this->homeLat,
                    'lng' => $this->homeLng,
                    'display_name' => $this->homeDisplayName,
                ],
                'stop_setting' => [
                    'enabled' => true,
                    'minutes' => 5,
                    'email_notification' => true,
                    'whatsapp_notification' => false,
                ],
                'notification_setting' => [
                    'email' => true,
                    'whatsapp' => false,
                ],
            ]);

            /*
            |--------------------------------------------------------------------------
            | 4. Vehicle (mimics ActivateVehicleService::store)
            |--------------------------------------------------------------------------
            */

            $vehicle = Vehicle::updateOrCreate(
                ['device_id' => $device->id],
                [
                    'vehicle_name' => 'Honda Beat Aldi',
                    'vehicle_type' => 'motor',
                    'plate_number' => 'B 1234 ALD',
                    'marker_icon' => 'motorcycle',
                    'marker_color' => 'blue',
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | 5. Geofence radius di sekitar home_location (mimics
            |    GeofenceService::buildRadiusConfig with radius_source=home_location)
            |--------------------------------------------------------------------------
            */

            Geofence::updateOrCreate(
                ['device_id' => $device->id, 'type' => 'radius'],
                [
                    'name' => 'Rumah',
                    'description' => 'Area sekitar rumah, notifikasi jika kendaraan keluar radius.',
                    'config' => [
                        'center' => [
                            'lat' => $this->homeLat,
                            'lng' => $this->homeLng,
                        ],
                        'radius' => 500,
                        'unit' => 'meter',
                        'source' => 'home_location',
                    ],
                    'status' => true,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | 6. Simulasi rute perjalanan: beberapa device_log + travel_history
            |    berurutan menjauh dari rumah (mimics DeviceLogService::store +
            |    TravelHistoryService::store untuk tiap titik MQTT masuk)
            |--------------------------------------------------------------------------
            */

            $route = [
                ['lat' => -6.19524, 'lng' => 106.82301, 'speed' => 0,  'heading' => 0,   'address' => 'Jl. MH Thamrin, Jakarta Pusat, DKI Jakarta'],
                ['lat' => -6.19790, 'lng' => 106.82405, 'speed' => 28, 'heading' => 160, 'address' => 'Jl. Kebon Sirih, Jakarta Pusat, DKI Jakarta'],
                ['lat' => -6.20124, 'lng' => 106.84262, 'speed' => 35, 'heading' => 95,  'address' => 'Jl. Jenderal Sudirman, Jakarta Pusat, DKI Jakarta'],
                ['lat' => -6.22443, 'lng' => 106.84041, 'speed' => 22, 'heading' => 180, 'address' => 'Jl. Gatot Subroto, Jakarta Selatan, DKI Jakarta'],
                ['lat' => -6.22521, 'lng' => 106.84119, 'speed' => 0,  'heading' => 0,   'address' => 'Jl. Gatot Subroto, Jakarta Selatan, DKI Jakarta'],
            ];

            $startedAt = Carbon::now()->subHours(3);
            $deviceLogs = [];

            foreach ($route as $index => $point) {

                $receivedAt = $startedAt->copy()->addMinutes($index * 15);

                $log = DeviceLog::create([
                    'device_id' => $device->id,
                    'message_id' => $index + 1,
                    'payload' => [
                        'message_id' => $index + 1,
                        'lat' => $point['lat'],
                        'lng' => $point['lng'],
                        'speed' => $point['speed'],
                        'heading' => $point['heading'],
                        'battery' => 87,
                        'satellite' => 11,
                        'received_at' => $receivedAt->toIso8601String(),
                    ],
                    'status' => 'valid',
                    'received_at' => $receivedAt,
                ]);

                $deviceLogs[] = $log;

                TravelHistory::create([
                    'device_log_id' => $log->id,
                    'device_id' => $device->id,
                    'location' => [
                        'lat' => $point['lat'],
                        'lng' => $point['lng'],
                        'speed' => $point['speed'],
                        'heading' => $point['heading'],
                        'battery' => 87,
                        'satellite' => 11,
                    ],
                    'search_address' => $point['address'],
                    'received_at' => $receivedAt,
                ]);
            }

            $lastPoint = end($route);
            $lastLog = end($deviceLogs);

            /*
            |--------------------------------------------------------------------------
            | 7. Stop history (kendaraan berhenti di titik terakhir, mimics
            |    StopDetectionService::createStopHistory + updateStopHistory)
            |--------------------------------------------------------------------------
            */

            $stopStart = $startedAt->copy()->addMinutes((count($route) - 1) * 15);
            $stopEnd = $stopStart->copy()->addMinutes(20);

            $stopHistory = StopHistory::create([
                'device_id' => $device->id,
                'location' => [
                    'lat' => $lastPoint['lat'],
                    'lng' => $lastPoint['lng'],
                    'speed' => 0,
                    'heading' => 0,
                    'battery' => 87,
                    'satellite' => 11,
                ],
                'search_address' => $lastPoint['address'],
                'start_time' => $stopStart,
                'end_time' => $stopEnd,
                'duration_seconds' => $stopStart->diffInSeconds($stopEnd),
                'notification_sent' => true,
            ]);

            /*
            |--------------------------------------------------------------------------
            | 8. Notifications (mimics NotificationService payload shapes)
            |--------------------------------------------------------------------------
            */

            Notification::create([
                'device_id' => $device->id,
                'stop_history_id' => $stopHistory->id,
                'type' => 'stop',
                'data' => [
                    'title' => 'Kendaraan Berhenti',
                    'message' => sprintf(
                        'Kendaraan berhenti selama %d menit.',
                        (int) floor($stopHistory->duration_seconds / 60)
                    ),
                    'location' => $stopHistory->location,
                    'search_address' => $stopHistory->search_address,
                    'start_time' => $stopHistory->start_time,
                    'end_time' => $stopHistory->end_time,
                ],
                'status' => 'sent',
                'sent_at' => $stopEnd,
            ]);

            Notification::create([
                'device_id' => $device->id,
                'type' => 'device_online',
                'data' => [
                    'title' => 'Perangkat Online',
                    'message' => 'Perangkat GPS-AF-0001 kembali online.',
                ],
                'status' => 'sent',
                'sent_at' => $startedAt,
            ]);
        });
    }
}
