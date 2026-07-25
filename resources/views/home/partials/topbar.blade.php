<div class="relative z-[3000] border-b border-slate-200 bg-white">

    <div class="flex h-[72px] items-center justify-between px-8">

        {{-- SEARCH --}}
        <div class="relative w-full max-w-xl">

            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-slate-400"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="m21 21-4.35-4.35m1.35-5.65a7 7 0 1 1-14 0a7 7 0 0 1 14 0Z"/>

                </svg>

            </div>

            <input
                id="searchVehicle"
                type="text"
                autocomplete="off"
                placeholder="Cari kendaraan, alamat, atau wilayah..."
                class="h-12 w-full rounded-2xl border border-slate-300 bg-white pl-12 pr-4 text-sm text-slate-700 outline-none transition focus:border-[#2563EB] focus:ring-4 focus:ring-blue-100">

            {{-- SEARCH RESULT --}}
            <div
                id="searchResult"
                class="absolute left-0 right-0 top-[56px] z-[9999] hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl">

            </div>

        </div>

        {{-- RIGHT MENU --}}
        <div class="ml-8 flex items-center gap-3">

            {{-- HOME --}}
            <button
                id="homeLocationButton"
                type="button"
                class="flex h-11 items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 transition hover:bg-slate-50">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-[#2563EB]"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M3 10.5L12 3l9 7.5"/>

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M5 9.5V21h14V9.5"/>

                </svg>

                Home

            </button>

            {{-- GEOFENCE --}}
            <div class="relative">

                <button
                    id="geofenceButton"
                    type="button"
                    class="flex h-11 items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 transition hover:bg-slate-50">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 text-[#2563EB]"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 21s7-4.35 7-11a7 7 0 1 0-14 0c0 6.65 7 11 7 11Z"/>

                        <circle
                            cx="12"
                            cy="10"
                            r="2.5"/>

                    </svg>

                    Geofence

                   <svg
                        id="geofenceArrow"
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 transition-transform duration-200"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="m6 9 6 6 6-6"/>

                    </svg>

                </button>

               <div
                    id="geofenceDropdown"
                    class="absolute right-0 mt-3 z-[99999] hidden w-80 rounded-2xl border border-slate-200 bg-white shadow-2xl">

                    {{-- QUICK ACTION --}}
                    <div class="border-b border-slate-200 p-4">

                        <p class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-400">
                            Quick Action
                        </p>

                        <div class="space-y-2">

                            <button
                                id="addGeofence"
                                type="button"
                                class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm font-medium text-slate-700 transition hover:bg-slate-100">

                                <span>➕</span>

                                <span>Tambah Geofence</span>

                            </button>

                            <button
                                id="deleteGeofence"
                                type="button"
                                class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm font-medium text-red-600 transition hover:bg-red-50">

                                <span>🗑</span>

                                <span>Hapus Geofence</span>

                            </button>

                        </div>

                    </div>

                    {{-- DISPLAY --}}
                    <div class="p-4">

                        <p class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-400">
                            Tampilkan
                        </p>

                        <div class="space-y-3">

                            <label class="flex cursor-pointer items-center justify-between">

                                <span class="text-sm text-slate-700">

                                    Semua

                                </span>

                                <input
                                    id="toggleAllGeofence"
                                    type="checkbox"
                                    checked
                                    class="h-4 w-4 rounded border-slate-300 text-[#2563EB]">

                            </label>

                            <label class="flex cursor-pointer items-center justify-between">

                                <span class="text-sm text-slate-700">

                                    Radius

                                </span>

                                <input
                                    id="toggleRadius"
                                    type="checkbox"
                                    checked
                                    class="h-4 w-4 rounded border-slate-300 text-[#2563EB]">

                            </label>

                            <label class="flex cursor-pointer items-center justify-between">

                                <span class="text-sm text-slate-700">

                                    Administratif

                                </span>

                                <input
                                    id="toggleAdministrative"
                                    type="checkbox"
                                    checked
                                    class="h-4 w-4 rounded border-slate-300 text-[#2563EB]">

                            </label>

                            <label class="flex cursor-pointer items-center justify-between">

                                <span class="text-sm text-slate-700">

                                    Custom

                                </span>

                                <input
                                    id="toggleCustom"
                                    type="checkbox"
                                    checked
                                    class="h-4 w-4 rounded border-slate-300 text-[#2563EB]">

                            </label>

                        </div>

                    </div>

                </div>

            </div>

            {{-- NOTIFICATION --}}
            <div class="relative">

                <button
                    id="notificationButton"
                    type="button"
                    class="relative flex h-11 w-11 items-center justify-center rounded-xl border border-slate-200 bg-white transition hover:bg-slate-50">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 text-slate-700"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2c0 .53-.21 1.04-.59 1.4L4 17h5m6 0a3 3 0 1 1-6 0m6 0H9"/>

                    </svg>

                    <span
                            id="notificationBadge"
                            class="absolute -right-1 -top-1 hidden flex h-5 min-w-[20px] items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white">
                    </span> 

                </button>

                <div
                    id="notificationDropdown"
                    class="absolute right-0 mt-3 z-[99999] hidden w-72 rounded-2xl border border-slate-200 bg-white p-4 shadow-2xl">

                </div>

            </div>

            {{-- PROFILE --}}
            <div class="relative">

                <button
                    id="profileButton"
                    type="button"
                    class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-3 py-2 transition hover:bg-slate-50">

                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#2563EB] text-sm font-bold text-white">

                        {{ strtoupper(substr(auth()->user()->name,0,1)) }}

                    </div>

                    <div class="text-left">

                        <div class="text-sm font-semibold text-slate-900">

                            {{ auth()->user()->name }}

                        </div>

                        <div class="text-xs text-slate-500">

                            {{ auth()->user()->email }}

                        </div>

                    </div>

                  <svg
                        id="profileArrow"
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 text-slate-500 transition-transform duration-200"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="m6 9 6 6 6-6"/>

                    </svg>

                </button>

                <div
                    id="profileDropdown"
                    class="absolute right-0 mt-3 hidden w-64 rounded-2xl border border-slate-200 bg-white shadow-2xl">

                </div>

            </div>

        </div>

    </div>

</div>


