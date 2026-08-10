<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationCreated implements ShouldBroadcast
{
    use Dispatchable;
    use SerializesModels;

    /**
     * User Primary Key.
     */
    public string $userId;

    /**
     * Broadcast Payload.
     */
    public array $payload;

    /**
     * Create Event.
     */
    public function __construct(
        string $userId,
        array $payload
    ) {

        $this->userId = $userId;

        $this->payload = $payload;
    }

    /**
     * Broadcast Channel.
     */
    public function broadcastOn(): array
    {

        return [

            new PrivateChannel(

                "user.{$this->userId}"

            ),

        ];
    }

    /**
     * Event Alias.
     */
    public function broadcastAs(): string
    {

        return 'notification.created';
    }

    /**
     * Broadcast Payload.
     */
    public function broadcastWith(): array
    {

        return $this->payload;
    }
}
