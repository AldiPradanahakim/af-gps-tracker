<div
    id="homeLocationModal"
    class="fixed inset-0 z-[9999] hidden pointer-events-none">

    <div
    class="pointer-events-auto absolute left-6 top-6 flex w-[460px] max-h-[calc(100vh-48px)] flex-col overflow-hidden rounded-3xl bg-white shadow-2xl">

        <div
            class="flex items-center justify-between border-b border-slate-200 px-8 py-6">

            <div>

                <h2
                    class="text-2xl font-bold">

                    Lokasi Rumah

                </h2>

                <p
                    class="mt-2 text-sm text-slate-500">

                    Atur Lokasi Rumah kendaraan.

                </p>

            </div>

            <button
                id="closeHomeLocation"
                class="rounded-xl p-2 hover:bg-slate-100">

                ✕

            </button>

        </div>

        <div
            class="flex-1 space-y-6 overflow-y-auto p-8">
            <div>

                <label
                    class="mb-2 block text-sm font-semibold">

                    Kendaraan

                </label>

                @php
                    $devicesWithoutHome = collect($devices)->filter(
                        fn($d) => empty($d['home_location'])
                    )->values();
                @endphp

                @if($devicesWithoutHome->isEmpty())

                    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                        <strong>Semua kendaraan</strong> sudah memiliki Lokasi Rumah.
                        Hapus Lokasi Rumah pada salah satu kendaraan terlebih dahulu untuk menambahkan yang baru.
                    </div>

                @else

                    <select
                        id="homeDevice"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3">

                        <option value="all">Semua Kendaraan</option>

                        @foreach($devicesWithoutHome as $device)

                            <option value="{{ $device['id'] }}">

                                {{ $device['vehicle_name'] ?? $device['device_id'] }}

                            </option>

                        @endforeach

                    </select>

                @endif

            </div>

            <div>

                <div>

                    <label
                        class="mb-2 block text-sm font-semibold text-slate-700">

                        Cari Lokasi

                    </label>

                    <div class="relative">

                        <input
                            id="homeSearch"
                            type="text"
                            autocomplete="off"
                            placeholder="Contoh : Sarijadi Bandung"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-100">

                        <div
                            id="homeSearchLoading"
                            class="absolute right-3 top-1/2 hidden -translate-y-1/2">

                            <svg
                                class="h-5 w-5 animate-spin text-blue-600"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24">

                                <circle
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                    class="opacity-25"/>

                                <path
                                    fill="currentColor"
                                    class="opacity-75"
                                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>

                            </svg>

                        </div>

                        <div
                            id="homeSearchResult"
                            class="absolute left-0 right-0 top-full z-[10000] mt-2 hidden max-h-80 overflow-y-auto overflow-x-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl">

                        </div>

                    </div>

                </div>

            </div>

            <div
                class="rounded-2xl border border-blue-200 bg-blue-50 p-5">

                <div
                    class="font-semibold text-blue-700">

                    Titik Home

                </div>

                <div
                    id="homeCoordinate"
                    class="mt-2 text-sm text-slate-600">

                    Belum memilih lokasi.

                </div>

            </div>

            <div
                class="grid grid-cols-2 gap-5">

                <div>

                    <label
                        class="mb-2 block text-sm font-semibold">

                        Lintang

                    </label>

                    <input
                        id="homeLatitudePreview"
                        readonly
                        class="w-full rounded-xl border border-slate-300 bg-slate-100 px-4 py-3">

                </div>

                <div>

                    <label
                        class="mb-2 block text-sm font-semibold">

                        Bujur

                    </label>

                    <input
                        id="homeLongitudePreview"
                        readonly
                        class="w-full rounded-xl border border-slate-300 bg-slate-100 px-4 py-3">

                </div>

            </div>

            <input id="homeLatitude" name="latitude" type="hidden">
            <input id="homeLongitude" name="longitude" type="hidden">
            <input id="homeDisplayName" name="display_name" type="hidden">
        </div>

        <div
            class="rounded-xl border border-amber-300 bg-amber-50 p-5">

            <div
                class="font-semibold text-amber-800">

                Informasi

            </div>

            <div
                class="mt-2 text-sm text-amber-700">

                Cari alamat kemudian pilih lokasi.
                Penanda Rumah akan langsung muncul di peta.

            </div>

        </div>

        <div
            class="flex justify-end gap-3 border-t border-slate-200 px-8 py-6">

            <button
                id="cancelHomeLocation"
                class="rounded-xl border border-slate-300 px-5 py-3">

                Batal

            </button>

            <button
                id="saveHomeLocation"
                class="rounded-xl bg-blue-600 px-5 py-3 font-semibold text-white">

                Simpan

            </button>

        </div>

    </div>

</div>