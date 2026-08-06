@extends('layouts.app')

@section('title', $device->vehicle->vehicle_name ?? 'Detail Kendaraan')

@section('content')

<div
    id="vehicleDetailPage"
    class="flex h-screen overflow-hidden bg-slate-50"
>

    @include('vehicles.partials.sidebar')

    <div
        class="flex h-screen flex-1 flex-col overflow-hidden"
    >

        @include('vehicles.partials.topbar')

        <main
            id="vehicleContent"
            class="flex-1 overflow-y-auto"
        >

            <div
                class="w-full  pb-2 "
            >

                @include('vehicles.partials.map')

                <section
                    id="vehicleInformationSection"
                    class="mt-5 px-5"
                >
                    @include('vehicles.partials.sections.information')
                </section>

                <section
                    id="vehicleHomeLocationSection"
                    class="hidden mt-5 px-5 mb-5"
                >
                    @include('vehicles.partials.sections.home-location')
                </section>

                <section
                    id="vehicleGeofenceSection"
                    class="hidden mt-5 px-5 mb-5"
                >
                    @include('vehicles.partials.sections.geofence')
                </section>

                <section
                    id="vehicleHistorySection"
                    class="hidden mt-5 px-5 mb-5"
                >
                    @include('vehicles.partials.sections.history')
                </section>

                <section
                    id="vehicleStopSection"
                    class="hidden mt-5 px-5 mb-5"
                >
                    @include('vehicles.partials.sections.stop')
                </section>

            </div>

        </main>

    </div>

    {{-- ========================================================= --}}
    {{-- Tambah / Hapus Kendaraan --}}
    {{-- ========================================================= --}}

    @include('home.modals.activate-device')

    @include('home.modals.vehicle-information')

    @include('home.modals.profile')

    @include('vehicles.partials.modals.delete-vehicle')

</div>

@endsection


@push('styles')

<style>

/* ==========================================================
   Vehicle Detail
========================================================== */

#vehicleDetailPage{
    background:#F8FAFC;
}

#vehicleContent{
    scroll-behavior:smooth;
}

/* ==========================================================
   Shadow
========================================================== */

.vehicle-card-shadow{
    box-shadow:
        0 8px 20px rgba(15,23,42,.06);
}

.vehicle-panel-shadow{
    box-shadow:
        0 8px 20px rgba(15,23,42,.05);
}

.vehicle-button-shadow{
    box-shadow:
        0 6px 18px rgba(15,23,42,.08);
}

/* ==========================================================
   Radius
========================================================== */

.vehicle-card-radius{
    border-radius:22px;
}

/* ==========================================================
   Map
========================================================== */

.vehicle-map-height{
    height:430px;
}

/* ==========================================================
   Popup
========================================================== */

.vehicle-popup-card{
    min-width:240px;
}

/* ==========================================================
   Scrollbar
========================================================== */

#vehicleContent::-webkit-scrollbar{
    width:8px;
}

#vehicleContent::-webkit-scrollbar-track{
    background:transparent;
}

#vehicleContent::-webkit-scrollbar-thumb{
    background:#CBD5E1;
    border-radius:999px;
}

#vehicleContent::-webkit-scrollbar-thumb:hover{
    background:#94A3B8;
}

</style>

@endpush

@push('scripts')

@include('vehicles.partials.scripts')

@endpush