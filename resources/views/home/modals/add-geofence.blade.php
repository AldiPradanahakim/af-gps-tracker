{{-- resources/views/home/modals/add-geofence.blade.php --}}

<div
    id="addGeofenceModal"
    class="fixed inset-0 z-[9999] hidden items-center justify-center bg-slate-950/50 backdrop-blur-sm">

    <div
    class="relative flex max-h-[90vh] w-full max-w-2xl flex-col rounded-3xl bg-white shadow-2xl">

        {{-- Header --}}
        <div
            class="flex items-center justify-between border-b border-slate-200 px-8 py-6">

            <div>

                <h2 class="text-2xl font-bold text-slate-900">

                    Tambah Geofence

                </h2>

                <p class="mt-2 text-sm text-slate-500">

                    Tambahkan area geofence untuk kendaraan.

                </p>

            </div>

            <button
                id="closeAddGeofence"
                type="button"
                class="rounded-xl p-2 transition hover:bg-slate-100">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6 text-slate-500"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"/>

                </svg>

            </button>

        </div>

        {{-- Body --}}
        <form
            method="POST"
            action="{{ route('geofences.store') }}"
            class="flex-1 space-y-6 overflow-y-auto px-8 py-8">

            @csrf

            {{-- Nama --}}
            <div>

                <label class="mb-2 block text-sm font-semibold text-slate-700">

                    Nama Geofence

                </label>

                <input
                    type="text"
                    name="name"
                    placeholder="Contoh : Rumah"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-[#2563EB] focus:outline-none focus:ring-4 focus:ring-blue-100">

            </div>

            {{-- Deskripsi --}}
            <div>

                <label class="mb-2 block text-sm font-semibold text-slate-700">

                    Deskripsi

                </label>

                <textarea
                    name="description"
                    rows="3"
                    placeholder="Opsional"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-[#2563EB] focus:outline-none focus:ring-4 focus:ring-blue-100"></textarea>

            </div>

            {{-- Kendaraan --}}
            <div>

                <label class="mb-2 block text-sm font-semibold text-slate-700">

                    Kendaraan

                </label>

                <select
                    name="device_id"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-[#2563EB] focus:outline-none focus:ring-4 focus:ring-blue-100">

                    @foreach ($devices as $device)

                        <option value="{{ $device['id'] }}">

                            {{ $device['vehicle_name'] }}

                            @if($device['plate_number'])

                                ({{ $device['plate_number'] }})

                            @endif

                        </option>

                    @endforeach

                </select>

            </div>

            {{-- Jenis --}}
            <div>

                <label class="mb-3 block text-sm font-semibold text-slate-700">

                    Jenis Geofence

                </label>

                <div class="grid grid-cols-2 gap-4">

                    <label
                        class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-300 p-4">

                        <input
                            checked
                            type="radio"
                            name="type"
                            value="radius"
                            id="radiusType">

                        <div>

                            <div class="font-semibold text-slate-800">

                                Radius

                            </div>

                            <div class="text-sm text-slate-500">

                                Area lingkaran

                            </div>

                        </div>

                    </label>

                    <label
                        class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-300 p-4">

                        <input
                            type="radio"
                            name="type"
                            value="administrative"
                            id="administrativeType">

                        <div>

                            <div class="font-semibold text-slate-800">

                                Administratif

                            </div>

                            <div class="text-sm text-slate-500">

                                Berdasarkan wilayah

                            </div>

                        </div>

                    </label>

                </div>

            </div>

            {{-- ========================= --}}
            {{-- RADIUS --}}
            {{-- ========================= --}}
            <div id="radiusContainer">

                <label class="mb-3 block text-sm font-semibold text-slate-700">

                    Titik Awal

                </label>

                <div class="space-y-3">

                    <label
                        class="flex items-center gap-3 rounded-xl border border-slate-300 p-4">

                        <input
                            checked
                            type="radio"
                            name="source"
                            value="home">

                        <div>

                            <div class="font-medium">

                                Home Location

                            </div>

                            <div class="text-sm text-slate-500">

                                Menggunakan Home Location kendaraan

                            </div>

                        </div>

                    </label>

                    <label
                        class="flex items-center gap-3 rounded-xl border border-slate-300 p-4">

                        <input
                            type="radio"
                            name="source"
                            value="current_location">

                        <div>

                            <div class="font-medium">

                                Lokasi Kendaraan Saat Ini

                            </div>

                            <div class="text-sm text-slate-500">

                                Menggunakan lokasi GPS terakhir

                            </div>

                        </div>

                    </label>

                </div>

                <div class="mt-6">

                    <label class="mb-2 block text-sm font-semibold">

                        Radius

                    </label>

                    <div class="flex gap-3">

                        <input
                            type="number"
                            name="radius"
                            value="500"
                            min="50"
                            class="flex-1 rounded-xl border border-slate-300 px-4 py-3">

                        <select
                            name="radius_unit"
                            class="w-40 rounded-xl border border-slate-300 px-4 py-3">

                            <option value="meter">

                                Meter

                            </option>

                            <option value="kilometer">

                                Kilometer

                            </option>

                        </select>

                    </div>

                </div>

            </div>

            {{-- ========================= --}}
            {{-- ADMINISTRATIF --}}
            {{-- ========================= --}}

            <div
                id="administrativeContainer"
                class="hidden">

                <label class="mb-2 block text-sm font-semibold">

                    Cari Wilayah

                </label>

                <div class="relative">

                    <input
                        id="administrativeSearch"
                        type="text"
                        autocomplete="off"
                        placeholder="Contoh : Kota Bandung"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3">

                    <div
                        id="administrativeResult"
                        class="absolute left-0 right-0 top-full z-50 mt-2 hidden overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">

                    </div>

                </div>

                <input
                    type="hidden"
                    name="display_name">

                <input
                    type="hidden"
                    name="geojson">

            </div>
                        {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-6">

                <button
                    id="cancelAddGeofence"
                    type="button"
                    class="rounded-xl border border-slate-300 px-6 py-3 font-medium text-slate-700 transition hover:bg-slate-100">

                    Batal

                </button>

                <button
                    type="submit"
                    class="rounded-xl bg-[#2563EB] px-6 py-3 font-semibold text-white transition hover:bg-blue-700">

                    Simpan Geofence

                </button>

            </div>

        </form>

    </div>

</div>

{{-- ========================================================= --}}
{{-- Script --}}
{{-- ========================================================= --}}

<script>

document.addEventListener('DOMContentLoaded', () => {

    const modal = document.getElementById('addGeofenceModal');

    const openButton = document.getElementById('addGeofence');

    const closeButton = document.getElementById('closeAddGeofence');

    const cancelButton = document.getElementById('cancelAddGeofence');

    const radiusType = document.getElementById('radiusType');

    const administrativeType = document.getElementById('administrativeType');

    const radiusContainer = document.getElementById('radiusContainer');

    const administrativeContainer = document.getElementById('administrativeContainer');

    const searchInput = document.getElementById('administrativeSearch');

    const searchResult = document.getElementById('administrativeResult');

    const displayNameInput = document.querySelector('input[name="display_name"]');

    const geojsonInput = document.querySelector('input[name="geojson"]');

    let debounce;

    /*
    |--------------------------------------------------------------------------
    | Modal
    |--------------------------------------------------------------------------
    */

    function openModal() {

        modal.classList.remove('hidden');

        modal.classList.add('flex');

    }

    function closeModal() {

        modal.classList.remove('flex');

        modal.classList.add('hidden');

    }

    openButton?.addEventListener('click', openModal);

    closeButton?.addEventListener('click', closeModal);

    cancelButton?.addEventListener('click', closeModal);

    modal?.addEventListener('click', (e) => {

        if (e.target === modal) {

            closeModal();

        }

    });

    /*
    |--------------------------------------------------------------------------
    | Radius / Administrative
    |--------------------------------------------------------------------------
    */

    function toggleType() {

        if (radiusType.checked) {

            radiusContainer.classList.remove('hidden');

            administrativeContainer.classList.add('hidden');

        } else {

            radiusContainer.classList.add('hidden');

            administrativeContainer.classList.remove('hidden');

        }

    }

    radiusType.addEventListener('change', toggleType);

    administrativeType.addEventListener('change', toggleType);

    toggleType();

    /*
    |--------------------------------------------------------------------------
    | Search Administrative
    |--------------------------------------------------------------------------
    */

    searchInput?.addEventListener('input', function () {

        clearTimeout(debounce);

        const keyword = this.value.trim();

        if (keyword.length < 2) {

            searchResult.classList.add('hidden');

            searchResult.innerHTML = '';

            return;

        }

        debounce = setTimeout(() => {

            searchAdministrative(keyword);

        }, 350);

    });

    async function searchAdministrative(keyword) {

        try {

            const response = await fetch(

                `/api/search?keyword=${encodeURIComponent(keyword)}`,

                {

                    headers: {

                        'Accept': 'application/json',

                        'X-Requested-With': 'XMLHttpRequest'

                    },

                    credentials: 'same-origin'

                }

            );

            const data = await response.json();

            renderAdministrative(data);

        } catch (e) {

            searchResult.classList.add('hidden');

        }

    }

    /*
    |--------------------------------------------------------------------------
    | Render Result
    |--------------------------------------------------------------------------
    */

    function renderAdministrative(results) {

        searchResult.innerHTML = '';

        const administrative = results.filter(item => item.type === 'administrative');

        if (!administrative.length) {

            searchResult.classList.add('hidden');

            return;

        }

        searchResult.classList.remove('hidden');

        administrative.forEach(item => {

            const button = document.createElement('button');

            button.type = 'button';

            button.className =
                'flex w-full flex-col border-b border-slate-100 px-4 py-3 text-left hover:bg-slate-50';

            button.innerHTML = `

                <span class="font-medium text-slate-900">

                    ${item.title}

                </span>

                <span class="mt-1 text-xs text-slate-500">

                    ${item.subtitle ?? ''}

                </span>

            `;

            button.addEventListener('click', () => {

                searchInput.value = item.title;

                displayNameInput.value = item.title;

                geojsonInput.value = JSON.stringify(item.geojson);

                searchResult.classList.add('hidden');

                /*
                |--------------------------------------------------------------------------
                | Preview Polygon
                |--------------------------------------------------------------------------
                */

                if (

                    window.GPSTracker &&

                    typeof GPSTracker.previewAdministrative === 'function'

                ) {

                    GPSTracker.previewAdministrative(item.geojson);

                }

            });

            searchResult.appendChild(button);

        });

    }

    /*
    |--------------------------------------------------------------------------
    | Hide Result
    |--------------------------------------------------------------------------
    */

    document.addEventListener('click', function (e) {

        if (

            !searchResult.contains(e.target) &&

            e.target !== searchInput

        ) {

            searchResult.classList.add('hidden');

        }

    });

});

</script>