<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGeofenceRequest;
use App\Services\GeofenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GeofenceController extends Controller
{
    public function __construct(
        protected GeofenceService $geofenceService
    ) {}

    /**
     * Mengambil seluruh geofence milik user.
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $this->geofenceService->getAll(
                $request->user()->id
            )
        );
    }

    /**
     * Menyimpan geofence baru.
     */
    public function store(
        StoreGeofenceRequest $request
    ): RedirectResponse {

        $this->geofenceService->store(
            $request->validated()
        );

        return back()->with(
            'success',
            'Geofence berhasil ditambahkan.'
        );
    }

    /**
     * Mengaktifkan / Menonaktifkan geofence.
     */
    public function updateStatus(
        Request $request,
        int $geofence
    ): JsonResponse {

        $request->validate([

            'status' => [

                'required',

                'boolean',

            ],

        ]);

        $data = $this->geofenceService
            ->updateStatus(
                $geofence,
                (bool) $request->boolean('status')
            );

        return response()->json([

            'message' => 'Status geofence berhasil diperbarui.',

            'data' => $data,

        ]);
    }

    /**
     * Menghapus geofence.
     */
    public function destroy(
        int $geofence
    ): JsonResponse {

        $this->geofenceService
            ->delete($geofence);

        return response()->json([

            'message' => 'Geofence berhasil dihapus.',

        ]);
    }
}
