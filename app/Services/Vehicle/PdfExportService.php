<?php

namespace App\Services\Vehicle;

use App\Models\Device;
use App\Repositories\Vehicle\VehicleRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as PdfDocument;
use Illuminate\Support\Carbon;

class PdfExportService
{
    /**
     * Rentang tanggal default (hari) kalau "from"/"to" tidak dikirim,
     * supaya export tanpa filter tidak pernah menarik seluruh riwayat.
     */
    private const DEFAULT_RANGE_DAYS = 30;

    public function __construct(
        protected VehicleRepository $vehicleRepository
    ) {}

    /**
     * --------------------------------------------------------------------------
     * Export Riwayat Perjalanan (PDF)
     * --------------------------------------------------------------------------
     */
    public function travelHistory(
        Device $device,
        ?string $from,
        ?string $to
    ): PdfDocument {

        [$startDate, $endDate] = $this->resolveRange($from, $to);

        $device->loadMissing('vehicle');

        $histories = $this->vehicleRepository->history(
            $device,
            $startDate,
            $endDate
        );

        $histories = array_map(function (array $history) {

            $history['received_at_label'] = $this->formatTimestamp(
                $history['received_at'] ?? null
            );

            return $history;

        }, $histories);

        $pdf = Pdf::loadView('exports.travel-history-pdf', [

            'device' => $device,

            'vehicle' => $device->vehicle,

            'histories' => $histories,

            'startDate' => $startDate,

            'endDate' => $endDate,

            'generatedAt' => now('Asia/Jakarta'),

        ]);

        return $pdf->setPaper('a4', 'portrait');
    }

    /**
     * --------------------------------------------------------------------------
     * Export Riwayat Kendaraan Berhenti (PDF)
     * --------------------------------------------------------------------------
     */
    public function stopHistory(
        Device $device,
        ?string $from,
        ?string $to
    ): PdfDocument {

        [$startDate, $endDate] = $this->resolveRange($from, $to);

        $device->loadMissing('vehicle');

        $stops = $this->vehicleRepository->stopHistoryRange(
            $device,
            $startDate,
            $endDate
        );

        $stops = array_map(function (array $stop) {

            $stop['duration_label'] = $this->formatDuration(
                (int) ($stop['duration_seconds'] ?? 0)
            );

            $stop['started_at_label'] = $this->formatTimestamp(
                $stop['started_at'] ?? null
            );

            $stop['ended_at_label'] = $this->formatTimestamp(
                $stop['ended_at'] ?? null
            );

            return $stop;

        }, $stops);

        $pdf = Pdf::loadView('exports.stop-history-pdf', [

            'device' => $device,

            'vehicle' => $device->vehicle,

            'stops' => $stops,

            'startDate' => $startDate,

            'endDate' => $endDate,

            'generatedAt' => now('Asia/Jakarta'),

        ]);

        return $pdf->setPaper('a4', 'portrait');
    }

    /**
     * --------------------------------------------------------------------------
     * Resolve Rentang Tanggal
     * --------------------------------------------------------------------------
     * Default ke DEFAULT_RANGE_DAYS hari terakhir (waktu WIB) kalau
     * "from"/"to" tidak dikirim. Menukar urutan kalau ternyata terbalik,
     * supaya query di repository tetap konsisten (start <= end).
     * --------------------------------------------------------------------------
     */
    protected function resolveRange(
        ?string $from,
        ?string $to
    ): array {

        $endDate = $to
            ? Carbon::parse($to, 'Asia/Jakarta')
            : now('Asia/Jakarta');

        $startDate = $from
            ? Carbon::parse($from, 'Asia/Jakarta')
            : $endDate->copy()->subDays(self::DEFAULT_RANGE_DAYS - 1);

        if ($startDate->greaterThan($endDate)) {

            [$startDate, $endDate] = [$endDate, $startDate];
        }

        return [

            $startDate->toDateString(),

            $endDate->toDateString(),

        ];
    }

    /**
     * Format timestamp UTC (tersimpan di database) -> string "dd/mm/Y H:i WIB"
     * supaya laporan mudah dibaca oleh pemilik kendaraan di Indonesia.
     */
    protected function formatTimestamp(?string $timestamp): string
    {
        if (! $timestamp) {

            return '-';
        }

        return Carbon::parse($timestamp, 'UTC')
            ->timezone('Asia/Jakarta')
            ->format('d/m/Y H:i') . ' WIB';
    }

    /**
     * Format detik -> "Xj Ym" / "Ym" (sama seperti VehicleRepository).
     */
    protected function formatDuration(int $seconds): string
    {
        if ($seconds <= 0) {

            return '-';
        }

        $hours = intdiv($seconds, 3600);

        $minutes = intdiv($seconds % 3600, 60);

        if ($hours > 0) {

            return "{$hours}j {$minutes}m";
        }

        return "{$minutes}m";
    }
}
