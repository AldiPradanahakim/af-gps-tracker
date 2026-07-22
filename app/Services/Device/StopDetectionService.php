<?php

namespace App\Services\Device;

use App\Models\Device;
use App\Models\StopHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class StopDetectionService
{
    /**
     * Process stop detection.
     *
     * Return:
     * - StopHistory : jika durasi berhenti sudah mencapai batas
     *                 dan notifikasi belum pernah dikirim.
     * - null        : jika belum memenuhi syarat.
     */
    public function process(
        Device $device,
        array $payload,
        string $searchAddress
    ): ?StopHistory {

        $setting = $this->extractSetting(
            $device
        );

        /*
        |--------------------------------------------------------------------------
        | Stop Detection Disabled
        |--------------------------------------------------------------------------
        */

        if (! $setting['enabled']) {
            return null;
        }

        $receivedAt = Carbon::parse(
            $this->extractReceivedAt(
                $payload
            )
        );

        $speed = $this->extractSpeed(
            $payload
        );

        /*
        |--------------------------------------------------------------------------
        | Vehicle Moving
        |--------------------------------------------------------------------------
        */

        if ($speed > 0) {

            $this->finishStopHistory(
                $device,
                $receivedAt
            );

            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | Active Stop History
        |--------------------------------------------------------------------------
        */

        $stopHistory = $this->getActiveStopHistory(
            $device
        );

        /*
        |--------------------------------------------------------------------------
        | Create First Stop History
        |--------------------------------------------------------------------------
        */

        if (! $stopHistory) {

            return $this->createStopHistory(
                device: $device,
                payload: $payload,
                searchAddress: $searchAddress,
                receivedAt: $receivedAt
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Continue Existing Stop
        |--------------------------------------------------------------------------
        */

        return $this->updateStopHistory(
            stopHistory: $stopHistory,
            receivedAt: $receivedAt,
            minimumMinutes: $setting['minutes']
        );
    }

    /**
     * Get active stop history.
     */
    protected function getActiveStopHistory(
        Device $device
    ): ?StopHistory {

        return StopHistory::query()

            ->where(
                'device_id',
                $device->id
            )

            ->whereNull(
                'end_time'
            )

            ->latest('start_time')

            ->first();
    }

    /**
     * Create first stop history.
     */
    protected function createStopHistory(
        Device $device,
        array $payload,
        string $searchAddress,
        Carbon $receivedAt
    ): StopHistory {

        return DB::transaction(function () use (
            $device,
            $payload,
            $searchAddress,
            $receivedAt
        ) {

            return StopHistory::create([

                'device_id' => $device->id,

                'location' => [

                    'lat' => $payload['lat'],

                    'lng' => $payload['lng'],

                    'speed' => $payload['speed'] ?? 0,

                    'heading' => $payload['heading'] ?? 0,

                    'battery' => $payload['battery'] ?? null,

                    'satellite' => $payload['satellite'] ?? null,

                ],

                'search_address' => $searchAddress,

                'start_time' => $receivedAt,

                'duration_seconds' => 0,

                'notification_sent' => false,

            ]);
        });
    }
    /**
     * Update active stop history.
     */
    protected function updateStopHistory(
        StopHistory $stopHistory,
        Carbon $receivedAt,
        int $minimumMinutes
    ): ?StopHistory {

        return DB::transaction(function () use (
            $stopHistory,
            $receivedAt,
            $minimumMinutes
        ) {

            $duration = $stopHistory
                ->start_time
                ->diffInSeconds(
                    $receivedAt
                );

            $stopHistory->update([

                'duration_seconds' => $duration,

            ]);

            /*
            |--------------------------------------------------------------------------
            | Notification Already Sent
            |--------------------------------------------------------------------------
            */

            if ($stopHistory->notification_sent) {

                return null;
            }

            /*
            |--------------------------------------------------------------------------
            | Minimum Duration Not Reached
            |--------------------------------------------------------------------------
            */

            if (
                $duration <
                ($minimumMinutes * 60)
            ) {

                return null;
            }

            /*
            |--------------------------------------------------------------------------
            | Ready To Notify
            |--------------------------------------------------------------------------
            */

            return $stopHistory;
        });
    }

    /**
     * Finish current stop history.
     */
    protected function finishStopHistory(
        Device $device,
        Carbon $receivedAt
    ): void {

        $stopHistory = $this->getActiveStopHistory(
            $device
        );

        if (! $stopHistory) {
            return;
        }

        DB::transaction(function () use (
            $stopHistory,
            $receivedAt
        ) {

            $duration = $stopHistory
                ->start_time
                ->diffInSeconds(
                    $receivedAt
                );

            $stopHistory->update([

                'end_time' => $receivedAt,

                'duration_seconds' => $duration,

            ]);
        });
    }

    /**
     * Extract stop detection setting.
     */
    protected function extractSetting(
        Device $device
    ): array {

        $setting = $device->stop_setting ?? [];

        return [

            'enabled' => (bool) (
                $setting['enabled']
                ?? false
            ),

            'minutes' => (int) (
                $setting['minutes']
                ?? 5
            ),

        ];
    }

    /**
     * Extract speed.
     */
    protected function extractSpeed(
        array $payload
    ): float {

        if (! isset($payload['speed'])) {

            throw new InvalidArgumentException(
                'speed is required.'
            );
        }

        if (! is_numeric($payload['speed'])) {

            throw new InvalidArgumentException(
                'Invalid speed.'
            );
        }

        return (float) $payload['speed'];
    }
    /**
     * Mark notification as sent.
     */
    public function markNotificationSent(
        StopHistory $stopHistory
    ): void {

        $stopHistory->update([

            'notification_sent' => true,

        ]);
    }

    /**
     * Extract received_at.
     */
    protected function extractReceivedAt(
        array $payload
    ): string {

        if (! isset($payload['received_at'])) {

            throw new InvalidArgumentException(
                'received_at is required.'
            );
        }

        return $payload['received_at'];
    }

    /**
     * Check whether stop detection is enabled.
     */
    public function isEnabled(
        Device $device
    ): bool {

        return $this->extractSetting(
            $device
        )['enabled'];
    }

    /**
     * Get configured stop duration.
     */
    public function getMinimumMinutes(
        Device $device
    ): int {

        return $this->extractSetting(
            $device
        )['minutes'];
    }

    /**
     * Determine whether vehicle is stopped.
     */
    public function isStopped(
        array $payload
    ): bool {

        return $this->extractSpeed(
            $payload
        ) == 0.0;
    }

    /**
     * Determine whether vehicle is moving.
     */
    public function isMoving(
        array $payload
    ): bool {

        return ! $this->isStopped(
            $payload
        );
    }
}
