<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Riwayat Perjalanan</title>
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
            color: #1d4ed8;
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
            background-color: #1d4ed8;
            color: #ffffff;
            font-size: 9px;
            text-transform: uppercase;
            padding: 6px 6px;
            text-align: left;
            border: 1px solid #1d4ed8;
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

        .badge-moving {
            color: #1d4ed8;
            font-weight: bold;
        }

        .badge-stopped {
            color: #c2410c;
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
                <div class="app-name">{{ config('app.name', 'Trackio') }}</div>
                <div class="doc-title">Laporan Riwayat Perjalanan</div>
                <div class="doc-subtitle">
                    Periode {{ \Illuminate\Support\Carbon::parse($startDate)->format('d/m/Y') }}
                    &ndash; {{ \Illuminate\Support\Carbon::parse($endDate)->format('d/m/Y') }}
                </div>
            </td>
            <td class="header-right">
                Dicetak: {{ $generatedAt->format('d/m/Y H:i') }} WIB<br>
                Total Titik: {{ count($histories) }}
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
    {{-- Tabel Riwayat --}}
    {{-- ==================================================== --}}

    @if(count($histories) === 0)

        <div class="empty-box">
            Tidak ada data riwayat perjalanan pada rentang tanggal yang dipilih.
        </div>

    @else

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 6%;">No</th>
                    <th style="width: 18%;">Waktu</th>
                    <th style="width: 36%;">Alamat</th>
                    <th style="width: 13%;">Koordinat</th>
                    <th style="width: 12%;">Kecepatan</th>
                    <th style="width: 15%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($histories as $index => $history)
                    <tr @class(['alt' => $index % 2 === 1])>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $history['received_at_label'] ?? '-' }}</td>
                        <td>{{ $history['address'] ?? '-' }}</td>
                        <td>
                            @if($history['lat'] !== null && $history['lng'] !== null)
                                {{ number_format($history['lat'], 6) }},
                                {{ number_format($history['lng'], 6) }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right">
                            {{ number_format((float) ($history['speed'] ?? 0), 0) }} km/jam
                        </td>
                        <td class="text-center">
                            @if((float) ($history['speed'] ?? 0) > 0)
                                <span class="badge-moving">Bergerak</span>
                            @else
                                <span class="badge-stopped">Berhenti</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @endif

    <div class="footer-note">
        Dokumen ini dibuat otomatis oleh {{ config('app.name', 'Trackio') }} dan tidak memerlukan tanda tangan basah.
    </div>

</body>

</html>
