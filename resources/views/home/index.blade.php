@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="h-[calc(100vh-72px)] overflow-hidden bg-[#F8FAFC]">

    <div class="flex h-full">

        {{-- SIDEBAR --}}
        @include('home.partials.sidebar')

        {{-- CONTENT --}}
        <div class="flex flex-1 flex-col">

            {{-- TOPBAR --}}
            @include('home.partials.topbar')

            {{-- MAP --}}
            <main class="relative flex-1 overflow-hidden">

                @include('home.partials.map')

            </main>

        </div>

    </div>

</div>

{{-- MODALS --}}
@include('home.modals.add-geofence')

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
    width:100%;
    height:100%;
}

.leaflet-control-attribution{
    display:none;
}

.leaflet-popup-content-wrapper{
    border-radius:14px;
}

</style>

@endpush


@push('scripts')

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>

@include('home.partials.scripts')

@endpush