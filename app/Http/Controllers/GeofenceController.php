<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteGeofenceRequest;
use App\Http\Requests\StoreGeofenceRequest;
use App\Http\Requests\UpdateGeofenceStatusRequest;
use App\Services\GeofenceService;
use Illuminate\Http\JsonResponse;

class GeofenceController extends Controller
{
    public function __construct(
        protected GeofenceService $geofenceService
    ) {}

    /**
     * --------------------------------------------------------------------------
     * Get Geofence By Device
     * --------------------------------------------------------------------------
     */
    public function index(
        int $device
    ): JsonResponse {

        $geofences = $this->geofenceService->getByDevice(
            $device
        );

        return response()->json([

            'success' => true,

            'message' => 'Daftar geofence berhasil diambil.',

            'data' => $geofences,

        ]);
    }

    /**
     * --------------------------------------------------------------------------
     * Store
     * --------------------------------------------------------------------------
     */
    public function store(
        StoreGeofenceRequest $request
    ): JsonResponse {

        $geofence = $this->geofenceService->store(
            $request->validated()
        );

        return response()->json([

            'success' => true,

            'message' => 'Geofence berhasil ditambahkan.',

            'data' => $geofence,

        ], 201);
    }

    /**
     * --------------------------------------------------------------------------
     * Update Status
     * --------------------------------------------------------------------------
     */
    public function updateStatus(
        UpdateGeofenceStatusRequest $request,
        int $geofence
    ): JsonResponse {

        $geofence = $this->geofenceService->updateStatus(

            $geofence,

            $request->validated()['status']

        );

        return response()->json([

            'success' => true,

            'message' => 'Status geofence berhasil diperbarui.',

            'data' => $geofence,

        ]);
    }

    /**
     * --------------------------------------------------------------------------
     * Delete Single
     * --------------------------------------------------------------------------
     */
    public function destroy(
        int $geofence
    ): JsonResponse {

        $this->geofenceService->destroy(
            $geofence
        );

        return response()->json([

            'success' => true,

            'message' => 'Geofence berhasil dihapus.',

        ]);
    }

    /**
     * --------------------------------------------------------------------------
     * Delete Multiple
     * --------------------------------------------------------------------------
     */
    public function destroyMany(
        DeleteGeofenceRequest $request
    ): JsonResponse {

        $deleted = $this->geofenceService->destroyMany(

            $request->validated()['geofence_ids']

        );

        return response()->json([

            'success' => true,

            'message' => "{$deleted} geofence berhasil dihapus.",

        ]);
    }
}
