<div
    id="vehicleMapContainer"
    class="relative overflow-hidden  border border-slate-200 bg-white vehicle-card-shadow"
>

    {{-- MAP --}}
    <div
        id="vehicleMap"
        class="h-[620px] w-full"
    ></div>

    {{-- ===================================================== --}}
    {{-- Event Focus Banner (?event= dari link notification) --}}
    {{-- ===================================================== --}}

    <div
        id="vehicleEventFocusBanner"
        class="absolute left-5 right-5 top-5 z-[650] hidden"
    >

        <div
            class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-lg"
        >

            <div class="min-w-0">

                <p
                    id="vehicleEventFocusTitle"
                    class="truncate text-sm font-semibold text-slate-900"
                ></p>

                <p
                    id="vehicleEventFocusTime"
                    class="text-xs text-slate-500"
                ></p>

            </div>

            <button
                id="vehicleEventFocusReset"
                type="button"
                class="shrink-0 rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-200"
            >
                Lokasi Terkini
            </button>

        </div>

    </div>

    {{-- ===================================================== --}}
    {{-- Floating Update --}}
    {{-- ===================================================== --}}

    <div
        class="absolute bottom-5 left-5 z-[600]"
    >

        <div
            class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-lg"
        >

            <span
                class="h-3 w-3 rounded-full bg-emerald-500"
            ></span>

            <div>

                <p
                    class="text-xs font-semibold text-slate-800"
                >
                    Terakhir diperbarui
                </p>

                <p
                    id="vehicleMapLastUpdate"
                    class="text-[11px] text-slate-500"
                >
                    -
                </p>

            </div>

        </div>

    </div>

    {{-- ===================================================== --}}
    {{-- Playback --}}
    {{-- ===================================================== --}}

    <div
        id="vehiclePlaybackToolbar"
        class="absolute bottom-5 left-1/2 hidden -translate-x-1/2 z-[600]"
    >

        <div
            class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-white p-2 shadow-xl"
        >

            <button
                id="playbackPlay"
                class="flex h-10 w-10 items-center justify-center rounded-xl hover:bg-slate-100"
            >
                <i class="fa-solid fa-play"></i>
            </button>

            <button
                id="playbackPause"
                class="flex h-10 w-10 items-center justify-center rounded-xl hover:bg-slate-100"
            >
                <i class="fa-solid fa-pause"></i>
            </button>

            <button
                id="playbackStop"
                class="flex h-10 w-10 items-center justify-center rounded-xl hover:bg-slate-100"
            >
                <i class="fa-solid fa-stop"></i>
            </button>

        </div>

    </div>

    {{-- ===================================================== --}}
    {{-- Floating Zoom Toolbar --}}
    {{-- ===================================================== --}}

    <div
        class="absolute bottom-28 right-4 z-[700] flex flex-col overflow-hidden rounded-lg border border-slate-200 shadow-lg"
    >

        <button
            id="vehicleZoomInButton"
            type="button"
            class="flex h-[34px] w-[34px] items-center justify-center bg-white text-slate-700 text-xl font-light leading-none transition hover:bg-slate-50 border-b border-slate-200"
        >+</button>

        <button
            id="vehicleZoomOutButton"
            type="button"
            class="flex h-[34px] w-[34px] items-center justify-center bg-white text-slate-700 text-xl font-light leading-none transition hover:bg-slate-50"
        >−</button>

    </div>

    {{-- ===================================================== --}}
    {{-- Map Layer --}}
    {{-- ===================================================== --}}

    <div class="absolute bottom-6 right-4 z-[700]">

        <button
            id="vehicleLayerButton"
            type="button"
            class="relative h-20 w-20 overflow-hidden rounded-2xl shadow-lg"
        >

            <img
                src="https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/15/13112/26928"
                alt=""
                class="absolute inset-0 h-full w-full object-cover"
            >

            <div class="absolute inset-0 bg-black/20"></div>

            <div class="absolute bottom-1 left-0 right-0 flex items-center justify-center gap-1 px-1 text-[11px] font-semibold text-white">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 shrink-0"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path d="M12 2 1 7l11 5 9-4.09V17h2V7L12 2Zm0 12L1 9v2l11 5 11-5V9l-11 5Zm0 5L1 14v2l11 5 11-5v-2l-11 5Z"/>
                </svg>

                <span class="truncate">Lapisan</span>

            </div>

        </button>

        <div
            id="vehicleLayerDropdown"
            class="absolute bottom-0 right-[90px] hidden w-48 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl"
        >

            <button
                type="button"
                class="vehicle-layer-option flex w-full items-center gap-3 px-4 py-3 text-left text-sm hover:bg-slate-50"
                data-layer="default"
            >
                🗺️ Default
            </button>

            <button
                type="button"
                class="vehicle-layer-option flex w-full items-center gap-3 border-t px-4 py-3 text-left text-sm hover:bg-slate-50"
                data-layer="satellite"
            >
                🛰️ Satelit
            </button>

        </div>

    </div>

</div>

{{-- ===================================================== --}}
{{-- Map Style --}}
{{-- ===================================================== --}}

<style>

#vehicleMapContainer{

    position:relative;

    overflow:hidden;


}

#vehicleMap{

    width:100%;

    height:620px;

    z-index:1;

}

#vehicleMap .leaflet-control-container{

    display:none;

}

#vehiclePreviewCard{

    transition:.25s ease;

}

#vehicleHomeZoneBadge{

    transition:.25s ease;

}

#vehicleOfficeZoneBadge{

    transition:.25s ease;

}

#vehiclePlaybackToolbar{

    transition:.25s ease;

}

#vehicleMapContainer .leaflet-popup{

    display:none;

}

#vehicleMapContainer .leaflet-marker-icon{

    z-index:300;

}

#vehicleMapContainer .leaflet-pane{

    z-index:200;

}

#vehicleMapContainer .leaflet-control-attribution{

    font-size:10px;

}

</style>