<!-- Mobile backdrop -->
<div x-show="mobileMenuOpen" 
     style="display: none;" 
     class="fixed inset-0 z-[4000] bg-slate-900/50 backdrop-blur-sm lg:hidden" 
     @click="mobileMenuOpen = false" 
     x-transition.opacity></div>

<aside
    id="vehicleSidebar"
    :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-[4001] flex h-screen w-[280px] flex-shrink-0 flex-col border-r border-slate-200 bg-white transition-transform duration-300 lg:static lg:w-[240px] lg:translate-x-0"
>

    {{-- ========================================================= --}}
    {{-- Logo --}}
    {{-- ========================================================= --}}

    <div class="flex items-center justify-between px-4 pt-4 pb-4">

        <a
            href="{{ route('home') }}"
            class="flex items-center gap-2.5"
        >

            <div
                class="flex h-8 w-8 items-center justify-center rounded-2xl bg-blue-50"
            >

                <img
                    src="{{ asset('images/logo-gps.png') }}"
                    alt="Logo GPS"
                    class="h-6 w-6 object-contain"
                >

            </div>

            <div class="leading-tight">

                <h1
                    class="text-[13px] font-bold tracking-wide text-slate-900"
                >
                    AF GPS TRACKER
                </h1>

                <p
                    class="mt-0.5 text-[9px] font-semibold uppercase tracking-[0.22em] text-slate-400"
                >
                    Sistem Pemantauan Kendaraan
                </p>

            </div>

        </a>

        <!-- Close Mobile Menu -->
        <button @click="mobileMenuOpen = false" type="button" class="lg:hidden rounded-lg p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-700">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>

    </div>

    {{-- ========================================================= --}}
    {{-- Content --}}
    {{-- ========================================================= --}}

    <div class="flex flex-1 flex-col overflow-hidden">

        <div class="flex-1 overflow-y-auto px-4">

            {{-- ========================================================= --}}
            {{-- Vehicle --}}
            {{-- ========================================================= --}}

            <div>

                <p
                    class="mb-3 text-[10px] font-bold uppercase tracking-[0.22em] text-slate-400"
                >
                    Kendaraan
                </p>

                <div class="space-y-2">

                    @foreach($vehicles as $item)

                        @php
                            $active = $item->id === $device->id;
                        @endphp

                        <a

                            href="{{ route('vehicles.show', $item) }}"

                            class="group flex items-center justify-between rounded-2xl px-3 py-2.5 transition-all duration-200
                            {{ $active
                                ? 'border border-blue-100 bg-blue-50 shadow-sm'
                                : 'border border-transparent bg-white hover:border-slate-200 hover:bg-slate-50'
                            }}"

                        >

                            <div
                                class="flex items-center gap-2.5"
                            >

                                {{-- ========================================================= --}}
                                {{-- Status --}}
                                {{-- ========================================================= --}}

                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-xl border border-slate-200 bg-white"
                                >

                                    <span
                                        class="h-2 w-2 rounded-full {{ $item->is_online ? 'bg-emerald-500' : 'bg-red-500' }}"
                                    ></span>

                                </div>

                                {{-- ========================================================= --}}
                                {{-- Vehicle --}}
                                {{-- ========================================================= --}}

                                <div
                                    class="min-w-0"
                                >

                                    <p
                                        class="truncate text-[12px] font-semibold text-slate-900"
                                    >
                                        {{ $item->vehicle->vehicle_name }}
                                    </p>

                                    <p
                                        class="mt-0.5 truncate text-[10px] text-slate-400"
                                    >
                                        {{ $item->device_id }}
                                    </p>

                                </div>

                            </div>

                            <i
                                class="fa-solid fa-chevron-right text-[10px] text-slate-300 transition group-hover:text-blue-500"
                            ></i>

                        </a>

                    @endforeach

                </div>

            </div>

            {{-- ========================================================= --}}
            {{-- Add Vehicle --}}
            {{-- ========================================================= --}}

            <div class="mt-5">

                <button
                    id="sidebarAddVehicleButton"
                    type="button"
                    class="flex h-11 w-full items-center justify-center gap-2 rounded-2xl border border-blue-200 bg-white text-[12px] font-semibold text-blue-700 transition-all duration-200 hover:border-blue-300 hover:bg-blue-50"
                >

                    <i class="fa-solid fa-plus text-[10px]"></i>

                    <span>
                        Tambah Kendaraan
                    </span>

                </button>

            </div>

                        {{-- ========================================================= --}}
            {{-- Menu --}}
            {{-- ========================================================= --}}

            <div class="mt-6">

                <p
                    class="mb-3 text-[10px] font-bold uppercase tracking-[0.22em] text-slate-400"
                >
                    Menu
                </p>

                <nav
                    id="vehicleSidebarMenu"
                    class="space-y-1.5"
                >

                    {{-- ====================================== --}}
                    {{-- Informasi --}}
                    {{-- ====================================== --}}

                    <button
                        type="button"
                        data-section="information"
                        class="vehicle-menu-btn group flex w-full items-center gap-2.5 rounded-2xl bg-blue-50 px-3 py-2.5 text-left transition-all duration-200"
                    >

                        <span
                            class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-600"
                        >
                            <i class="fa-solid fa-circle-info text-[13px]"></i>
                        </span>

                        <span
                            class="text-[12px] font-semibold text-blue-700"
                        >
                            Informasi Kendaraan
                        </span>

                    </button>

                    {{-- ====================================== --}}
                    {{-- Home --}}
                    {{-- ====================================== --}}

                    <button
                        type="button"
                        data-section="home-location"
                        class="vehicle-menu-btn group flex w-full items-center gap-2.5 rounded-2xl px-3 py-2.5 text-left transition-all duration-200 hover:bg-slate-50"
                    >

                        <span
                            class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition group-hover:bg-blue-50 group-hover:text-blue-600"
                        >
                            <i class="fa-solid fa-house text-[13px]"></i>
                        </span>

                        <span
                            class="text-[12px] font-semibold text-slate-700 transition group-hover:text-blue-600"
                        >
                            Lokasi Rumah
                        </span>

                    </button>

                    {{-- ====================================== --}}
                    {{-- Geofence --}}
                    {{-- ====================================== --}}

                    <button
                        type="button"
                        data-section="geofence"
                        class="vehicle-menu-btn group flex w-full items-center gap-2.5 rounded-2xl px-3 py-2.5 text-left transition-all duration-200 hover:bg-slate-50"
                    >
                        <span
                            class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition group-hover:bg-blue-50 group-hover:text-blue-600"
                        >
                            <i class="fa-solid fa-draw-polygon text-[13px]"></i>
                        </span>

                        <span
                            class="text-[12px] font-semibold text-slate-700 transition group-hover:text-blue-600"
                        >
                            Geofence
                        </span>

                    </button>

                    {{-- ====================================== --}}
                    {{-- History --}}
                    {{-- ====================================== --}}

                    <button
                        type="button"
                        data-section="history"
                        class="vehicle-menu-btn group flex w-full items-center gap-2.5 rounded-2xl px-3 py-2.5 text-left transition-all duration-200 hover:bg-slate-50"
                    >

                        <span
                            class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition group-hover:bg-blue-50 group-hover:text-blue-600"
                        >
                            <i class="fa-solid fa-route text-[13px]"></i>
                        </span>

                        <span
                            class="text-[12px] font-semibold text-slate-700 transition group-hover:text-blue-600"
                        >
                            Riwayat Perjalanan
                        </span>

                    </button>

                    {{-- ====================================== --}}
                    {{-- Pesan --}}
                    {{-- ====================================== --}}

                    <button
                        type="button"
                        data-section="messages"
                        class="vehicle-menu-btn group flex w-full items-center gap-2.5 rounded-2xl px-3 py-2.5 text-left transition-all duration-200 hover:bg-slate-50"
                    >
                        <span
                            class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition group-hover:bg-blue-50 group-hover:text-blue-600"
                        >
                            <i class="fa-regular fa-bell text-[13px]"></i>
                        </span>

                        <span
                            class="text-[12px] font-semibold text-slate-700 transition group-hover:text-blue-600"
                        >
                            Pesan
                        </span>

                    </button>

                    {{-- ====================================== --}}
                    {{-- Stop --}}
                    {{-- ====================================== --}}

                    <button
                        type="button"
                        data-section="stop"
                        class="vehicle-menu-btn group flex w-full items-center gap-2.5 rounded-2xl px-3 py-2.5 text-left transition-all duration-200 hover:bg-slate-50"
                    >
                        <span
                            class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition group-hover:bg-blue-50 group-hover:text-blue-600"
                        >
                            <i class="fa-solid fa-stopwatch text-[13px]"></i>
                        </span>

                        <span
                            class="text-[12px] font-semibold text-slate-700 transition group-hover:text-blue-600"
                        >
                            Kendaraan Berhenti
                        </span>

                    </button>

                    {{-- ====================================== --}}
                    {{-- Speed --}}
                    {{-- ====================================== --}}

                    <button
                        type="button"
                        data-section="speed"
                        class="vehicle-menu-btn group flex w-full items-center gap-2.5 rounded-2xl px-3 py-2.5 text-left transition-all duration-200 hover:bg-slate-50"
                    >
                        <span
                            class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition group-hover:bg-blue-50 group-hover:text-blue-600"
                        >
                            <i class="fa-solid fa-gauge-high text-[13px]"></i>
                        </span>

                        <span
                            class="text-[12px] font-semibold text-slate-700 transition group-hover:text-blue-600"
                        >
                            Batas Kecepatan
                        </span>

                    </button>

                </nav>

            </div>

        </div>

        {{-- ========================================================= --}}
        {{-- Bottom Action --}}
        {{-- ========================================================= --}}

        <div
            class="border-t border-slate-200 bg-white px-4 py-4"
        >

            <button

                id="sidebarDeleteVehicleButton"

                type="button"

                data-device-id="{{ $device->id }}"

                data-vehicle-name="{{ $device->vehicle->vehicle_name ?? $device->device_id }}"

                class="flex h-11 w-full items-center justify-center gap-2
                       rounded-2xl
                       border border-red-200
                       bg-red-50
                       text-[12px] font-semibold text-red-600
                       transition-all duration-200
                       hover:bg-red-100
                       hover:border-red-300"

            >

                <i
                    class="fa-solid fa-trash text-[12px]"
                ></i>

                <span>

                    Hapus Kendaraan

                </span>

            </button>

        </div>

    </div>

</aside>
