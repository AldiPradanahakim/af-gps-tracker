<div class="space-y-6">

    {{-- ========================================================= --}}
    {{-- ROW 1 --}}
    {{-- ========================================================= --}}

    <div
        class="grid grid-cols-12 gap-5 items-start"
    >

        {{-- ========================================================= --}}
        {{-- INFORMASI KENDARAAN --}}
        {{-- ========================================================= --}}

        <section
            class="col-span-12 xl:col-span-6 flex h-[370px] flex-col overflow-hidden rounded-[20px] border border-slate-200 bg-white vehicle-panel-shadow"
        >

            {{-- ===================================================== --}}
            {{-- Header --}}
            {{-- ===================================================== --}}

            <div
                class="border-b flex items-center justify-between px-6 py-5"
            >

                <h2
                    class="text-[15px] font-semibold text-slate-900"
                >
                    Informasi Kendaraan
                </h2>

                <button
                    id="editVehicleButton"
                    type="button"
                    class="inline-flex h-9 items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-[11px] font-medium text-slate-700 transition hover:border-blue-300 hover:bg-slate-50"
                >

                    <i class="fa-solid fa-pen text-[10px]"></i>

                    Edit

                </button>

            </div>

            {{-- ===================================================== --}}
            {{-- Data Kendaraan --}}
            {{-- ===================================================== --}}

            <div
                class="px-6 pb-10 pt-4"
            >

                <div
                    class="max-w-[390px] space-y-4"
                >

                    {{-- Nama --}}
                    <div class="grid grid-cols-[145px_15px_1fr] items-center">

                        <span class="text-[13px] text-slate-500">
                            Nama Kendaraan
                        </span>

                        <span class="text-center text-slate-400">
                            :
                        </span>

                        <span class="text-[13px] font-semibold text-slate-900">
                            {{ $device->vehicle->vehicle_name }}
                        </span>

                    </div>

                    {{-- Device --}}
                    <div class="grid grid-cols-[145px_15px_1fr] items-center">

                        <span class="text-[13px] text-slate-500">
                            ID Perangkat
                        </span>

                        <span class="text-center text-slate-400">
                            :
                        </span>

                        <span class="text-[13px] font-semibold text-slate-900">
                            {{ $device->device_id }}
                        </span>

                    </div>

                    {{-- Plat --}}
                    <div class="grid grid-cols-[145px_15px_1fr] items-center">

                        <span class="text-[13px] text-slate-500">
                            Nomor Polisi
                        </span>

                        <span class="text-center text-slate-400">
                            :
                        </span>

                        <span class="text-[13px] font-semibold text-slate-900">
                            {{ $device->vehicle->plate_number ?? '-' }}
                        </span>

                    </div>

                    {{-- Jenis --}}
                    <div class="grid grid-cols-[145px_15px_1fr] items-center">

                        <span class="text-[13px] text-slate-500">
                            Jenis Kendaraan
                        </span>

                        <span class="text-center text-slate-400">
                            :
                        </span>

                        <span class="text-[13px] font-semibold text-slate-900">
                            {{ ucfirst($device->vehicle->vehicle_type ?? '-') }}
                        </span>

                    </div>

                </div>

            </div>

            {{-- ===================================================== --}}
            {{-- Status Realtime --}}
            {{-- ===================================================== --}}

            <div
                class="px-6 pb-6"
            >

                <div
                    class="overflow-hidden rounded-xl border border-emerald-100 bg-gradient-to-r from-emerald-50 to-slate-50"
                >

                    <div
                        class="grid grid-cols-4"
                    >

                        {{-- STATUS --}}
                        <div class="px-4 py-3">

                            <p
                                class="text-[10px] text-slate-500"
                            >
                                Status
                            </p>

                            <div
                                class="mt-1 flex items-center gap-2"
                            >

                                <span
                                    class="h-2 w-2 rounded-full bg-emerald-500"
                                ></span>

                                <span
                                    id="vehicleStatus"
                                    class="text-[13px] font-semibold text-emerald-600"
                                >
                                    -
                                </span>

                            </div>

                        </div>

                        {{-- SPEED --}}
                        <div
                            class="border-l border-emerald-100 px-4 py-3"
                        >

                            <p
                                class="text-[10px] text-slate-500"
                            >
                                Kecepatan
                            </p>

                            <p
                                id="vehicleSpeed"
                                class="mt-1 text-[13px] font-semibold text-slate-900"
                            >
                                0 km/jam
                            </p>

                        </div>

                        {{-- ARAH --}}
                        <div
                            class="border-l border-emerald-100 px-4 py-3"
                        >

                            <p
                                class="text-[10px] text-slate-500"
                            >
                                Arah
                            </p>

                            <p
                                id="vehicleDirection"
                                class="mt-1 text-[13px] font-semibold text-slate-900"
                            >
                                -
                            </p>

                        </div>

                        {{-- BATTERY --}}
                        <div
                            class="border-l border-emerald-100 px-4 py-3"
                        >

                            <p
                                class="text-[10px] text-slate-500"
                            >
                                Baterai
                            </p>

                            <div
                                class="mt-1 flex items-center gap-2"
                            >

                                <i
                                    class="fa-solid fa-battery-three-quarters text-[14px] text-emerald-500"
                                ></i>

                                <span
                                    id="vehicleBattery"
                                    class="text-[13px] font-semibold text-slate-900"
                                >
                                    -
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>
        {{-- ===================================================== --}}
        {{-- LOKASI TERAKHIR --}}
        {{-- ===================================================== --}}

        <section
            class="col-span-12 xl:col-span-3 flex h-[370px] flex-col overflow-hidden rounded-[20px] border border-slate-200 bg-white vehicle-panel-shadow"
        >

            {{-- ===================================================== --}}
            {{-- HEADER --}}
            {{-- ===================================================== --}}

            <div
                class="border-b border-slate-200 px-6 py-5"
            >
                <h2
                    class="mt-2 text-[18px] font-bold text-slate-900"
                >
                    Lokasi Terakhir
                </h2>

            </div>

            {{-- ===================================================== --}}
            {{-- CONTENT --}}
            {{-- ===================================================== --}}

            <div
                class="px-6 py-5"
            >

                <div
                    class="space-y-5"
                >

                    {{-- Alamat --}}
                    <div
                        class="flex items-start justify-between gap-6"
                    >

                        <span
                            class="w-[85px] flex-shrink-0 text-[13px] text-slate-500"
                        >
                            Alamat
                        </span>

                        <div
                            id="vehicleAddress"
                            class="flex-1 text-right text-[13px] font-medium leading-6 text-slate-900"
                        >
                            -
                        </div>

                    </div>

                    {{-- Latitude --}}
                    <div
                        class="flex items-center justify-between gap-6"
                    >

                        <span
                            class="text-[13px] text-slate-500"
                        >
                            Latitude
                        </span>

                        <span
                            id="vehicleLatitude"
                            class="text-[13px] font-semibold text-slate-900"
                        >
                            -
                        </span>

                    </div>

                    {{-- Longitude --}}
                    <div
                        class="flex items-center justify-between gap-6"
                    >

                        <span
                            class="text-[13px] text-slate-500"
                        >
                            Longitude
                        </span>

                        <span
                            id="vehicleLongitude"
                            class="text-[13px] font-semibold text-slate-900"
                        >
                            -
                        </span>

                    </div>

                    {{-- Waktu --}}
                    <div
                        class="flex items-center justify-between gap-6"
                    >

                        <span
                            class="text-[13px] text-slate-500"
                        >
                            Waktu
                        </span>

                        <span
                            id="vehicleLastUpdate"
                            class="text-[13px] font-semibold text-slate-900"
                        >
                            -
                        </span>

                    </div>

                </div>

            </div>

        </section>

        {{-- ===================================================== --}}
        {{-- RINGKASAN HARI INI --}}
        {{-- ===================================================== --}}

        <section
            class="col-span-12 xl:col-span-3 flex h-[370px] flex-col overflow-hidden rounded-[20px] border border-slate-200 bg-white vehicle-panel-shadow"
        >

            {{-- ===================================================== --}}
            {{-- HEADER --}}
            {{-- ===================================================== --}}

            <div
                class="border-b border-slate-200 px-6 py-5"
            >
                <h2
                    class="mt-2 text-[18px] font-bold text-slate-900"
                >
                    Ringkasan Hari Ini
                </h2>

            </div>

            {{-- ===================================================== --}}
            {{-- CONTENT --}}
            {{-- ===================================================== --}}

            <div
                class="divide-y divide-slate-100"
            >

                {{-- ================================================= --}}
                {{-- Jarak Tempuh --}}
                {{-- ================================================= --}}

                <div
                    class="flex items-center justify-between px-6 py-4"
                >

                    <div
                        class="flex items-center gap-3"
                    >

                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50"
                        >

                            <i
                                class="fa-solid fa-road text-[13px] text-blue-600"
                            ></i>

                        </div>

                        <div>

                            <p
                                class="text-[12px] font-medium text-slate-500"
                            >
                                Jarak Tempuh
                            </p>

                        </div>

                    </div>

                    <span
                        id="todayDistance"
                        class="text-[14px] font-bold text-slate-900"
                    >
                        0 km
                    </span>

                </div>

                {{-- ================================================= --}}
                {{-- Durasi --}}
                {{-- ================================================= --}}

                <div
                    class="flex items-center justify-between px-6 py-4"
                >

                    <div
                        class="flex items-center gap-3"
                    >

                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50"
                        >

                            <i
                                class="fa-solid fa-clock text-[13px] text-emerald-600"
                            ></i>

                        </div>

                        <p
                            class="text-[12px] font-medium text-slate-500"
                        >
                            Durasi
                        </p>

                    </div>

                    <span
                        id="todayDuration"
                        class="text-[14px] font-bold text-slate-900"
                    >
                        00:00
                    </span>

                </div>

                {{-- ================================================= --}}
                {{-- Kecepatan Maksimum --}}
                {{-- ================================================= --}}

                <div
                    class="flex items-center justify-between px-6 py-4"
                >

                    <div
                        class="flex items-center gap-3"
                    >

                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50"
                        >

                            <i
                                class="fa-solid fa-gauge-high text-[13px] text-amber-600"
                            ></i>

                        </div>

                        <p
                            class="text-[12px] font-medium text-slate-500"
                        >
                            Kecepatan Maks
                        </p>

                    </div>

                    <span
                        id="todayMaxSpeed"
                        class="text-[14px] font-bold text-slate-900"
                    >
                        0 km/jam
                    </span>

                </div>

                {{-- ================================================= --}}
                {{-- Berhenti --}}
                {{-- ================================================= --}}

                <div
                    class="flex items-center justify-between px-6 py-4"
                >

                    <div
                        class="flex items-center gap-3"
                    >

                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-rose-50"
                        >

                            <i
                                class="fa-solid fa-circle-stop text-[13px] text-rose-600"
                            ></i>

                        </div>

                        <p
                            class="text-[12px] font-medium text-slate-500"
                        >
                            Berhenti
                        </p>

                    </div>

                    <span
                        id="todayStop"
                        class="text-[14px] font-bold text-slate-900"
                    >
                        0
                    </span>

                </div>

            </div>

        </section>

    </div>

    {{-- ========================================================= --}}
    {{-- ROW 2 --}}
    {{-- ========================================================= --}}

    <section
        class="overflow-hidden rounded-[22px] border border-slate-200 bg-white vehicle-panel-shadow"
    >

        {{-- ====================================================== --}}
        {{-- RIWAYAT PERJALANAN TERAKHIR --}}
        {{-- ====================================================== --}}

        <section
            class="overflow-hidden rounded-[22px] border border-slate-200 bg-white vehicle-panel-shadow"
        >

            {{-- ================================================== --}}
            {{-- Header --}}
            {{-- ================================================== --}}

            <div
                class="flex items-center justify-between border-b border-slate-200 px-6 py-5"
            >

                <div>

                    <p
                        class="text-[10px] font-semibold uppercase tracking-[0.30em] text-slate-400"
                    >
                        RIWAYAT PERJALANAN
                    </p>

                    <h2
                        class="mt-2 text-[18px] font-bold text-slate-900"
                    >
                        Perjalanan Terakhir
                    </h2>

                    <p
                        class="mt-1 text-[12px] text-slate-500"
                    >
                        Histori perjalanan GPS kendaraan.
                    </p>

                </div>

                <button

                    id="historyRefreshButton"

                    type="button"

                    class="inline-flex h-9 items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-[12px] font-semibold text-slate-700 transition hover:bg-slate-50"

                >

                    <i class="fa-solid fa-rotate-right text-[11px]"></i>

                    Refresh

                </button>

            </div>

            {{-- ================================================== --}}
            {{-- Header Table --}}
            {{-- ================================================== --}}

            <div
                class="grid grid-cols-12 border-b border-slate-200 bg-slate-50 px-6 py-3 text-[11px] font-semibold uppercase tracking-wide text-slate-500"
            >

                <div class="col-span-2">
                    Waktu
                </div>

                <div class="col-span-2">
                    Latitude
                </div>

                <div class="col-span-2">
                    Longitude
                </div>

                <div class="col-span-2">
                    Speed
                </div>

                <div class="col-span-4">
                    Alamat
                </div>

            </div>

            {{-- ================================================== --}}
            {{-- Data --}}
            {{-- ================================================== --}}

            <div
                id="vehicleLatestHistory"
                class="divide-y divide-slate-100"
            >

                {{-- Javascript Render --}}
                {{-- Diisi oleh script dari database --}}

            </div>

            {{-- ================================================== --}}
            {{-- Empty --}}
            {{-- ================================================== --}}

            <div

                id="vehicleLatestHistoryEmpty"

                class="hidden px-6 py-14 text-center"

            >

                <div
                    class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100"
                >

                    <i
                        class="fa-solid fa-route text-lg text-slate-400"
                    ></i>

                </div>

                <h3
                    class="mt-4 text-[15px] font-semibold text-slate-900"
                >
                    Belum ada riwayat perjalanan
                </h3>

                <p
                    class="mt-2 text-[12px] text-slate-500"
                >
                    Data akan muncul setelah perangkat GPS mengirimkan lokasi.
                </p>

            </div>

        </section>

        </div>

    </section>

</div>