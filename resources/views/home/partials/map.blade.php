<div class="relative h-full w-full overflow-hidden">

    {{-- =========================================================================
    | LEAFLET MAP
    ========================================================================= --}}
    <div
        id="map"
        class="absolute inset-0">
    </div>

{{-- =========================================================================
| MAP LAYER
========================================================================= --}}

<div class="absolute bottom-6 right-4 z-[900]">

    <button
    id="layerButton"
    type="button"
    class="relative h-20 w-20 overflow-hidden rounded-2xl shadow-lg">

    <img
        src="https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/15/13112/26928"
        alt=""
        class="absolute inset-0 h-full w-full object-cover">

    <div
        class="absolute inset-0 bg-black/20">
    </div>

    <div
        class="absolute bottom-1 left-0 right-0 flex items-center justify-center gap-1 px-1 text-[11px] font-semibold text-white">

        <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 shrink-0"
            fill="currentColor"
            viewBox="0 0 24 24">

            <path d="M12 2 1 7l11 5 9-4.09V17h2V7L12 2Zm0 12L1 9v2l11 5 11-5V9l-11 5Zm0 5L1 14v2l11 5 11-5v-2l-11 5Z"/>

        </svg>

        <span class="truncate">

            Lapisan

        </span>

    </div>

</button>

    <div
        id="layerDropdown"
        class="absolute bottom-0 right-[90px] hidden w-48 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl">

        <button
            class="layer-option flex w-full items-center gap-3 px-4 py-3 hover:bg-slate-50"
            data-layer="default">

            🗺️ Default

        </button>

        <button
            class="layer-option flex w-full items-center gap-3 border-t px-4 py-3 hover:bg-slate-50"
            data-layer="satellite">

            🛰️ Satelit

        </button>

    </div>

</div>

    {{-- =========================================================================
    | MAP LOADING
    ========================================================================= --}}
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

    {{-- =========================================================================
    | MAP LEGEND
    ========================================================================= --}}
    <div
        class="absolute bottom-6 left-6 z-[800] rounded-xl bg-white/95 p-4 shadow-xl backdrop-blur">

        <h3 class="mb-3 text-sm font-semibold text-slate-800">

            Keterangan

        </h3>

        <div class="space-y-2">

            <div class="flex items-center gap-3">

                <span class="h-3 w-3 rounded-full bg-green-500"></span>

                <span class="text-sm text-slate-700">

                    Kendaraan Terhubung

                </span>

            </div>

            <div class="flex items-center gap-3">

                <span class="h-3 w-3 rounded-full bg-red-500"></span>

                <span class="text-sm text-slate-700">

                    Kendaraan Terputus

                </span>

            </div>

            <div class="flex items-center gap-3">

                <span class="h-3 w-3 rounded-full bg-blue-500"></span>

                <span class="text-sm text-slate-700">

                    Geofence Radius

                </span>

            </div>

            <div class="flex items-center gap-3">

                <span class="h-3 w-3 rounded-full bg-purple-500"></span>

                <span class="text-sm text-slate-700">

                    Geofence Administratif

                </span>

            </div>

        </div>

    </div>

    {{-- =========================================================================
    | MAP FOOTER
    ========================================================================= --}}

</div>

<script>

    /*
    |--------------------------------------------------------------------------
    | Initial Payload From Laravel
    |--------------------------------------------------------------------------
    |
    | window.Home hanya digunakan sebagai payload awal.
    | Setelah app.blade.php dijalankan,
    | seluruh frontend menggunakan GPSTracker.
    |
    */

    window.Home = {

        userId: @json($user->id ?? null),

        vehicles: @json($vehicles ?? []),

        geofences: @json($geofences ?? []),

        notifications: @json($notifications ?? []),

    };

</script>