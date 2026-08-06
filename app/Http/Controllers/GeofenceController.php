<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteGeofenceRequest;
use App\Http\Requests\StoreGeofenceRequest;
use App\Http\Requests\UpdateGeofenceRequest;
use App\Http\Requests\UpdateGeofenceStatusRequest;
use App\Services\GeofenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;


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
    public function store(StoreGeofenceRequest $request): JsonResponse
    {
        try {

            $result = $this->geofenceService->store(
                $request->validated()
            );

            $isMany = $result instanceof Collection;

            return response()->json([
                'success' => true,
                'message' => $isMany
                    ? $result->count() . ' geofence berhasil ditambahkan.'
                    : 'Geofence berhasil ditambahkan.',
                'data' => $isMany
                    ? $result->values()
                    : $result,
            ], 201);
        } catch (ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first()
                    ?? 'Data tidak valid.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * --------------------------------------------------------------------------
     * Tipe geofence yang sudah dimiliki tiap device milik user.
     * Dipakai frontend untuk menonaktifkan pilihan yang sudah penuh.
     * --------------------------------------------------------------------------
     */
    public function types(): JsonResponse
    {
        return response()->json([

            'success' => true,

            'message' => 'Tipe geofence per kendaraan berhasil diambil.',

            'data' => $this->geofenceService->getTypesByUser(),

        ]);
    }

    /**
     * --------------------------------------------------------------------------
     * Update (nama, status, geometry)
     * --------------------------------------------------------------------------
     */
    public function update(
        UpdateGeofenceRequest $request,
        int $geofence
    ): JsonResponse {

        try {

            $result = $this->geofenceService->update(
                $geofence,
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Geofence berhasil diperbarui.',
                'data' => $result,
            ]);
        } catch (ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first()
                    ?? 'Data tidak valid.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
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

    /**
     * --------------------------------------------------------------------------
     * Get All Geofence User
     * --------------------------------------------------------------------------
     */
    public function all(): JsonResponse
    {
        $geofences = $this->geofenceService->all();

        return response()->json([

            'success' => true,

            'message' => 'Daftar geofence berhasil diambil.',

            'data' => $geofences,

        ]);
    }
}
