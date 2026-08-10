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
                    Update terakhir
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
    {{-- Floating Toolbar --}}
    {{-- ===================================================== --}}

    <div
        class="absolute right-5 top-1/2 z-[700] flex -translate-y-1/2 flex-col gap-3"
    >

        <button
            id="vehicleZoomInButton"
            type="button"
            class="flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white shadow-lg transition hover:bg-slate-100"
        >
            <i class="fa-solid fa-plus text-slate-700"></i>
        </button>

        <button
            id="vehicleZoomOutButton"
            type="button"
            class="flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white shadow-lg transition hover:bg-slate-100"
        >
            <i class="fa-solid fa-minus text-slate-700"></i>
        </button>

    </div>

    {{-- ===================================================== --}}
    {{-- Map Layer --}}
    {{-- ===================================================== --}}

    <div class="absolute bottom-5 right-5 z-[700]">

        <button
            id="vehicleLayerButton"
            type="button"
            class="relative h-16 w-16 overflow-hidden rounded-2xl border border-slate-200 shadow-lg"
        >

            <img
                src="https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/15/13112/26928"
                alt=""
                class="absolute inset-0 h-full w-full object-cover"
            >

            <div class="absolute inset-0 bg-black/20"></div>

            <div class="absolute bottom-1 left-0 right-0 flex items-center justify-center gap-1 px-1 text-[10px] font-semibold text-white">
                <i class="fa-solid fa-layer-group"></i>
                <span class="truncate">Lapisan</span>
            </div>

        </button>

        <div
            id="vehicleLayerDropdown"
            class="absolute bottom-0 right-[76px] hidden w-44 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl"
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
                🛰️ Satellite
            </button>

            <button
                type="button"
                class="vehicle-layer-option flex w-full items-center gap-3 border-t px-4 py-3 text-left text-sm hover:bg-slate-50"
                data-layer="dark"
            >
                🌙 Dark
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