<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Riwayat Berhenti</title>
    <style>
        @page {
            margin: 28px 32px;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        /* ==================================================== */
        /* Header                                                */
        /* ==================================================== */

        .header-table {
            width: 100%;
            margin-bottom: 14px;
        }

        .header-table td {
            vertical-align: top;
        }

        .app-name {
            font-size: 16px;
            font-weight: bold;
            color: #c2410c;
        }

        .doc-title {
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 2px;
        }

        .doc-subtitle {
            font-size: 9px;
            color: #64748b;
            margin-top: 2px;
        }

        .header-right {
            text-align: right;
            font-size: 9px;
            color: #64748b;
        }

        hr.divider {
            border: none;
            border-top: 1px solid #cbd5e1;
            margin: 10px 0 14px 0;
        }

        /* ==================================================== */
        /* Info Kendaraan                                        */
        /* ==================================================== */

        .info-table {
            width: 100%;
            margin-bottom: 16px;
            border: 1px solid #e2e8f0;
        }

        .info-table td {
            padding: 6px 10px;
            font-size: 9.5px;
            border-bottom: 1px solid #eef2f7;
        }

        .info-label {
            color: #64748b;
            width: 110px;
        }

        .info-value {
            color: #0f172a;
            font-weight: bold;
        }

        /* ==================================================== */
        /* Data Table                                            */
        /* ==================================================== */

        .data-table {
            width: 100%;
            margin-top: 4px;
        }

        .data-table thead th {
            background-color: #c2410c;
            color: #ffffff;
            font-size: 9px;
            text-transform: uppercase;
            padding: 6px 6px;
            text-align: left;
            border: 1px solid #c2410c;
        }

        .data-table tbody td {
            padding: 5px 6px;
            font-size: 9px;
            border: 1px solid #e2e8f0;
            color: #1e293b;
        }

        .data-table tbody tr.alt {
            background-color: #f8fafc;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .badge-ongoing {
            color: #b45309;
            font-weight: bold;
        }

        .badge-done {
            color: #047857;
            font-weight: bold;
        }

        .empty-box {
            border: 1px solid #e2e8f0;
            padding: 24px;
            text-align: center;
            color: #94a3b8;
            font-size: 10px;
            margin-top: 10px;
        }

        .footer-note {
            margin-top: 16px;
            font-size: 8px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>

<body>

    {{-- ==================================================== --}}
    {{-- Header --}}
    {{-- ==================================================== --}}

    <table class="header-table">
        <tr>
            <td>
                <div class="app-name">{{ config('app.name', 'AF GPS TRACKER') }}</div>
                <div class="doc-title">Laporan Riwayat Kendaraan Berhenti</div>
                <div class="doc-subtitle">
                    Periode {{ \Illuminate\Support\Carbon::parse($startDate)->format('d/m/Y') }}
                    &ndash; {{ \Illuminate\Support\Carbon::parse($endDate)->format('d/m/Y') }}
                </div>
            </td>
            <td class="header-right">
                Dicetak: {{ \App\Helpers\AppTime::format($generatedAt, 'd/m/Y H:i') }}<br>
                Total Stop: {{ count($stops) }}
            </td>
        </tr>
    </table>

    <hr class="divider">

    {{-- ==================================================== --}}
    {{-- Info Kendaraan --}}
    {{-- ==================================================== --}}

    <table class="info-table">
        <tr>
            <td class="info-label">Nama Kendaraan</td>
            <td class="info-value">{{ $vehicle->vehicle_name ?? '-' }}</td>
            <td class="info-label">Nomor Polisi</td>
            <td class="info-value">{{ $vehicle->plate_number ?? '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">Jenis Kendaraan</td>
            <td class="info-value">{{ ucfirst($vehicle->vehicle_type ?? '-') }}</td>
            <td class="info-label">ID Perangkat</td>
            <td class="info-value">{{ $device->device_id ?? '-' }}</td>
        </tr>
    </table>

    {{-- ==================================================== --}}
    {{-- Tabel Riwayat Berhenti --}}
    {{-- ==================================================== --}}

    @if(count($stops) === 0)

        <div class="empty-box">
            Tidak ada data kendaraan berhenti pada rentang tanggal yang dipilih.
        </div>

    @else

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 17%;">Mulai</th>
                    <th style="width: 17%;">Selesai</th>
                    <th style="width: 10%;">Durasi</th>
                    <th style="width: 33%;">Alamat</th>
                    <th style="width: 18%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stops as $index => $stop)
                    <tr @class(['alt' => $index % 2 === 1])>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $stop['started_at_label'] ?? '-' }}</td>
                        <td>{{ $stop['ended_at_label'] ?? '-' }}</td>
                        <td>{{ $stop['duration_label'] ?? '-' }}</td>
                        <td>{{ $stop['address'] ?? '-' }}</td>
                        <td class="text-center">
                            @if(empty($stop['ended_at']))
                                <span class="badge-ongoing">Sedang Berhenti</span>
                            @else
                                <span class="badge-done">Selesai</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @endif

    @if(($totalRows ?? 0) > ($maxRows ?? 0))
        <div class="footer-note">
            Ditampilkan {{ number_format($maxRows) }} data terbaru dari total
            {{ number_format($totalRows) }} data pada rentang ini. Persempit rentang
            tanggal untuk melihat sisanya.
        </div>
    @endif

    <div class="footer-note">
        Dokumen ini dibuat otomatis oleh {{ config('app.name', 'AF GPS TRACKER') }} dan tidak memerlukan tanda tangan basah.
    </div>

</body>

</html>
