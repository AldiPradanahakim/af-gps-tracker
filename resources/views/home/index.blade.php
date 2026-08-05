@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="h-screen overflow-hidden bg-[#F8FAFC]">

    <div class="flex h-full">

        {{-- SIDEBAR --}}
        @include('home.partials.sidebar')

        {{-- CONTENT --}}
        <div class="relative z-10 flex flex-1 flex-col">

            {{-- TOPBAR --}}
            @include('home.partials.topbar')

            {{-- MAP --}}
            <main class="relative flex-1 min-h-0 overflow-hidden">

                @include('home.partials.map')

            </main>

        </div>

    </div>

</div>

{{-- MODALS --}}

@include('home.modals.home-location')

@include('home.modals.add-geofence')

@include('home.modals.delete-geofence')

@include('home.modals.activate-device')

@include('home.modals.vehicle-information')

@include('home.modals.profile')

@endsection


@push('styles')

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css"/>

<style>

html,
body{
    height:100%;
}

#map{
    position:absolute;
    inset:0;
    width:100%;
    height:100%;
}

.leaflet-container{
    width:100%;
    height:100%;
}

.leaflet-control-attribution{
    display:none;
}

.leaflet-popup-content-wrapper{
    border-radius:14px;
}
/* ===========================================
| Layer Button
=========================================== */

.gps-layer-control{
    box-shadow:none !important;
    background:transparent !important;
    border:none !important;
}

.gps-layer-control .leaflet-control-layers-toggle{

    width:70px !important;

    height:70px !important;

    background-size:cover !important;

    border-radius:18px;

    background-image:url('/images/map-layer.png');

}

.gps-layer-control.leaflet-control-layers-expanded{

    background:white;

    border-radius:18px;

    padding:10px;

    box-shadow:0 15px 40px rgba(0,0,0,.18);

}

/* posisi */

.leaflet-bottom.leaflet-right{

    display:flex;

    flex-direction:column;

    align-items:flex-end;

}

.leaflet-control-zoom{

    margin-bottom:10px !important;

}

.gps-layer-control{

    margin-bottom:18px !important;

}

.leaflet-bottom.leaflet-right{

    bottom:120px !important;

    right:16px !important;

}

.leaflet-control-zoom{

    margin:0 !important;

}


</style>

@endpush

@push('scripts')

<script>

    window.GPSHomeLocations = @json($devices);

</script>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>

@include('home.partials.scripts')

@endpush