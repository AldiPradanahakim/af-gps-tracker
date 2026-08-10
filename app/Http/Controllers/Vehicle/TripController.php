<?php

namespace App\Http\Controllers\Vehicle;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Services\Vehicle\TripService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * --------------------------------------------------------------------------
 * Trip Controller
 * --------------------------------------------------------------------------
 * Controller khusus & terpisah dari VehicleController supaya fitur trip
 * grouping tidak menyentuh/berisiko konflik dengan VehicleController yang
 * sedang dikerjakan paralel.
 * --------------------------------------------------------------------------
 */
class TripController extends Controller
{
    public function __construct(
        protected TripService $tripService
    ) {}

    /**
     * Pastikan device yang diakses memang milik user yang login.
     * Mencegah IDOR: user lain tidak boleh bisa lihat trip milik device
     * milik user lain hanya dengan menebak/mengetahui UUID device-nya.
     *
     * Mirror dari VehicleController::authorizeDevice().
     */
    private function authorizeDevice(Device $device): void
    {
        abort_if($device->user_id !== Auth::id(), 403, 'Kendaraan tidak ditemukan.');
    }

    /**
     * --------------------------------------------------------------------------
     * Daftar Trip (Perjalanan yang Dikelompokkan)
     * --------------------------------------------------------------------------
     */
    public function index(
        Device $device,
        Request $request
    ): JsonResponse {

        $this->authorizeDevice($device);

        $trips = $this->tripService->trips(

            $device,

            $request->query('start_date'),

            $request->query('end_date')

        );

        return response()->json([

            'success' => true,

            'message' => 'Riwayat perjalanan (trip) berhasil diambil.',

            'data' => $trips,

        ]);
    }
}
