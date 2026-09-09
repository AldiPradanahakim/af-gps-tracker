<?php

namespace App\Services\Vehicle;

use App\Helpers\AppTime;
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

    /**
     * --------------------------------------------------------------------------
     * Batas Baris PDF
     * --------------------------------------------------------------------------
     *
     * Perangkat GPS bisa menghasilkan ribuan titik per hari. DomPDF
     * menahan SELURUH tabel di memori saat me-layout halaman, sehingga
     * rentang tanggal yang lebar (6.000+ baris) membuat proses export
     * mati diam-diam karena kehabisan memori - pengguna hanya melihat
     * unduhan yang gagal tanpa pesan apa pun.
     *
     * Batasnya tidak dipaku ke satu angka, tapi dihitung dari
     * memory_limit yang benar-benar berlaku di server (lihat
     * maxRows()), sehingga server yang lebih besar otomatis bisa
     * mencetak lebih banyak. Bisa juga dipaksa lewat PDF_MAX_ROWS
     * di .env.
     */

    /**
     * Perkiraan pemakaian memori DomPDF per baris tabel laporan ini,
     * dalam megabyte. Diukur langsung pada laporan Riwayat Perjalanan:
     * 300 baris ~= 108 MB, 600 baris ~= 198 MB, 1000 baris ~= 364 MB.
     */
    private const MEMORY_PER_ROW_MB = 0.34;

    /**
     * Memori yang disisihkan untuk Laravel, koneksi database, dan hasil
     * query sebelum DomPDF mulai bekerja (megabyte).
     */
    private const MEMORY_OVERHEAD_MB = 60;

    /**
     * Porsi memory_limit yang boleh dipakai proses export.
     *
     * Sengaja tidak 100%: biaya per baris hanyalah rata-rata (baris
     * dengan alamat panjang jauh lebih mahal), dan proses lain di
     * request yang sama juga butuh ruang. Pengukuran tanpa margin ini
     * menyentuh 488 MB dari limit 512 MB - terlalu dekat dengan batas
     * untuk dipakai di produksi.
     */
    private const MEMORY_SAFETY_RATIO = 0.65;

    /**
     * Batas bawah & atas jumlah baris, apa pun hasil hitungannya.
     */
    private const MIN_PDF_ROWS = 300;

    private const MAX_PDF_ROWS_CEILING = 5000;

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

        $totalRows = count($histories);

        $histories = array_slice($histories, 0, $this->maxRows());

        $histories = array_map(function (array $history) {

            $history['received_at_label'] = $this->formatTimestamp(
                $history['received_at'] ?? null
            );

            return $history;

        }, $histories);

        $pdf = Pdf::loadView('exports.travel-history-pdf', [

            'totalRows' => $totalRows,

            'maxRows' => $this->maxRows(),


            'device' => $device,

            'vehicle' => $device->vehicle,

            'histories' => $histories,

            'startDate' => $startDate,

            'endDate' => $endDate,

            'generatedAt' => AppTime::now(),

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

        $totalRows = count($stops);

        $stops = array_slice($stops, 0, $this->maxRows());

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

            'totalRows' => $totalRows,

            'maxRows' => $this->maxRows(),


            'device' => $device,

            'vehicle' => $device->vehicle,

            'stops' => $stops,

            'startDate' => $startDate,

            'endDate' => $endDate,

            'generatedAt' => AppTime::now(),

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

        $timezone = AppTime::displayTimezone();

        $endDate = $to
            ? Carbon::parse($to, $timezone)
            : AppTime::now();

        $startDate = $from
            ? Carbon::parse($from, $timezone)
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
     * Format timestamp dari database -> string "dd/mm/Y H:i WIB".
     *
     * Timestamp yang masuk ke sini berasal dari Carbon::toDateTimeString()
     * di repository, jadi zona waktunya adalah zona PENYIMPANAN
     * (config('app.timezone')) - bukan selalu UTC. Sebelumnya string itu
     * selalu diparse sebagai UTC lalu digeser ke WIB, sehingga sejak
     * APP_TIMEZONE diubah ke Asia/Jakarta seluruh jam (dan kadang
     * tanggalnya) di export PDF maju 7 jam.
     */
    protected function formatTimestamp(?string $timestamp): string
    {
        if (! $timestamp) {

            return '-';
        }

        return AppTime::format(
            Carbon::parse($timestamp, AppTime::storageTimezone()),
            'd/m/Y H:i'
        );
    }

    /**
     * Berapa baris yang aman dirender pada server ini.
     *
     * Dihitung dari memory_limit PHP yang sedang berlaku dikurangi
     * overhead, dibagi biaya memori per baris. memory_limit "-1"
     * (tanpa batas) memakai plafon MAX_PDF_ROWS_CEILING supaya laporan
     * tidak menjadi ribuan halaman yang tak terbaca.
     */
    protected function maxRows(): int
    {
        $configured = (int) config('pdf.max_rows', 0);

        if ($configured > 0) {

            return min($configured, self::MAX_PDF_ROWS_CEILING);
        }

        $memoryLimitMb = $this->memoryLimitInMegabytes();

        if ($memoryLimitMb === null) {

            return self::MAX_PDF_ROWS_CEILING;
        }

        $budgetMb = ($memoryLimitMb * self::MEMORY_SAFETY_RATIO)
            - self::MEMORY_OVERHEAD_MB;

        $rows = (int) floor($budgetMb / self::MEMORY_PER_ROW_MB);

        return max(
            self::MIN_PDF_ROWS,
            min($rows, self::MAX_PDF_ROWS_CEILING)
        );
    }

    /**
     * memory_limit PHP dalam megabyte, atau null kalau tak terbatas.
     */
    protected function memoryLimitInMegabytes(): ?int
    {
        $limit = trim((string) ini_get('memory_limit'));

        if ($limit === '' || $limit === '-1') {

            return null;
        }

        $unit = strtolower(substr($limit, -1));

        $value = (int) $limit;

        return match ($unit) {
            'g' => $value * 1024,
            'm' => $value,
            'k' => (int) floor($value / 1024),
            default => (int) floor($value / 1048576),
        };
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
