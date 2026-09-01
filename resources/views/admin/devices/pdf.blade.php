<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Perangkat GPS ({{ $date }})</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
        }
        h2 {
            text-align: center;
            margin-bottom: 5px;
        }
        p.subtitle {
            text-align: center;
            color: #666;
            margin-top: 0;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
        }
        .password {
            font-family: monospace;
            letter-spacing: 2px;
            font-weight: bold;
        }
        .note {
            font-size: 12px;
            color: #d9534f;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h2>Daftar Kredensial Perangkat GPS</h2>
    <p class="subtitle">Digenerate pada: {{ $date }}</p>

    <table>
        <thead>
            <tr>
                <th width="10%">No</th>
                <th width="45%">Device ID</th>
                <th width="45%">Password (Kata Sandi)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($devices as $index => $device)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td><strong>{{ $device['device_id'] }}</strong></td>
                <td class="password">{{ $device['device_password'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p class="note">
        <strong>PENTING:</strong> Simpan dokumen ini dengan baik. Kata sandi di atas hanya ditampilkan sekali dan tidak dapat dilihat lagi melalui sistem.
    </p>
</body>
</html>
