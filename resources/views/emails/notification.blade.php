@component('mail::message')
# {{ $headerEmoji }} {{ $title }}

{{ $message }}

@component('mail::table')
| | |
|:---|:---|
| **Kendaraan** | {{ $vehicleName ?? '-' }} |
| **Plat Nomor** | {{ $plateNumber ?? '-' }} |
@if($geofenceName)
| **Geofence** | {{ $geofenceName }} |
@endif
@if($durationMinutes !== null)
| **Durasi Berhenti** | {{ $durationMinutes }} menit |
@endif
@if($searchAddress)
| **Lokasi** | {{ $searchAddress }} |
@endif
@if($latitude !== null && $longitude !== null)
| **Koordinat** | {{ $latitude }}, {{ $longitude }} |
@endif
| **Waktu** | {{ $formattedTime }} |
@endcomponent

@if($trackingLink)
@component('mail::button', ['url' => $trackingLink])
Lihat Lokasi di Peta
@endcomponent
@endif

Notifikasi ini dikirim otomatis oleh {{ config('app.name') }} berdasarkan aktivitas kendaraan Anda.

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent
