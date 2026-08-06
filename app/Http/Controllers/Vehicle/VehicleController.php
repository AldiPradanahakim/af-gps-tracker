<?php

namespace App\Http\Controllers\Vehicle;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Services\Vehicle\VehicleService;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function __construct(
        protected VehicleService $vehicleService
    ) {}

    /**
     * Halaman Detail Kendaraan.
     */
    public function show(Device $device): View
    {
        return view(
            'vehicles.show',
            $this->vehicleService->show($device)
        );
    }

    /**
     * --------------------------------------------------------------------------
     * Update Vehicle Information
     * --------------------------------------------------------------------------
     */
    public function updateInformation(
        Request $request,
        Device $device
    ): JsonResponse {

        $validated = $request->validate([

            'vehicle_name' => [
                'required',
                'string',
                'max:100',
            ],

            'plate_number' => [
                'required',
                'string',
                'max:20',
            ],

            'vehicle_type' => [
                'required',
                'in:motor,mobil',
            ],

        ]);

        $vehicle = $this->vehicleService->updateInformation(

            $device,

            $validated

        );

        return response()->json([

            'success' => true,

            'message' => 'Informasi kendaraan berhasil diperbarui.',

            'data' => $vehicle,

        ]);
    }

    /**
     * --------------------------------------------------------------------------
     * Latest Location
     * --------------------------------------------------------------------------
     */
    public function latest(
        Device $device
    ): JsonResponse {

        $location = $this->vehicleService->latest(
            $device
        );

        return response()->json([

            'success' => true,

            'message' => 'Lokasi terakhir berhasil diambil.',

            'data' => $location,

        ]);
    }

    /**
     * --------------------------------------------------------------------------
     * Travel History
     * --------------------------------------------------------------------------
     */
    public function history(
        Request $request,
        Device $device
    ): JsonResponse {

        $histories = $this->vehicleService->history(

            $device,

            $request->query('date')

        );

        return response()->json([

            'success' => true,

            'message' => 'Riwayat perjalanan berhasil diambil.',

            'data' => $histories,

        ]);
    }

    /**
     * --------------------------------------------------------------------------
     * Playback
     * --------------------------------------------------------------------------
     */
    public function playback(
        Request $request,
        Device $device
    ): JsonResponse {

        $playback = $this->vehicleService->playback(

            $device,

            $request->query('date')

        );

        return response()->json([

            'success' => true,

            'message' => 'Data playback berhasil diambil.',

            'data' => $playback,

        ]);
    }

    /**
     * --------------------------------------------------------------------------
     * Summary
     * --------------------------------------------------------------------------
     */
    public function summary(
        Device $device
    ): JsonResponse {

        $summary = $this->vehicleService->summary(
            $device
        );

        return response()->json([

            'success' => true,

            'message' => 'Ringkasan perjalanan berhasil diambil.',

            'data' => $summary,

        ]);
    }

    /**
     * --------------------------------------------------------------------------
     * Activity Timeline
     * --------------------------------------------------------------------------
     */
    public function activity(
        Device $device
    ): JsonResponse {

        $activities = $this->vehicleService->activity(
            $device
        );

        return response()->json([

            'success' => true,

            'message' => 'Aktivitas kendaraan berhasil diambil.',

            'data' => $activities,

        ]);
    }

    /**
     * --------------------------------------------------------------------------
     * Stop History
     * --------------------------------------------------------------------------
     */
    public function stop(
        Device $device
    ): JsonResponse {

        $stops = $this->vehicleService->stop(
            $device
        );

        return response()->json([

            'success' => true,

            'message' => 'Riwayat kendaraan berhenti berhasil diambil.',

            'data' => $stops,

        ]);
    }
}
