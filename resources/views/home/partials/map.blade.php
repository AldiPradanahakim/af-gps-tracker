<div class="relative h-full w-full">

    {{-- LEAFLET MAP --}}
    <div id="map" class="h-full w-full"></div>

    {{-- MAP LOADING --}}
    <div
        id="mapLoading"
        class="absolute inset-0 z-[999] flex items-center justify-center bg-white/80 backdrop-blur-sm">

        <div class="flex flex-col items-center gap-4">

            <div
                class="h-12 w-12 animate-spin rounded-full border-4 border-[#2563EB] border-t-transparent">
            </div>

            <div class="text-center">

                <h3 class="text-lg font-semibold text-slate-900">

                    Memuat Peta...

                </h3>

                <p class="mt-1 text-sm text-slate-500">

                    Menyiapkan data kendaraan dan geofence.

                </p>

            </div>

        </div>

    </div>

    {{-- MAP LEGEND --}}
    <div
        class="absolute bottom-6 left-6 z-[500] rounded-2xl bg-white p-5 shadow-xl">

        <h3 class="mb-4 text-sm font-bold text-slate-900">

            Keterangan

        </h3>

        <div class="space-y-3">

            <div class="flex items-center gap-3">

                <span
                    class="h-3 w-3 rounded-full bg-green-500">
                </span>

                <span class="text-sm text-slate-700">

                    Kendaraan Online

                </span>

            </div>

            <div class="flex items-center gap-3">

                <span
                    class="h-3 w-3 rounded-full bg-red-500">
                </span>

                <span class="text-sm text-slate-700">

                    Kendaraan Offline

                </span>

            </div>

            <div class="flex items-center gap-3">

                <span
                    class="h-3 w-3 rounded-full bg-blue-500">
                </span>

                <span class="text-sm text-slate-700">

                    Geofence Radius

                </span>

            </div>

            <div class="flex items-center gap-3">

                <span
                    class="h-3 w-3 rounded-full bg-purple-500">
                </span>

                <span class="text-sm text-slate-700">

                    Geofence Administratif

                </span>

            </div>

        </div>

    </div>

    {{-- MAP SCALE --}}
    <div
        class="absolute bottom-6 right-6 z-[500] rounded-xl bg-white px-4 py-2 shadow-lg">

        <p class="text-xs font-medium text-slate-600">

            GPS Tracker Monitoring

        </p>

    </div>

</div>

<script>

window.Home = {

    vehicles : @json($vehicles),

    geofences : @json($geofences),

    notifications : @json($notifications),

};

</script>