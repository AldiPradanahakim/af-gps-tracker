<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\DeviceLog;
use App\Models\User;
use App\Services\Device\DeviceLogService;
use App\Services\MQTT\RealtimeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class DeviceLogPersistenceTest extends TestCase
{
    use RefreshDatabase;

    protected Device $device;
    protected DeviceLogService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();

        $this->device = Device::create([
            'user_id' => $user->id,
            'device_id' => 'DEV-TEST-001',
            'device_password' => 'secret123',
            'is_active' => true,
        ]);

        $this->service = app(DeviceLogService::class);
    }

    public function test_first_position_is_always_saved_as_reference(): void
    {
        $payload = [
            'device_id' => 'DEV-TEST-001',
            'message_id' => 1001,
            'lat' => -6.912345,
            'lng' => 107.612345,
            'speed' => 0.0,
            'heading' => 90,
            'battery' => 95,
            'satellite' => 10,
            'received_at' => Carbon::now()->toISOString(),
        ];

        $log = $this->service->store($this->device, $payload);

        $this->assertNotNull($log);
        $this->assertInstanceOf(DeviceLog::class, $log);
        $this->assertDatabaseHas('device_logs', [
            'device_id' => $this->device->id,
            'message_id' => 1001,
        ]);
    }

    public function test_speed_below_one_and_distance_below_threshold_is_not_saved(): void
    {
        // 1. Simpan posisi awal (ref)
        $this->service->store($this->device, [
            'device_id' => 'DEV-TEST-001',
            'message_id' => 1001,
            'lat' => -6.912345,
            'lng' => 107.612345,
            'speed' => 0.0,
            'heading' => 90,
            'battery' => 95,
            'satellite' => 10,
            'received_at' => Carbon::now()->subSeconds(2)->toISOString(),
        ]);

        // 2. Kirim payload dengan pergeseran sangat kecil (~0.1 meter) dan speed 0.5 km/h (< 1 km/h)
        $payload = [
            'device_id' => 'DEV-TEST-001',
            'message_id' => 1002,
            'lat' => -6.9123458, // sangat dekat
            'lng' => 107.6123458,
            'speed' => 0.5,
            'heading' => 90,
            'battery' => 95,
            'satellite' => 10,
            'received_at' => Carbon::now()->toISOString(),
        ];

        $log = $this->service->store($this->device, $payload);

        $this->assertNull($log);
        $this->assertDatabaseMissing('device_logs', [
            'device_id' => $this->device->id,
            'message_id' => 1002,
        ]);
    }

    public function test_speed_below_one_and_distance_above_threshold_is_saved(): void
    {
        // 1. Simpan posisi awal
        $this->service->store($this->device, [
            'device_id' => 'DEV-TEST-001',
            'message_id' => 1001,
            'lat' => -6.912345,
            'lng' => 107.612345,
            'speed' => 0.0,
            'heading' => 90,
            'battery' => 95,
            'satellite' => 10,
            'received_at' => Carbon::now()->subSeconds(2)->toISOString(),
        ]);

        // 2. Kirim payload dengan pergeseran > 0.5 meter (misal ~10 meter) tapi speed 0.8 km/h (< 1 km/h)
        $payload = [
            'device_id' => 'DEV-TEST-001',
            'message_id' => 1002,
            'lat' => -6.912450, // geser ~11 meter
            'lng' => 107.612345,
            'speed' => 0.8,
            'heading' => 90,
            'battery' => 95,
            'satellite' => 10,
            'received_at' => Carbon::now()->toISOString(),
        ];

        $log = $this->service->store($this->device, $payload);

        $this->assertNotNull($log);
        $this->assertInstanceOf(DeviceLog::class, $log);
        $this->assertDatabaseHas('device_logs', [
            'device_id' => $this->device->id,
            'message_id' => 1002,
        ]);
    }

    public function test_speed_greater_or_equal_to_one_bypasses_distance_threshold(): void
    {
        // 1. Simpan posisi awal
        $this->service->store($this->device, [
            'device_id' => 'DEV-TEST-001',
            'message_id' => 1001,
            'lat' => -6.912345,
            'lng' => 107.612345,
            'speed' => 0.0,
            'heading' => 90,
            'battery' => 95,
            'satellite' => 10,
            'received_at' => Carbon::now()->subSeconds(2)->toISOString(),
        ]);

        // 2. Kirim payload dengan jarak sangat dekat (< 0.5m) TETAPI speed >= 1.0 km/h (tepat 1.0)
        $payloadExactOne = [
            'device_id' => 'DEV-TEST-001',
            'message_id' => 1002,
            'lat' => -6.9123452,
            'lng' => 107.6123452,
            'speed' => 1.0, // Batas 1.0 km/h -> threshold nonaktif
            'heading' => 90,
            'battery' => 95,
            'satellite' => 10,
            'received_at' => Carbon::now()->subSecond()->toISOString(),
        ];

        $log1 = $this->service->store($this->device, $payloadExactOne);

        $this->assertNotNull($log1);
        $this->assertInstanceOf(DeviceLog::class, $log1);
        $this->assertDatabaseHas('device_logs', [
            'device_id' => $this->device->id,
            'message_id' => 1002,
        ]);

        // 3. Kirim payload lain dengan speed 25 km/h
        $payloadMoving = [
            'device_id' => 'DEV-TEST-001',
            'message_id' => 1003,
            'lat' => -6.9123453,
            'lng' => 107.6123453,
            'speed' => 25.0,
            'heading' => 90,
            'battery' => 95,
            'satellite' => 10,
            'received_at' => Carbon::now()->toISOString(),
        ];

        $log2 = $this->service->store($this->device, $payloadMoving);

        $this->assertNotNull($log2);
        $this->assertInstanceOf(DeviceLog::class, $log2);
        $this->assertDatabaseHas('device_logs', [
            'device_id' => $this->device->id,
            'message_id' => 1003,
        ]);
    }

    public function test_realtime_service_broadcast_payload_contains_received_at_and_updated_at(): void
    {
        $realtimeService = app(RealtimeService::class);

        $time = Carbon::now()->toISOString();
        $payload = [
            'device_id' => 'DEV-TEST-001',
            'message_id' => 2001,
            'lat' => -6.912345,
            'lng' => 107.612345,
            'speed' => 15.5,
            'heading' => 180,
            'battery' => 88,
            'satellite' => 12,
            'received_at' => $time,
        ];

        // Refleksi untuk cek method protected payload
        $reflection = new \ReflectionClass(RealtimeService::class);
        $method = $reflection->getMethod('payload');
        $method->setAccessible(true);

        $result = $method->invokeArgs($realtimeService, [
            $this->device,
            $payload,
            [],
            null,
            null,
        ]);

        $this->assertArrayHasKey('received_at', $result);
        $this->assertArrayHasKey('updated_at', $result);
        $this->assertSame($time, $result['received_at']);
        $this->assertSame($time, $result['updated_at']);
        $this->assertSame(15.5, $result['speed']);
        $this->assertSame(88, $result['battery']);
        $this->assertSame(12, $result['satellite']);
        $this->assertArrayHasKey('received_at', $result['location']);
    }
}
