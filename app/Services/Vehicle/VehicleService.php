<?php

namespace App\Services\Vehicle;

use App\Models\Device;
use App\Repositories\Vehicle\VehicleRepository;
use App\Services\Notification\NotificationService;
use Illuminate\Support\Facades\Auth;

class VehicleService
{
    public function __construct(
        protected VehicleRepository $vehicleRepository,
        protected NotificationService $notificationService,
        protected RouteMatchingService $routeMatchingService,
        protected PdfExportService $pdfExportService
    ) {}

    /**
     * Data halaman detail kendaraan.
     *
     * Notification yang ditampilkan hanya yang belum dibaca (sama
     * seperti Home), agar konsisten di semua halaman.
     *
     * $eventNotificationId (query string ?event=) membuat peta fokus ke
     * lokasi PERSIS milik notification tersebut (diambil dari database,
     * bukan lokasi kendaraan terkini) - dipakai oleh link "Detail
     * Lengkap" dari halaman pelacakan publik/Email/WhatsApp, supaya
     * lokasi yang tampil selalu sama dengan yang tertulis di pesan.
     */
    public function show(
        Device $device,
        ?string $eventNotificationId = null
    ): array {

        $data = $this->vehicleRepository->detail($device);

        $data['notifications'] = $this->notificationService->unreadForUser(
            Auth::user()
        );

        $data['eventFocus'] = $eventNotificationId
            ? $this->buildEventFocus($device, $eventNotificationId)
            : null;

        return $data;
    }

    /**
     * Bangun data fokus peta dari satu notification (lihat show()).
     */
    protected function buildEventFocus(
        Device $device,
        string $notificationId
    ): ?array {

        $notification = $this->notificationService->findForDevice(
            $notificationId,
            (string) $device->id
        );

        if (! $notification) {

            return null;
        }

        return [

            'id' => $notification->id,

            'type' => $notification->type,

            'title' => data_get($notification->data, 'title'),

            'message' => data_get($notification->data, 'message'),

            'address' => data_get($notification->data, 'search_address'),

            'latitude' => data_get($notification->data, 'location.lat'),

            'longitude' => data_get($notification->data, 'location.lng'),

            'time' => optional($notification->created_at)->toISOString(),

        ];
    }

    /**
     * --------------------------------------------------------------------------
     * Update Vehicle Information
     * --------------------------------------------------------------------------
     */
    public function updateInformation(
        Device $device,
        array $data
    ): array {

        return $this->vehicleRepository->updateInformation(

            $device,

            $data

        );
    }

    /**
     * --------------------------------------------------------------------------
     * Hapus Kendaraan
     * --------------------------------------------------------------------------
     */
    public function destroy(Device $device): void
    {
        $this->vehicleRepository->destroy($device);
    }

    /**
     * --------------------------------------------------------------------------
     * Update Stop Detection Setting
     * --------------------------------------------------------------------------
     */
    public function updateStopSetting(
        Device $device,
        array $data
    ): object {

        return $this->vehicleRepository->updateStopSetting(

            $device,

            $data

        );
    }

    /**
     * --------------------------------------------------------------------------
     * Update Batas Kecepatan Setting
     * --------------------------------------------------------------------------
     */
    public function updateSpeedSetting(
        Device $device,
        array $data
    ): object {

        return $this->vehicleRepository->updateSpeedSetting(

            $device,

            $data

        );
    }

    /**
     * --------------------------------------------------------------------------
     * Update Notifikasi Geofence Setting
     * --------------------------------------------------------------------------
     */
    public function updateNotificationSetting(
        Device $device,
        array $data
    ): object {

        return $this->vehicleRepository->updateNotificationSetting(

            $device,

            $data

        );
    }

    /**
     * Update Pengaturan Geofence (pengingat "masih di luar area").
     */
    public function updateGeofenceSetting(
        Device $device,
        array $data
    ): object {

        return $this->vehicleRepository->updateGeofenceSetting(

            $device,

            $data

        );
    }

    /**
     * Riwayat Masuk/Keluar Geofence.
     */
    public function geofenceHistory(
        Device $device,
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $event = null
    ): array {

        return [

            'items' => $this->vehicleRepository->geofenceHistory(
                $device,
                $startDate,
                $endDate,
                $event
            ),

            'summary' => $this->vehicleRepository->geofenceHistorySummary(
                $device
            ),

        ];
    }

    /**
     * Latest Location.
     */
    public function latest(
        Device $device
    ): array {

        return $this->vehicleRepository->latest(
            $device
        );
    }
    /**
     * Travel History.
     */
    public function history(
        Device $device,
        ?string $startDate = null,
        ?string $endDate = null
    ): array {

        return $this->vehicleRepository->history(

            $device,

            $startDate,

            $endDate

        );
    }

    /**
     * Playback.
     */
    public function playback(
        Device $device,
        ?string $date
    ): array {

        return $this->vehicleRepository->playback(

            $device,

            $date

        );
    }

    /**
     * Rute mengikuti jalan (map-matching) untuk garis playback/riwayat.
     *
     * Menerima titik yang SAMA PERSIS dengan yang sedang ditampilkan di
     * frontend (bukan query ulang by date) supaya rute yang di-match
     * selalu konsisten dengan titik yang terlihat di daftar riwayat.
     *
     * Titik mentah tetap dipakai sebagai fallback di frontend - kalau
     * map-matching gagal (offline, server demo OSRM down, dsb), garis
     * lurus antar titik asli tetap tampil, tidak pernah kosong/rusak.
     */
    public function route(array $points): array
    {
        $matched = $this->routeMatchingService->match(
            $points
        );

        return [

            'matched' => $matched !== null,

            'path' => $matched ?? [],

        ];
    }

    /**
     * Summary.
     */
    public function summary(
        Device $device
    ): array {

        return $this->vehicleRepository->summary(
            $device
        );
    }

    /**
     * Activity Timeline.
     */
    public function activity(
        Device $device
    ): array {

        return $this->vehicleRepository->activity(
            $device
        );
    }

    /**
     * Stop History.
     */
    public function stop(
        Device $device
    ): array {

        return $this->vehicleRepository->stop(
            $device
        );
    }

    /**
     * Export Riwayat Perjalanan (PDF).
     */
    public function exportTravelHistory(
        Device $device,
        ?string $from,
        ?string $to
    ) {

        return $this->pdfExportService->travelHistory(

            $device,

            $from,

            $to

        );
    }

    /**
     * Export Riwayat Kendaraan Berhenti (PDF).
     */
    public function exportStopHistory(
        Device $device,
        ?string $from,
        ?string $to
    ) {

        return $this->pdfExportService->stopHistory(

            $device,

            $from,

            $to

        );
    }
}
