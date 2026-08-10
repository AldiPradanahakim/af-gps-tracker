<?php

namespace App\Http\Controllers;

use App\Services\Notification\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * --------------------------------------------------------------------------
     * Daftar Notification (Milik User Login)
     * --------------------------------------------------------------------------
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = $this->notificationService->listForUser(

            Auth::user(),

            (int) $request->query('per_page', 20)

        );

        return response()->json([

            'success' => true,

            'message' => 'Daftar notifikasi berhasil diambil.',

            'data' => $notifications,

        ]);
    }

    /**
     * --------------------------------------------------------------------------
     * Tandai Satu Notification Sebagai Telah Dibaca
     * --------------------------------------------------------------------------
     */
    public function markAsRead(string $notification): JsonResponse
    {
        $result = $this->notificationService->markAsRead(

            Auth::user(),

            $notification

        );

        return response()->json([

            'success' => true,

            'message' => 'Notifikasi ditandai telah dibaca.',

            'data' => $result,

        ]);
    }

    /**
     * --------------------------------------------------------------------------
     * Tandai Semua Notification Sebagai Telah Dibaca
     * --------------------------------------------------------------------------
     */
    public function markAllAsRead(): JsonResponse
    {
        $total = $this->notificationService->markAllAsRead(

            Auth::user()

        );

        return response()->json([

            'success' => true,

            'message' => "{$total} notifikasi ditandai telah dibaca.",

        ]);
    }
}
