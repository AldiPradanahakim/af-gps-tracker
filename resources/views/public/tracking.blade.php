@extends('layouts.app')

@section('title', ($vehicle?->vehicle_name ?? 'Kendaraan') . ' - Lokasi Terakhir')

@section('meta_description', 'Lokasi terakhir kendaraan.')

@section('content')

<div class="relative h-screen w-screen overflow-hidden bg-[#EDEFF3]">

    <div id="trackingMap" class="absolute inset-0"></div>

    @if(is_null($latitude) || is_null($longitude))
        <div class="absolute inset-0 z-[500] flex items-center justify-center bg-white/95">
            <div class="flex flex-col items-center gap-3 px-8 text-center">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                    <i class="fa-solid fa-location-crosshairs text-[22px]"></i>
                </div>
                <p class="text-sm font-semibold text-slate-700">
                    Lokasi kendaraan belum tersedia.
                </p>
            </div>
        </div>
    @endif

    {{-- ========================================================= --}}
    {{-- Badge Nama Aplikasi --}}
    {{-- ========================================================= --}}

    <div class="pointer-events-none absolute left-4 top-4 z-[600]">
        <span class="pointer-events-auto rounded-full bg-white/95 px-3 py-1.5 text-[11px] font-bold text-[#2563EB] shadow-md backdrop-blur">
            {{ config('app.name') }}
        </span>
    </div>

</div>

@endsection

@push('scripts')
<script>

document.addEventListener('DOMContentLoaded', function () {

    function escapeHtml(value) {
        if (value === null || value === undefined) {
            return '';
        }
        return String(value).replace(/[&<>"']/g, function (char) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[char];
        });
    }

    var latitude = @json($latitude);
    var longitude = @json($longitude);
    var vehicleName = @json($vehicle?->vehicle_name ?? 'Kendaraan');
    var plateNumber = @json($vehicle?->plate_number ?? '-');
    var markerIcon = @json($vehicle?->marker_icon ?? 'car');
    var markerColor = @json($vehicle?->marker_color ?? 'green');
    var address = @json($address);
    var updatedAt = @json($receivedAt ? \App\Services\Notification\NotificationPresenter::formatDateTime($receivedAt) : null);
    var detailUrl = @json(route('vehicles.show', $device) . '?event=' . $notification->id);

    if (latitude === null || longitude === null || typeof L === 'undefined') {
        return;
    }

    var map = L.map('trackingMap', {
        zoomControl: true,
        attributionControl: false,
    }).setView([latitude, longitude], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
    }).addTo(map);

    /*
    |--------------------------------------------------------------------------
    | Marker sama persis dengan yang dipakai di peta Home (ikon +
    | warna kendaraan), supaya konsisten di seluruh aplikasi.
    |--------------------------------------------------------------------------
    */

    var colors = {
        green: '#22c55e', blue: '#2563eb', red: '#ef4444', orange: '#f97316',
        yellow: '#eab308', purple: '#9333ea', black: '#111827', gray: '#6b7280',
    };

    var icons = {
        motorcycle: 'fa-solid fa-motorcycle', car: 'fa-solid fa-car',
        pickup: 'fa-solid fa-truck-pickup', truck: 'fa-solid fa-truck',
        bus: 'fa-solid fa-bus', ambulance: 'fa-solid fa-truck-medical',
        police: 'fa-solid fa-shield-halved', bicycle: 'fa-solid fa-bicycle',
        van: 'fa-solid fa-van-shuttle', taxi: 'fa-solid fa-taxi',
    };

    var vehicleIcon = L.divIcon({
        className: '',
        iconSize: [60, 60],
        iconAnchor: [30, 30],
        popupAnchor: [0, -30],
        html: '<div style="width:60px;height:60px;display:flex;justify-content:center;align-items:center;">'
            + '<div style="width:50px;height:50px;border-radius:50%;background:' + (colors[markerColor] ?? colors.green) + ';border:4px solid white;box-shadow:0 6px 18px rgba(0,0,0,.35);display:flex;justify-content:center;align-items:center;">'
            + '<i class="' + (icons[markerIcon] ?? icons.car) + '" style="font-size:26px;color:white;"></i>'
            + '</div>'
            + '</div>',
    });

    var popupHtml = ''
        + '<div style="min-width:220px">'
        + '<div style="font-weight:700;font-size:13px;margin-bottom:2px;">' + escapeHtml(vehicleName) + '</div>'
        + '<div style="font-size:12px;color:#64748b;margin-bottom:6px;">' + escapeHtml(plateNumber) + '</div>'
        + (address ? '<div style="font-size:12px;color:#334155;margin-bottom:8px;">' + escapeHtml(address) + '</div>' : '')
        + (updatedAt ? '<div style="font-size:11px;color:#94a3b8;margin-bottom:10px;">Update: ' + escapeHtml(updatedAt) + '</div>' : '')
        + '<div style="display:flex;gap:6px;">'
        + '<a href="https://maps.google.com/?q=' + latitude + ',' + longitude + '" target="_blank" rel="noopener" style="flex:1;text-align:center;padding:6px 8px;border:1px solid #e2e8f0;border-radius:8px;font-size:11px;font-weight:600;color:#334155;text-decoration:none;">Google Maps</a>'
        + '<a href="' + detailUrl + '" style="flex:1;text-align:center;padding:6px 8px;background:#2563EB;border-radius:8px;font-size:11px;font-weight:600;color:#fff;text-decoration:none;">Detail Lengkap</a>'
        + '</div>'
        + '</div>';

    L.marker([latitude, longitude], { icon: vehicleIcon })
        .addTo(map)
        .bindPopup(popupHtml)
        .openPopup();

});

</script>
@endpush
