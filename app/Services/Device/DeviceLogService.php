<?php

namespace App\Services\Device;

use App\Models\Device;
use App\Models\DeviceLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class DeviceLogService
{
    /**
     * Store incoming device payload.
     */
    public function store(
        Device $device,
        array $payload
    ): DeviceLog {

        return DB::transaction(function () use (
            $device,
            $payload
        ) {

            $messageId = $this->extractMessageId($payload);

            $receivedAt = $this->extractReceivedAt($payload);

            $existing = DeviceLog::query()

                ->where(
                    'device_id',
                    $device->id
                )

                ->where(
                    'message_id',
                    $messageId
                )

                ->first();

            if ($existing) {
                return $existing;
            }

            return DeviceLog::create([

                'device_id' => $device->id,

                'message_id' => $messageId,

                'payload' => $payload,

                'status' => 'valid',

                'received_at' => $receivedAt,

            ]);
        });
    }

    /**
     * Extract message id.
     */
    protected function extractMessageId(
        array $payload
    ): int {

        if (! isset($payload['message_id'])) {

            throw new InvalidArgumentException(
                'message_id is required.'
            );
        }

        if (! is_numeric($payload['message_id'])) {

            throw new InvalidArgumentException(
                'Invalid message_id.'
            );
        }

        return (int) $payload['message_id'];
    }

    /**
     * Extract received_at.
     */
    protected function extractReceivedAt(
        array $payload
    ): Carbon {

        if (! isset($payload['received_at'])) {

            throw new InvalidArgumentException(
                'received_at is required.'
            );
        }

        return Carbon::parse(
            $payload['received_at']
        );
    }
}
