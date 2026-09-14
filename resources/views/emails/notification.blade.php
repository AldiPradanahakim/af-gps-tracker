@component('mail::message')
# {{ $headerEmoji }} {{ $title }}

{{ $message }}

@component('mail::table')
| | |
|:---|:---|
| **Kendaraan** | {{ $vehicleName ?? '-' }} |
| **Plat Nomor** | {{ $plateNumber ?? '-' }} |
@if($geofenceName)
| **Geofence** | {{ $geofenceName }}{{ $geofenceTypeLabel ? ' (' . $geofenceTypeLabel . ')' : '' }} |
@endif
@if($minutesOutside)
| **Sudah di Luar Area** | {{ $minutesOutside }} menit |
@endif
@if($durationMinutes !== null)
| **Durasi Berhenti** | {{ $durationMinutes }} menit |
@endif
| **Alamat** | {{ $searchAddress ?: 'Alamat tidak tersedia' }} |
@if($latitude !== null && $longitude !== null)
| **Koordinat** | {{ $latitude }}, {{ $longitude }} |
@endif
| **Waktu** | {{ $formattedTime }} |
@endcomponent

@if($trackingLink)
@component('mail::button', ['url' => $trackingLink])
Lihat Lokasi di Peta
@endcomponent

@if($mapsLink)
Buka di Google Maps: [{{ $latitude }}, {{ $longitude }}]({{ $mapsLink }})
@endif
@endif

Notifikasi ini dikirim otomatis oleh {{ config('app.name') }} berdasarkan aktivitas kendaraan Anda.

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent
