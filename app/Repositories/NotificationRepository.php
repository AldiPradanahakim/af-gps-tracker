<?php

namespace App\Repositories;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class NotificationRepository
{
    /**
     * Simpan notification.
     */
    public function create(array $data): Notification
    {
        return Notification::create($data);
    }

    /**
     * Cari notification milik user (via device.user_id).
     */
    public function findOwnedByUser(
        string $id,
        string $userId
    ): ?Notification {

        return Notification::query()

            ->whereKey($id)

            ->whereHas('device', function ($query) use ($userId) {

                $query->where('user_id', $userId);
            })

            ->first();
    }

    /**
     * Cari notification milik device tertentu (untuk fokus peta di
     * halaman Detail Kendaraan - lihat VehicleService::show()).
     */
    public function findForDevice(
        string $id,
        string $deviceId
    ): ?Notification {

        return Notification::query()

            ->whereKey($id)

            ->where('device_id', $deviceId)

            ->first();
    }

    /**
     * Daftar notification milik user, terbaru lebih dulu.
     */
    public function listByUser(
        string $userId,
        int $perPage = 20
    ): LengthAwarePaginator {

        return Notification::query()

            ->with(['device.vehicle'])

            ->whereHas('device', function ($query) use ($userId) {

                $query->where('user_id', $userId);
            })

            ->latest()

            ->paginate($perPage);
    }

    /**
     * Notification milik user yang belum dibaca, terbaru lebih dulu.
     *
     * Dipakai sebagai sumber tampilan notification dropdown: setelah
     * refresh/login, notification yang sudah dibaca tidak perlu
     * ditampilkan lagi.
     */
    public function unreadByUser(
        string $userId,
        int $limit = 50
    ): Collection {

        return Notification::query()

            ->with(['device.vehicle'])

            ->whereNull('read_at')

            ->whereHas('device', function ($query) use ($userId) {

                $query->where('user_id', $userId);
            })

            ->latest()

            ->limit($limit)

            ->get();
    }

    /**
     * Tandai satu notification sebagai telah dibaca.
     */
    public function markAsRead(Notification $notification): Notification
    {
        if (! $notification->read_at) {

            $notification->update([

                'read_at' => Carbon::now(),

            ]);
        }

        return $notification->refresh();
    }

    /**
     * Tandai seluruh notification milik user sebagai telah dibaca.
     */
    public function markAllAsReadByUser(string $userId): int
    {
        return Notification::query()

            ->whereNull('read_at')

            ->whereHas('device', function ($query) use ($userId) {

                $query->where('user_id', $userId);
            })

            ->update([

                'read_at' => Carbon::now(),

            ]);
    }
}
