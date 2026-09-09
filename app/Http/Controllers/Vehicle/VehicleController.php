<?php

namespace App\Http\Controllers\Vehicle;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExportHistoryRequest;
use App\Http\Requests\UpdateNotificationSettingRequest;
use App\Http\Requests\UpdateSpeedSettingRequest;
use App\Http\Requests\UpdateStopSettingRequest;
use App\Models\Device;
use App\Services\Vehicle\VehicleService;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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
        if ($device->user_id !== Auth::id()) {
            if (request()->expectsJson()) {
                abort(403, 'Kendaraan tidak ditemukan.');
            }
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                redirect()->route('home')->with('error', 'Kendaraan tidak ditemukan.')
            );
        }
    }

    /**
     * Halaman Detail Kendaraan.
     *
     * Query string opsional `?event={notification}` (dikirim dari link
     * "Detail Lengkap" pada halaman pelacakan publik/Email/WhatsApp)
     * membuat peta fokus ke lokasi persis milik notification tersebut,
     * bukan lokasi kendaraan terkini.
     */
    public function show(Request $request, Device $device): View
    {
        $this->authorizeDevice($device);

        return view(
            'vehicles.show',
            $this->vehicleService->show(
                $device,
                $request->query('event')
            )
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
                'regex:/^[A-Z]{1,2}\s\d{1,4}\s[A-Z]{1,3}$/i',
            ],

            'vehicle_type' => [
                'required',
                'in:motor,mobil,kendaraan_besar,sepeda',
            ],

            'marker_icon' => [
                'required',
                'in:motorcycle,car,pickup,truck,bus,van,taxi,ambulance,police,bicycle',
            ],

            'marker_color' => [
                'required',
                'in:green,blue,red,orange,yellow,purple,black,gray',
            ],

        ], [

            'plate_number.regex' => 'Format nomor polisi tidak sesuai. Gunakan format Huruf-Angka-Huruf, contoh: D 1234 ABC.',

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
     * Update Batas Kecepatan Setting
     * --------------------------------------------------------------------------
     */
    public function updateSpeedSetting(
        UpdateSpeedSettingRequest $request,
        Device $device
    ): JsonResponse {

        $this->authorizeDevice($device);

        $setting = $this->vehicleService->updateSpeedSetting(

            $device,

            $request->validated()

        );

        return response()->json([

            'success' => true,

            'message' => 'Pengaturan Batas Kecepatan berhasil diperbarui.',

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
     * Rute Mengikuti Jalan (Map-Matching)
     * --------------------------------------------------------------------------
     */
    public function route(
        Request $request,
        Device $device
    ): JsonResponse {

        $this->authorizeDevice($device);

        $validated = $request->validate([

            'points' => ['required', 'array', 'min:2', 'max:20000'],

            'points.*.lat' => ['required', 'numeric', 'between:-90,90'],

            'points.*.lng' => ['required', 'numeric', 'between:-180,180'],

        ]);

        $route = $this->vehicleService->route(

            $validated['points']

        );

        return response()->json([

            'success' => true,

            'message' => 'Rute berhasil diambil.',

            'data' => $route,

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

    /**
     * --------------------------------------------------------------------------
     * Export Riwayat Perjalanan (PDF)
     * --------------------------------------------------------------------------
     */
    public function exportTravel(
        ExportHistoryRequest $request,
        Device $device
    ): Response {

        $this->authorizeDevice($device);

        $pdf = $this->vehicleService->exportTravelHistory(

            $device,

            $request->validated('from'),

            $request->validated('to')

        );

        return $pdf->download(
            $this->exportFilename($device, 'riwayat-perjalanan')
        );
    }

    /**
     * --------------------------------------------------------------------------
     * Export Riwayat Kendaraan Berhenti (PDF)
     * --------------------------------------------------------------------------
     */
    public function exportStop(
        ExportHistoryRequest $request,
        Device $device
    ): Response {

        $this->authorizeDevice($device);

        $pdf = $this->vehicleService->exportStopHistory(

            $device,

            $request->validated('from'),

            $request->validated('to')

        );

        return $pdf->download(
            $this->exportFilename($device, 'riwayat-berhenti')
        );
    }

    /**
     * Nama file PDF hasil export, disamarkan dari plate_number (fallback
     * ke device_id) supaya tetap terbaca meski karakternya tidak standar.
     */
    private function exportFilename(
        Device $device,
        string $prefix
    ): string {

        $device->loadMissing('vehicle');

        $plate = Str::slug(
            $device->vehicle?->plate_number ?? $device->device_id ?? 'kendaraan'
        );

        return sprintf(
            '%s-%s-%s.pdf',
            $prefix,
            $plate ?: 'kendaraan',
            now()->format('Ymd-His')
        );
    }
}
