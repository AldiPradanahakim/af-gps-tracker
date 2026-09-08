<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VehicleLocationUpdated implements ShouldBroadcastNow
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Device Primary Key.
     */
    public string $deviceId;

    /**
     * Broadcast Payload.
     */
    public array $payload;

    /**
     * Create Event.
     */
    public function __construct(
        string $deviceId,
        array $payload
    ) {
        $this->deviceId = $deviceId;

        $this->payload = $payload;
    }

    /**
     * Broadcast Channel.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(
                "vehicle.{$this->deviceId}"
            ),
        ];
    }

    /**
     * Event Alias.
     */
    public function broadcastAs(): string
    {
        return 'location.updated';
    }

    /**
     * Broadcast Payload.
     */
    public function broadcastWith(): array
    {
        return $this->payload;
    }
}