<?php

namespace App\Http\Controllers\Vehicle;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateNotificationSettingRequest;
use App\Http\Requests\UpdateStopSettingRequest;
use App\Models\Device;
use App\Services\Vehicle\VehicleService;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VehicleController extends Controller
{
    public function __construct(
        protected VehicleService $vehicleService
    ) {}

    /**
     * Pastikan device yang diakses memang milik user yang login.
     * Mencegah IDOR: user lain tidak boleh bisa lihat/ubah kendaraan
     * milik user lain hanya dengan menebak/mengetahui UUID device-nya.
     */
    private function authorizeDevice(Device $device): void
    {
        abort_if($device->user_id !== Auth::id(), 403, 'Kendaraan tidak ditemukan.');
    }

    /**
     * Halaman Detail Kendaraan.
     */
    public function show(Device $device): View
    {
        $this->authorizeDevice($device);

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

        $this->authorizeDevice($device);

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
     * Hapus Kendaraan
     * --------------------------------------------------------------------------
     */
    public function destroy(Device $device): JsonResponse
    {
        $this->authorizeDevice($device);

        $this->vehicleService->destroy($device);

        return response()->json([

            'success' => true,

            'message' => 'Kendaraan berhasil dihapus.',

        ]);
    }

    /**
     * --------------------------------------------------------------------------
     * Update Stop Detection Setting
     * --------------------------------------------------------------------------
     */
    public function updateStopSetting(
        UpdateStopSettingRequest $request,
        Device $device
    ): JsonResponse {

        $this->authorizeDevice($device);

        $setting = $this->vehicleService->updateStopSetting(

            $device,

            $request->validated()

        );

        return response()->json([

            'success' => true,

            'message' => 'Pengaturan Stop Detection berhasil diperbarui.',

            'data' => $setting,

        ]);
    }

    /**
     * --------------------------------------------------------------------------
     * Update Notifikasi Geofence Setting
     * --------------------------------------------------------------------------
     */
    public function updateNotificationSetting(
        UpdateNotificationSettingRequest $request,
        Device $device
    ): JsonResponse {

        $this->authorizeDevice($device);

        $setting = $this->vehicleService->updateNotificationSetting(

            $device,

            $request->validated()

        );

        return response()->json([

            'success' => true,

            'message' => 'Pengaturan notifikasi Geofence berhasil diperbarui.',

            'data' => $setting,

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

        $this->authorizeDevice($device);

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

        $this->authorizeDevice($device);

        $histories = $this->vehicleService->history(

            $device,

            $request->query('start_date'),

            $request->query('end_date')

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

        $this->authorizeDevice($device);

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

        $this->authorizeDevice($device);

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

        $this->authorizeDevice($device);

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

        $this->authorizeDevice($device);

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
