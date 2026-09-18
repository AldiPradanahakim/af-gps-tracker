{{-- resources/views/home/modals/add-geofence.blade.php --}}

<div
    id="addGeofenceModal"
    class="fixed inset-0 z-[9999] hidden items-center justify-center bg-slate-950/50 backdrop-blur-sm">

    <div
        class="relative flex max-h-[92vh] w-full max-w-3xl flex-col overflow-hidden rounded-3xl bg-white shadow-2xl">

        {{-- ========================================================= --}}
        {{-- Header --}}
        {{-- ========================================================= --}}

        <div
            class="flex items-center justify-between border-b border-slate-200 px-8 py-6">

            <div>

                <h2
                    class="text-2xl font-bold text-slate-900">

                    Tambah Geofence

                </h2>

                <p
                    class="mt-2 text-sm text-slate-500">

                    Tambahkan geofence baru pada kendaraan.

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

        {{-- ========================================================= --}}
        {{-- Form --}}
        {{-- ========================================================= --}}

        <form
            id="addGeofenceForm"
            method="POST"
            action="{{ route('geofences.store') }}"
            class="flex-1 overflow-y-auto">

            @csrf

            <div
                class="space-y-8 px-8 py-8">

                {{-- ========================================================= --}}
                {{-- Nama --}}
                {{-- ========================================================= --}}

                <div>

                    <label
                        for="geofenceName"
                        class="mb-2 block text-sm font-semibold text-slate-700">

                        Nama Geofence

                    </label>

                    <input
                        id="geofenceName"
                        name="name"
                        type="text"
                        maxlength="100"
                        autocomplete="off"
                        placeholder="Contoh : Area Aman"

                        class="w-full rounded-xl border border-slate-300 px-4 py-3 transition focus:border-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-100">

                    <p
                        id="nameError"
                        class="mt-2 hidden text-sm text-red-600">

                    </p>

                </div>

                {{-- ========================================================= --}}
                {{-- Deskripsi --}}
                {{-- ========================================================= --}}

                <div>

                    <label
                        for="geofenceDescription"
                        class="mb-2 block text-sm font-semibold text-slate-700">

                        Deskripsi

                    </label>

                    <textarea
                        id="geofenceDescription"
                        name="description"
                        rows="3"
                        maxlength="255"
                        autocomplete="off"
                        placeholder="Opsional"

                        class="w-full rounded-xl border border-slate-300 px-4 py-3 transition focus:border-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-100"></textarea>

                    <p
                        id="descriptionError"
                        class="mt-2 hidden text-sm text-red-600">

                    </p>

                </div>

                {{-- ========================================================= --}}
                {{-- Kendaraan --}}
                {{-- ========================================================= --}}

                <div>

                    <label
                        for="deviceSelect"
                        class="mb-2 block text-sm font-semibold text-slate-700">

                        Kendaraan

                    </label>

                    <select
                        id="deviceSelect"
                        name="device_id"

                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 transition focus:border-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-100">

                        <option
                            value="">

                            -- Pilih Kendaraan --

                        </option>

                        <option
                            value="all">

                            Semua Kendaraan

                        </option>

                        @foreach($devices as $device)

                            <option
                                value="{{ $device['id'] }}"

                                data-device-id="{{ $device['device_id'] ?? '' }}"

                                data-home-lat="{{ $device['home_location']['lat'] ?? '' }}"

                                data-home-lng="{{ $device['home_location']['lng'] ?? '' }}"

                                data-last-lat="{{ $device['last_location']['lat'] ?? '' }}"

                                data-last-lng="{{ $device['last_location']['lng'] ?? '' }}">

                                {{ $device['vehicle_name'] }}

                                @if(!empty($device['plate_number']))

                                    ({{ $device['plate_number'] }})

                                @endif

                            </option>

                        @endforeach

                    </select>

                    <p
                        id="deviceError"
                        class="mt-2 hidden text-sm text-red-600">

                    </p>

                </div>

                {{-- ========================================================= --}}
                {{-- Jenis Geofence --}}
                {{-- ========================================================= --}}

                <div>

                    <label
                        class="mb-4 block text-sm font-semibold text-slate-700">

                        Jenis Geofence

                    </label>

                    <div
                        class="grid grid-cols-3 gap-5">

                        {{-- Radius --}}

                        <label
                            id="radiusCard"

                            class="flex cursor-pointer items-start gap-4 rounded-2xl border-2 border-blue-600 bg-blue-50 p-5 transition">

                            <input
                                id="radiusType"
                                checked
                                type="radio"
                                name="type"
                                value="radius"
                                class="mt-1 h-5 w-5">

                            <div>

                                <div
                                    class="font-semibold text-slate-900">

                                    Radius

                                </div>

                                <p
                                    class="mt-1 text-sm text-slate-500">

                                    Membuat area berbentuk lingkaran berdasarkan titik pusat.

                                </p>

                            </div>

                        </label>

                        {{-- Administrative --}}

                        <label
                            id="administrativeCard"

                            class="flex cursor-pointer items-start gap-4 rounded-2xl border-2 border-slate-300 p-5 transition hover:border-blue-400">

                            <input
                                id="administrativeType"
                                type="radio"
                                name="type"
                                value="administrative"
                                class="mt-1 h-5 w-5">

                            <div>

                                <div
                                    class="font-semibold text-slate-900">

                                    Administratif

                                </div>

                                <p
                                    class="mt-1 text-sm text-slate-500">

                                    Menggunakan batas wilayah administrasi.

                                </p>

                            </div>

                        </label>

                        {{-- Custom Polygon --}}

                        <label
                            id="customCard"
                            class="flex cursor-pointer items-start gap-4 rounded-2xl border-2 border-slate-300 p-5 transition hover:border-blue-400">

                            <input
                                id="customType"
                                type="radio"
                                name="type"
                                value="custom"
                                class="mt-1 h-5 w-5">

                            <div>

                                <div
                                    class="font-semibold text-slate-900">

                                    Poligon Kustom

                                </div>

                                <p
                                    class="mt-1 text-sm text-slate-500">

                                    Gambar area geofence sendiri langsung pada peta.

                                </p>

                            </div>

                        </label>

                    </div>

                </div>

                {{-- ========================================================= --}}
                {{-- Radius Container --}}
                {{-- ========================================================= --}}

                <div
                    id="radiusContainer"
                    class="space-y-8 rounded-2xl border border-slate-200 bg-slate-50 p-6">

                    <div>

                        <h3
                            class="text-base font-semibold text-slate-900">

                            Titik Pusat Radius

                        </h3>

                        <p
                            class="mt-1 text-sm text-slate-500">

                            Pilih sumber titik pusat geofence radius.

                        </p>

                    </div>

                    <div
                        class="space-y-4">

                        <label
                            class="flex cursor-pointer items-start gap-4 rounded-xl border border-slate-300 bg-white p-4">

                            <input
                                checked
                                type="radio"
                                id="radiusHome"
                                name="radius_source"
                                value="home_location"
                                class="mt-1">

                            <div>

                                <div
                                    class="font-medium text-slate-900">

                                    Lokasi Rumah

                                </div>

                                <div
                                    class="mt-1 text-sm text-slate-500">

                                    Menggunakan koordinat Lokasi Rumah kendaraan.

                                </div>

                            </div>

                        </label>

                        <label
                            class="flex cursor-pointer items-start gap-4 rounded-xl border border-slate-300 bg-white p-4">

                            <input
                                type="radio"
                                id="radiusCurrent"
                                name="radius_source"
                                value="current_location"
                                class="mt-1">

                            <div>

                                <div
                                    class="font-medium text-slate-900">

                                    Lokasi GPS Terakhir

                                </div>

                                <div
                                    class="mt-1 text-sm text-slate-500">

                                    Menggunakan koordinat GPS kendaraan terakhir.

                                </div>

                            </div>

                        </label>

                    </div>

                    <div
                        class="grid grid-cols-2 gap-6">

                        <div>

                            <label
                                class="mb-2 block text-sm font-semibold">

                                Radius

                            </label>

                            <input
                                id="radiusValue"
                                name="radius"
                                type="number"
                                min="50"
                                max="50000"
                                value="500"

                                class="w-full rounded-xl border border-slate-300 px-4 py-3">

                        </div>

                        <div>

                            <label
                                class="mb-2 block text-sm font-semibold">

                                Satuan

                            </label>

                            <select
                                id="radiusUnit"
                                name="radius_unit"

                                class="w-full rounded-xl border border-slate-300 px-4 py-3">

                                <option value="meter">

                                    Meter

                                </option>

                                <option value="kilometer">

                                    Kilometer

                                </option>

                            </select>

                        </div>

                    </div>

                    <div
                        class="rounded-xl border border-dashed border-blue-300 bg-blue-50 px-5 py-4">

                        <div
                            class="text-sm font-medium text-blue-700">

                            Titik Terpilih

                        </div>

                        <div
                            id="radiusCoordinate"

                            class="mt-2 text-sm text-slate-600">

                            Belum ada titik dipilih.

                        </div>

                    </div>

                                        <div
                        class="grid grid-cols-2 gap-6">

                        <div>

                            <label
                                class="mb-2 block text-sm font-semibold text-slate-700">

                                Lintang

                            </label>

                            <input
                                id="radiusLatitudePreview"
                                type="text"
                                readonly

                                class="w-full rounded-xl border border-slate-300 bg-slate-100 px-4 py-3 text-slate-600">

                        </div>

                        <div>

                            <label
                                class="mb-2 block text-sm font-semibold text-slate-700">

                                Bujur

                            </label>

                            <input
                                id="radiusLongitudePreview"
                                type="text"
                                readonly

                                class="w-full rounded-xl border border-slate-300 bg-slate-100 px-4 py-3 text-slate-600">

                        </div>

                    </div>

                    <div
                        class="rounded-xl border border-amber-300 bg-amber-50 px-5 py-4">

                        <div
                            class="flex items-start gap-3">

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="mt-0.5 h-5 w-5 text-amber-500"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M12 8v.01"/>

                            </svg>

                            <div>

                                <div class="font-medium text-amber-800">
                                    Informasi
                                </div>

                                <div class="mt-1 text-sm text-amber-700">
                                    Radius akan menggunakan koordinat <b>Lokasi Rumah</b> atau
                                    <b>Lokasi GPS Terakhir</b> sesuai pilihan Anda.
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                {{-- ========================================================= --}}
                {{-- Administrative Container --}}
                {{-- ========================================================= --}}

                <div
                    id="administrativeContainer"
                    class="hidden space-y-8 rounded-2xl border border-slate-200 bg-slate-50 p-6">

                    <div>

                        <h3
                            class="text-base font-semibold text-slate-900">

                            Wilayah Administratif

                        </h3>

                        <p
                            class="mt-1 text-sm text-slate-500">

                            Cari kota, kabupaten, provinsi atau wilayah administrasi lainnya.

                        </p>

                    </div>

                    <div>

                        <label
                            class="mb-2 block text-sm font-semibold text-slate-700">

                            Cari Wilayah

                        </label>

                        <div
                            class="relative">

                            <input

                                id="administrativeSearch"

                                type="text"

                                autocomplete="off"

                                placeholder="Contoh : Kota Bandung"

                                class="w-full rounded-xl border border-slate-300 px-4 py-3 transition focus:border-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-100">

                            <button

                                id="clearAdministrativeSearch"

                                type="button"

                                class="absolute right-3 top-1/2 hidden -translate-y-1/2 rounded-lg p-1 hover:bg-slate-100">

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-slate-500"
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

                            <div

                                id="administrativeLoading"

                                class="absolute right-3 top-1/2 hidden -translate-y-1/2">

                                <svg
                                    class="h-5 w-5 animate-spin text-blue-600"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24">

                                    <circle
                                        class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4">

                                    </circle>

                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">

                                    </path>

                                </svg>

                            </div>

                            <div

                                id="administrativeResult"

                                class="absolute left-0 right-0 top-full z-[10000] mt-2 hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl">

                            </div>

                        </div>

                    </div>

                    <div
                        id="selectedAdministrative"
                        class="hidden rounded-xl border border-emerald-300 bg-emerald-50 p-5">

                        <div
                            class="text-sm font-semibold text-emerald-700">

                            Wilayah Terpilih

                        </div>

                        <div
                            id="selectedAdministrativeName"
                            class="mt-2 text-base font-semibold text-slate-900">

                        </div>

                        <div
                            id="selectedAdministrativeType"
                            class="mt-1 text-sm text-slate-500">

                        </div>

                    </div>

                    <div
                        id="administrativeEmpty"
                        class="hidden rounded-xl border border-slate-300 bg-white px-5 py-8 text-center">

                        <div
                            class="font-medium text-slate-700">

                            Tidak ada wilayah ditemukan.

                        </div>

                    </div>

                    <div
                        id="administrativeError"
                        class="hidden rounded-xl border border-red-300 bg-red-50 px-5 py-4">

                        <div
                            class="font-medium text-red-700">

                            Gagal mengambil data wilayah.

                        </div>

                    </div>

                </div>

                {{-- ========================================================= --}}
                {{-- Custom Polygon Container --}}
                {{-- ========================================================= --}}

                <div
                    id="customContainer"
                    class="hidden space-y-8 rounded-2xl border border-slate-200 bg-slate-50 p-6">

                    <div>

                        <h3
                            class="text-base font-semibold text-slate-900">

                            Poligon Kustom

                        </h3>

                        <p
                            class="mt-1 text-sm text-slate-500">

                            Klik pada peta untuk membuat titik poligon.
                            Klik dua kali untuk menyelesaikan poligon.

                        </p>

                    </div>

                    <div
                        class="rounded-xl border border-blue-300 bg-blue-50 p-5">

                        <div
                            class="font-medium text-blue-700">

                            Cara Menggambar

                        </div>

                        <ol
                            class="mt-3 list-decimal space-y-2 pl-5 text-sm text-blue-700">

                            <li>Klik pada peta untuk membuat titik pertama.</li>

                            <li>Klik lagi untuk membuat titik berikutnya.</li>

                            <li>Minimal 3 titik.</li>

                            <li>Klik dua kali untuk menyelesaikan poligon.</li>

                        </ol>

                    </div>

                    <div class="flex justify-center">

                        <button
                            id="startCustomDrawing"
                            type="button"
                            class="rounded-xl bg-blue-600 px-6 py-3 font-semibold text-white transition hover:bg-blue-700">

                            Mulai Menggambar

                        </button>

                    </div>

                    <div
                        id="customDrawingAction"
                        class="hidden flex justify-center gap-3">

                        <button
                            id="redrawCustomPolygon"
                            type="button"
                            class="rounded-xl border border-slate-300 px-5 py-3 hover:bg-slate-100">

                            Gambar Ulang

                        </button>

                        <button
                            id="cancelCustomPolygon"
                            type="button"
                            class="rounded-xl bg-red-600 px-5 py-3 font-semibold text-white hover:bg-red-700">

                            Batalkan

                        </button>

                    </div>

                    <div
                        id="customPolygonStatus"
                        class="rounded-xl border border-dashed border-slate-300 bg-white px-5 py-6 text-center text-sm text-slate-500">

                        Belum ada poligon dibuat.

                    </div>

                </div>

                {{-- ========================================================= --}}
                {{-- Hidden Input --}}
                {{-- ========================================================= --}}

                <input
                    id="geofenceLatitude"
                    type="hidden"
                    name="latitude">

                <input
                    id="geofenceLongitude"
                    type="hidden"
                    name="longitude">

                <input
                    id="geofenceGeojson"
                    type="hidden"
                    name="geojson">

                <input
                    id="geofenceDisplayName"
                    type="hidden"
                    name="display_name">

                <input
                    id="geofenceAdministrativeType"
                    type="hidden"
                    name="administrative_type">

            </div>

            {{-- ========================================================= --}}
            {{-- Footer --}}
            {{-- ========================================================= --}}

            <div
                class="flex items-center justify-between border-t border-slate-200 bg-white px-8 py-6">

                <div
                    id="submitStatus"
                    class="text-sm text-slate-500">

                </div>

                <div
                    class="flex items-center gap-3">

                    <button
                        id="cancelAddGeofence"
                        type="button"

                        class="rounded-xl border border-slate-300 px-6 py-3 font-medium text-slate-700 transition hover:bg-slate-100">

                        Batal

                    </button>

                    <button
                        id="submitAddGeofence"

                        type="submit"

                        class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 font-semibold text-white transition hover:bg-blue-700">

                        <svg
                            id="submitLoading"
                            class="hidden h-5 w-5 animate-spin"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24">

                            <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4">

                            </circle>

                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">

                            </path>

                        </svg>

                        <span
                            id="submitText">

                            Simpan Geofence

                        </span>

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

{{-- ========================================================= --}}
{{-- Script --}}
{{-- ========================================================= --}}

<script>
    document.addEventListener(

    'DOMContentLoaded',

    () => {

        /*
        |--------------------------------------------------------------------------
        | Element
        |--------------------------------------------------------------------------
        */

        /*
        |--------------------------------------------------------------------------
        | Escape Helper
        |--------------------------------------------------------------------------
        */

        function escapeHtml(value) {

            if (value === null || value === undefined) {

                return '';
            }

            return String(value).replace(/[&<>"']/g, function (char) {

                return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[char];

            });

        }

        const modal = document.getElementById(

            'addGeofenceModal'

        );

        const form = document.getElementById(

            'addGeofenceForm'

        );

        const openButton = document.getElementById(

            'addGeofence'

        );

        const closeButton = document.getElementById(

            'closeAddGeofence'

        );

        const cancelButton = document.getElementById(

            'cancelAddGeofence'

        );

        const submitButton = document.getElementById(

            'submitAddGeofence'

        );

        const submitText = document.getElementById(

            'submitText'

        );

        const submitLoading = document.getElementById(

            'submitLoading'

        );

        /*
        |--------------------------------------------------------------------------
        | Form
        |--------------------------------------------------------------------------
        */

        const deviceSelect = document.getElementById(

            'deviceSelect'

        );

        const radiusType = document.getElementById(

            'radiusType'

        );

        const administrativeType = document.getElementById(

            'administrativeType'

        );

        const radiusCard = document.getElementById(

            'radiusCard'

        );

        const administrativeCard = document.getElementById(

            'administrativeCard'

        );

        const customType = document.getElementById(

            'customType'

        );

        const customCard = document.getElementById(

            'customCard'

        );

        const customContainer = document.getElementById(

            'customContainer'

        );

        const customPolygonStatus = document.getElementById(

            'customPolygonStatus'

        );

        const customDrawingAction = document.getElementById(
            'customDrawingAction'
        );

        const redrawCustomPolygon = document.getElementById(
            'redrawCustomPolygon'
        );

        const cancelCustomPolygon = document.getElementById(
            'cancelCustomPolygon'
        );

        const startCustomDrawing = document.getElementById(

            'startCustomDrawing'

        );

        const radiusContainer = document.getElementById(

            'radiusContainer'

        );

        const administrativeContainer = document.getElementById(

            'administrativeContainer'

        );

        /*
        |--------------------------------------------------------------------------
        | Radius
        |--------------------------------------------------------------------------
        */

        const radiusHome = document.getElementById(

            'radiusHome'

        );

        const radiusCurrent = document.getElementById(

            'radiusCurrent'

        );

        const radiusValue = document.getElementById(

            'radiusValue'

        );

        const radiusUnit = document.getElementById(

            'radiusUnit'

        );

        const radiusCoordinate = document.getElementById(

            'radiusCoordinate'

        );

        const latitudePreview = document.getElementById(

            'radiusLatitudePreview'

        );

        const longitudePreview = document.getElementById(

            'radiusLongitudePreview'

        );

        /*
        |--------------------------------------------------------------------------
        | Administrative
        |--------------------------------------------------------------------------
        */

        const administrativeSearch = document.getElementById(

            'administrativeSearch'

        );

        const administrativeResult = document.getElementById(

            'administrativeResult'

        );

        const administrativeLoading = document.getElementById(

            'administrativeLoading'

        );

        const administrativeEmpty = document.getElementById(

            'administrativeEmpty'

        );

        const administrativeError = document.getElementById(

            'administrativeError'

        );

        const selectedAdministrative = document.getElementById(

            'selectedAdministrative'

        );

        const selectedAdministrativeName = document.getElementById(

            'selectedAdministrativeName'

        );

        const selectedAdministrativeType = document.getElementById(

            'selectedAdministrativeType'

        );

        const clearAdministrativeSearch = document.getElementById(

            'clearAdministrativeSearch'

        );

        /*
        |--------------------------------------------------------------------------
        | Hidden
        |--------------------------------------------------------------------------
        */

        const latitudeInput = document.getElementById(

            'geofenceLatitude'

        );

        const longitudeInput = document.getElementById(

            'geofenceLongitude'

        );

        const geojsonInput = document.getElementById(

            'geofenceGeojson'

        );

        const displayNameInput = document.getElementById(

            'geofenceDisplayName'

        );

        const administrativeTypeInput = document.getElementById(

            'geofenceAdministrativeType'

        );

        /*
        |--------------------------------------------------------------------------
        | State
        |--------------------------------------------------------------------------
        */

        let debounce = null;

        let searching = false;

        /*
        |--------------------------------------------------------------------------
        | Modal
        |--------------------------------------------------------------------------
        */

        function openModal() {

            modal.classList.remove(

                'hidden'

            );

            modal.classList.add(

                'flex'

            );

        }

        function closeModal() {

            modal.classList.remove(

                'flex'

            );

            modal.classList.add(

                'hidden'

            );

            resetForm();

        }

        function hideModal() {

            modal.classList.remove(

                'flex'

            );

            modal.classList.add(

                'hidden'

            );

        }

        openButton?.addEventListener(

            'click',

            openModal

        );

        closeButton?.addEventListener(

            'click',

            closeModal

        );

        cancelButton?.addEventListener(

            'click',

            closeModal

        );

        modal?.addEventListener(

            'click',

            e => {

                if (

                    e.target === modal

                ) {

                    closeModal();

                }

            }

        );

        /*
        |--------------------------------------------------------------------------
        | Custom Polygon Finished
        |--------------------------------------------------------------------------
        */

        document.addEventListener(

            'gpstracker:drawing-finished',

            event => {

                if (

                    !customType.checked

                ) {

                    return;

                }

                geojsonInput.value = JSON.stringify(

                    event.detail.geojson

                );

                customDrawingAction.classList.add(
                    'hidden'
                );

                openModal();

                customDrawingAction.classList.remove(
                    'hidden'
                );

                GPSTracker.previewCustom?.(
                    event.detail.geojson
                );

                customPolygonStatus.innerHTML =

                `
                <div class="font-semibold text-emerald-700">

                    ✓ Poligon berhasil dibuat

                </div>

                <div class="mt-2 text-sm text-slate-600">

                    Area siap disimpan atau digambar ulang.

                </div>
                `;

            }

        );

        /*
        |--------------------------------------------------------------------------
        | Reset
        |--------------------------------------------------------------------------
        */

        function resetForm() {

            form.reset();

            radiusType.checked = true;

            toggleType();

            latitudeInput.value = '';

            longitudeInput.value = '';

            geojsonInput.value = '';

            displayNameInput.value = '';

            administrativeTypeInput.value = '';

            latitudePreview.value = '';

            longitudePreview.value = '';

            radiusCoordinate.innerHTML =

                'Belum ada titik dipilih.';

            customPolygonStatus.textContent =
                'Belum ada poligon dibuat.';

            administrativeSearch.value = '';

            administrativeResult.innerHTML = '';

            administrativeResult.classList.add(

                'hidden'

            );

            selectedAdministrative.classList.add(

                'hidden'

            );

            administrativeEmpty.classList.add(

                'hidden'

            );

            administrativeError.classList.add(

                'hidden'

            );

            clearAdministrativeSearch.classList.add(

                'hidden'

            );

            if (

                window.GPSTracker

            ) {

                GPSTracker.removePreviewLayer?.();

                GPSTracker.cancelRadiusDrawing?.();

                GPSTracker.cancelAdministrativeDrawing?.();

                GPSTracker.cancelCustomDrawing?.();

            }

            customDrawingAction.classList.add(
                'hidden'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Toggle Type
        |--------------------------------------------------------------------------
        */

        function toggleType() {

            /*
            |--------------------------------------------------------------------------
            | Reset Card
            |--------------------------------------------------------------------------
            */

            radiusCard.classList.remove(

                'border-blue-600',
                'bg-blue-50'

            );

            administrativeCard.classList.remove(

                'border-blue-600',
                'bg-blue-50'

            );

            customCard.classList.remove(

                'border-blue-600',
                'bg-blue-50'

            );

            /*
            |--------------------------------------------------------------------------
            | Hide Container
            |--------------------------------------------------------------------------
            */

            radiusContainer.classList.add(

                'hidden'

            );

            administrativeContainer.classList.add(

                'hidden'

            );

            customContainer.classList.add(

                'hidden'

            );

            /*
            |--------------------------------------------------------------------------
            | Stop Drawing
            |--------------------------------------------------------------------------
            */

            if (window.GPSTracker) {

                GPSTracker.cancelRadiusDrawing?.();

                GPSTracker.cancelAdministrativeDrawing?.();

                GPSTracker.cancelCustomDrawing?.();

                GPSTracker.removePreviewLayer?.();

                geojsonInput.value = '';

                displayNameInput.value = '';

                administrativeTypeInput.value = '';

            }

            /*
            |--------------------------------------------------------------------------
            | Radius
            |--------------------------------------------------------------------------
            */

            if (radiusType.checked) {

                radiusContainer.classList.remove(
                    'hidden'
                );

                radiusCard.classList.add(
                    'border-blue-600',
                    'bg-blue-50'
                );

                updateRadiusSource();

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Administrative
            |--------------------------------------------------------------------------
            */

            if (administrativeType.checked) {

                administrativeContainer.classList.remove(

                    'hidden'

                );

                administrativeCard.classList.add(

                    'border-blue-600',
                    'bg-blue-50'

                );

                GPSTracker.startAdministrativeDrawing?.();

                return;

            }

            /*
            |--------------------------------------------------------------------------
            | Custom Polygon
            |--------------------------------------------------------------------------
            */

            if (customType.checked) {

                    customContainer.classList.remove(
                        'hidden'
                    );

                    customCard.classList.add(
                        'border-blue-600',
                        'bg-blue-50'
                    );

                    customPolygonStatus.textContent =
                        'Klik tombol "Mulai Menggambar".';

                }

        }

        radiusType.addEventListener(

            'change',

            toggleType

        );

        administrativeType.addEventListener(

            'change',

            toggleType

        );

        customType.addEventListener(

            'change',

            toggleType

        );

        startCustomDrawing?.addEventListener(

            'click',

            () => {

                GPSTracker.startCustomDrawing?.();

                hideModal();

                window.Toast?.info(
                    'Klik pada peta untuk membuat titik. Klik dua kali untuk selesai.'
                );

            }

        );

        redrawCustomPolygon?.addEventListener(

            'click',

            () => {

                GPSTracker.cancelCustomDrawing?.();

                geojsonInput.value = '';

                hideModal();

                GPSTracker.startCustomDrawing?.();

            }

        );

        cancelCustomPolygon?.addEventListener(

            'click',

            () => {

                GPSTracker.cancelCustomDrawing?.();

                geojsonInput.value = '';

                customDrawingAction.classList.add(
                    'hidden'
                );

                customPolygonStatus.textContent =
                    'Belum ada poligon dibuat.';

            }

        );
                /*
        |--------------------------------------------------------------------------
        | Device Changed
        |--------------------------------------------------------------------------
        */

        deviceSelect.addEventListener(

            'change',

            () => {

                latitudeInput.value = '';

                longitudeInput.value = '';

                latitudePreview.value = '';

                longitudePreview.value = '';

                radiusCoordinate.innerHTML =
                    'Belum ada titik dipilih.';

                if (radiusType.checked) {

                    updateRadiusSource();

                }

            }

        );

        /*
        |--------------------------------------------------------------------------
        | Radius Source
        |--------------------------------------------------------------------------
        */

        radiusHome.addEventListener(

            'change',

            updateRadiusSource

        );

        radiusCurrent.addEventListener(

            'change',

            updateRadiusSource

        );

        function updateRadiusSource() {

                const option =
                    deviceSelect.options[
                        deviceSelect.selectedIndex
                    ];

                if (

                    !option ||

                    !option.value

                ) {

                    latitudeInput.value = '';

                    longitudeInput.value = '';

                    latitudePreview.value = '';

                    longitudePreview.value = '';

                    radiusCoordinate.innerHTML =

                        'Silakan pilih kendaraan terlebih dahulu.';

                    return;

                }

                /*
                |--------------------------------------------------------------------------
                | Semua Kendaraan: tidak ada satu titik spesifik, tiap
                | kendaraan akan pakai titik home/GPS miliknya sendiri
                | saat disimpan di backend.
                |--------------------------------------------------------------------------
                */

                if (option.value === 'all') {

                    latitudeInput.value = '';

                    longitudeInput.value = '';

                    latitudePreview.value = '';

                    longitudePreview.value = '';

                    radiusCoordinate.innerHTML =

                        'Setiap kendaraan akan menggunakan titik '
                        + (radiusHome.checked ? 'Lokasi Rumah' : 'GPS terakhir')
                        + ' miliknya masing-masing.';

                    GPSTracker.removePreviewLayer?.();

                    return;

                }

                let lat = null;

                let lng = null;

                if (

                    radiusHome.checked

                ) {

                    lat = option.dataset.homeLat;

                    lng = option.dataset.homeLng;

                }

                else if (

                    radiusCurrent.checked

                ) {

                    lat = option.dataset.lastLat;

                    lng = option.dataset.lastLng;

                }

                else {

                    return;

                }
                if (

                    lat === '' ||

                    lng === ''

                ) {

                    latitudeInput.value = '';

                    longitudeInput.value = '';

                    latitudePreview.value = '';

                    longitudePreview.value = '';

                    radiusCoordinate.innerHTML =

                        'Koordinat belum tersedia.';

                    return;

                }

                setRadiusCoordinate(

                    lat,

                    lng

                );

                GPSTracker.removePreviewLayer?.();

                GPSTracker.previewRadiusDrawing?.(

                    Number(lat),

                    Number(lng),

                    getRadiusMeter()

                );

}

        /*
        |--------------------------------------------------------------------------
        | Radius Meter
        |--------------------------------------------------------------------------
        */

        function getRadiusMeter() {

            let radius =

                Number(

                    radiusValue.value

                );

            if (

                radiusUnit.value ===

                'kilometer'

            ) {

                radius *= 1000;

            }

            return radius;

        }

        /*
        |--------------------------------------------------------------------------
        | Radius Changed
        |--------------------------------------------------------------------------
        */

        radiusValue.addEventListener(

                    'input',

                    refreshRadiusPreview

                );

                radiusUnit.addEventListener(

                    'change',

                    refreshRadiusPreview

                );

                function refreshRadiusPreview() {

            if (

                !latitudeInput.value ||

                !longitudeInput.value

            ) {

                return;

            }

            GPSTracker.updateRadiusDrawing?.(

                Number(

                    latitudeInput.value

                ),

                Number(

                    longitudeInput.value

                ),

                getRadiusMeter()

            );

        }

        /*
        |--------------------------------------------------------------------------
        | Set Radius Coordinate
        |--------------------------------------------------------------------------
        */

        function setRadiusCoordinate(

            latitude,

            longitude

        ) {

            if (

                latitude === '' ||

                longitude === '' ||

                latitude === null ||

                longitude === null ||

                latitude === undefined ||

                longitude === undefined

            ) {

                return;

            }

            latitudeInput.value =

                latitude;

            longitudeInput.value =

                longitude;

            latitudePreview.value =

                Number(

                    latitude

                ).toFixed(6);

            longitudePreview.value =

                Number(

                    longitude

                ).toFixed(6);

            radiusCoordinate.innerHTML =

                `
                Lintang :
                <b>${Number(latitude).toFixed(6)}</b>

                <br>

                Bujur :
                <b>${Number(longitude).toFixed(6)}</b>
                `;

        }

        document.addEventListener(

            'gpstracker:drawing-started',

            () => {

                customPolygonStatus.innerHTML =

                `

                <div class="font-semibold text-blue-700">

                    Sedang menggambar poligon...

                </div>

                <div class="mt-2 text-sm text-slate-600">

                    Klik pada peta untuk membuat titik.<br>

                    Klik dua kali untuk menyelesaikan poligon.

                </div>

                `;

            }

        );

        /*
        |--------------------------------------------------------------------------
        | Administrative Search
        |--------------------------------------------------------------------------
        */

        administrativeSearch.addEventListener(

            'input',

            () => {

                clearTimeout(

                    debounce

                );

                const keyword =

                    administrativeSearch

                    .value

                    .trim();

                clearAdministrativeSearch.classList.toggle(

                    'hidden',

                    keyword.length === 0

                );

                if (

                    keyword.length < 3

                ) {

                    administrativeResult.innerHTML = '';

                    administrativeResult.classList.add(

                        'hidden'

                    );

                    administrativeEmpty.classList.add(

                        'hidden'

                    );

                    administrativeError.classList.add(

                        'hidden'

                    );

                    return;

                }

                debounce =

                    setTimeout(

                        () => {

                            searchAdministrative(

                                keyword

                            );

                        },

                        400

                    );

            }

        );
        
        async function searchAdministrative(keyword)
            {
                if (searching) {
                    return;
                }

                searching = true;

                administrativeLoading.classList.remove('hidden');
                administrativeError.classList.add('hidden');
                administrativeEmpty.classList.add('hidden');
                administrativeResult.classList.add('hidden');
                administrativeResult.innerHTML = '';

                try {

                    const [districtResponse, provinceResponse, regencyResponse] = await Promise.all([
                        fetch(
                            '/api/administrative/districts',
                            {
                                headers: {
                                    Accept: 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            }
                        ),
                        fetch(
                            '/api/administrative/provinces',
                            {
                                headers: {
                                    Accept: 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            }
                        ),
                        fetch(
                            '/api/administrative/regencies',
                            {
                                headers: {
                                    Accept: 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            }
                        ),
                    ]);

                    if (!districtResponse.ok) {
                        throw new Error('Gagal memuat daftar kecamatan.');
                    }

                    const response = await districtResponse.json();

                        if (!response.success) {

                            throw new Error(
                                response.message ??
                                'Gagal memuat daftar kecamatan.'
                            );

                        }

                    const districts = response.data ?? [];

                    let provinces = [];

                    if (provinceResponse.ok) {

                        const provinceResult = await provinceResponse.json();

                        if (provinceResult.success) {

                            provinces = provinceResult.data ?? [];

                        }

                    }

                    let regencies = [];

                    if (regencyResponse.ok) {

                        const regencyResult = await regencyResponse.json();

                        if (regencyResult.success) {

                            regencies = regencyResult.data ?? [];

                        }

                    }

                    const lowerKeyword = keyword.toLowerCase();

                    const results = [];

                    provinces.forEach(province => {

                        if (
                            province.name
                                .toLowerCase()
                                .includes(lowerKeyword)
                        ) {

                            results.push({

                                level: 'province',

                                code: province.code,

                                name: province.name,

                                type: 'Provinsi'

                            });

                        }

                    });

                    regencies.forEach(regency => {

                        if (
                            regency.name
                                .toLowerCase()
                                .includes(lowerKeyword)
                        ) {

                            results.push({

                                level: 'regency',

                                code: regency.code,

                                name: regency.name,

                                type: 'Kabupaten/Kota'

                            });

                        }

                    });

                    districts.forEach(district => {

                        if (
                            district.name
                                .toLowerCase()
                                .includes(lowerKeyword)
                        ) {

                            results.push({

                                level: 'district',

                                code: district.code,

                                name: district.name,

                                type: 'Kecamatan'

                            });

                        }

                        if (
                            Array.isArray(district.villages)
                        ) {

                            district.villages.forEach(village => {

                                if (
                                    village.name
                                        .toLowerCase()
                                        .includes(lowerKeyword)
                                ) {

                                    results.push({

                                        level: 'village',

                                        code: village.code,

                                        district_code: district.code,

                                        district_name: district.name,

                                        name: village.name,

                                        type: 'Kelurahan'

                                    });

                                }

                            });

                        }

                    });

                    renderAdministrative(results);

                }
                catch (error) {

                    console.error(error);

                    administrativeError.classList.remove(
                        'hidden'
                    );

                }
                finally {

                    searching = false;

                    administrativeLoading.classList.add(
                        'hidden'
                    );

                }
            }

        /*
        |--------------------------------------------------------------------------
        | Render Administrative
        |--------------------------------------------------------------------------
        */

        function renderAdministrative(results)
            {
                administrativeResult.innerHTML = '';

                if (
                    !Array.isArray(results) ||
                    results.length === 0
                ) {
                    administrativeEmpty.classList.remove(
                        'hidden'
                    );
                    return;
                }

                administrativeResult.classList.remove(
                    'hidden'
                );

                results.forEach(item => {

                    const button = document.createElement(
                        'button'
                    );

                    button.type = 'button';

                    button.className =
                        'block w-full border-b border-slate-100 px-5 py-4 text-left transition hover:bg-slate-50';

                    button.innerHTML = `
                        <div class="font-semibold text-slate-900">
                            ${escapeHtml(item.name)}
                        </div>

                        <div class="mt-1 text-sm text-slate-500">
                            ${escapeHtml(item.type)}
                        </div>

                        ${
                            item.district_name
                                ? `
                                <div class="mt-1 text-xs text-slate-400">
                                    Kecamatan :
                                    ${escapeHtml(item.district_name)}
                                </div>
                                `
                                : ''
                        }
                    `;

                    button.addEventListener(
                        'click',
                        () => {

                            selectAdministrative(item);

                        }
                    );

                    administrativeResult.appendChild(
                        button
                    );

                });

            }

        /*
        |--------------------------------------------------------------------------
        | Select Administrative
        |--------------------------------------------------------------------------
        */

        async function selectAdministrative(item)
            {
                try {

                    administrativeLoading.classList.remove('hidden');

                    const geometry = await loadAdministrativePolygon(
                        item.level,
                        item.code
                    );

                    administrativeSearch.value = item.name;

                    displayNameInput.value = item.name;

                    administrativeTypeInput.value = item.level;

                    geojsonInput.value = JSON.stringify(geometry);

                    selectedAdministrative.classList.remove('hidden');

                    selectedAdministrativeName.textContent = item.name;

                    selectedAdministrativeType.textContent = item.type;

                    administrativeResult.classList.add('hidden');

                    GPSTracker.previewAdministrative?.(
                        geometry
                    );

                }
                catch (error) {

                    console.error(error);

                    administrativeError.classList.remove('hidden');

                }
                finally {

                    administrativeLoading.classList.add('hidden');

                }
            }

        async function loadAdministrativePolygon(level, code)
        {
            const response = await fetch(

                `/api/administrative/geojson/${level}/${code}`,

                {

                    headers: {

                        Accept: 'application/json',

                        'X-Requested-With':
                            'XMLHttpRequest'

                    }

                }

            );

            if (!response.ok) {

                throw new Error(
                    'Gagal memuat wilayah administratif.'
                );

            }

            const result = await response.json();

            if (!result.success) {

                throw new Error(
                    result.message ??
                    'Gagal memuat wilayah administratif.'
                );

            }

            return result.data;

        }

        /*
        |--------------------------------------------------------------------------
        | Clear Administrative
        |--------------------------------------------------------------------------
        */

        clearAdministrativeSearch.addEventListener(

            'click',

            () => {

                administrativeSearch.value = '';

                displayNameInput.value = '';

                administrativeTypeInput.value = '';

                geojsonInput.value = '';

                administrativeResult.innerHTML = '';

                administrativeResult.classList.add(

                    'hidden'

                );

                selectedAdministrative.classList.add(

                    'hidden'

                );

                administrativeEmpty.classList.add(

                    'hidden'

                );

                administrativeError.classList.add(

                    'hidden'

                );

                clearAdministrativeSearch.classList.add(

                    'hidden'

                );

                GPSTracker.removePreviewLayer?.();

                GPSTracker.cancelAdministrativeDrawing?.();

            }

        );

        /*
        |--------------------------------------------------------------------------
        | Hide Result
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'click',
            event => {

                if (
                    administrativeResult.contains(event.target)
                ) {
                    return;
                }

                if (
                    administrativeSearch.contains(event.target)
                ) {
                    return;
                }

                administrativeResult.classList.add(
                    'hidden'
                );

            }
        );
        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        function clearValidation() {

            [

                'nameError',

                'descriptionError',

                'deviceError'

            ].forEach(

                id => {

                    const element = document.getElementById(

                        id

                    );

                    element.classList.add(

                        'hidden'

                    );

                    element.textContent = '';

                }

            );

        }

        function showError(

            id,

            message

        ) {

            const element = document.getElementById(

                id

            );

            if (

                !element

            ) {

                return;

            }

            element.textContent =

                message;

            element.classList.remove(

                'hidden'

            );

        }

        function validateForm() {

            clearValidation();

            let valid = true;

            if (

                document.getElementById(

                    'geofenceName'

                ).value.trim() === ''

            ) {

                showError(

                    'nameError',

                    'Nama geofence wajib diisi.'

                );

                valid = false;

            }

            if (

                deviceSelect.value === ''

            ) {

                showError(

                    'deviceError',

                    'Silakan pilih kendaraan.'

                );

                valid = false;

            }

            if (

                radiusType.checked &&

                deviceSelect.value !== 'all'

            ) {

                if (

                    latitudeInput.value === '' ||

                    longitudeInput.value === ''

                ) {

                    GPSTracker.showToast?.(

                        'error',

                        'Silakan tentukan titik radius.'

                    );

                    valid = false;

                }

            }

            if (

                administrativeType.checked

            ) {

                if (

                    geojsonInput.value === ''

                ) {

                    GPSTracker.showToast?.(

                        'error',

                        'Silakan pilih wilayah administratif terlebih dahulu.'

                    );

                    valid = false;

                }

            }

            if (customType.checked) {

                if (geojsonInput.value === '') {

                    GPSTracker.showToast?.(

                        'error',

                        'Silakan gambar poligon terlebih dahulu.'

                    );

                    valid = false;

                }

            }

            return valid;

        }

        /*
        |--------------------------------------------------------------------------
        | Loading
        |--------------------------------------------------------------------------
        */

        function startLoading() {

            submitButton.disabled = true;

            submitLoading.classList.remove(

                'hidden'

            );

            submitText.textContent =

                'Menyimpan...';

        }

        function stopLoading() {

            submitButton.disabled = false;

            submitLoading.classList.add(

                'hidden'

            );

            submitText.textContent =

                'Simpan Geofence';

        }

        /*
        |--------------------------------------------------------------------------
        | Submit
        |--------------------------------------------------------------------------
        */

        form.addEventListener(

            'submit',

            async function (

                event

            ) {

                event.preventDefault();

                if (

                    !validateForm()

                ) {

                    return;

                }

                startLoading();

                try {

                    const response = await fetch(

                        form.action,

                        {

                            method: 'POST',

                            headers: {

                                'Accept': 'application/json',

                                'X-CSRF-TOKEN':

                                    document

                                        .querySelector(

                                            'meta[name="csrf-token"]'

                                        )

                                        .content,

                            },

                            body: new FormData(

                                form

                            ),

                        }

                    );

                    const result =

                        await response.json();

                    if (

                        !response.ok

                    ) {

                        if (

                            result.errors

                        ) {

                            Object.entries(

                                result.errors

                            ).forEach(

                                ([

                                    key,

                                    value

                                ]) => {

                                    switch (

                                        key

                                    ) {

                                        case 'name':

                                            showError(

                                                'nameError',

                                                value[0]

                                            );

                                            break;

                                        case 'description':

                                            showError(

                                                'descriptionError',

                                                value[0]

                                            );

                                            break;

                                        case 'device_id':

                                            showError(

                                                'deviceError',

                                                value[0]

                                            );

                                            break;

                                    }

                                }

                            );

                            stopLoading();

                            return;

                        }

                        throw new Error(

                            result.message ??

                            'Terjadi kesalahan.'

                        );

                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Refresh GPSTracker
                    |--------------------------------------------------------------------------
                    */

                    if (window.GPSTracker) {

                        const deviceId = String(deviceSelect.value);

                        GPSTracker.focusVehicle(deviceId);

                        await GPSTracker.refreshGeofences();

                        closeModal();

                    }

                    GPSTracker.showToast(
                        'success',
                        'Berhasil',
                        result.message ?? 'Geofence berhasil ditambahkan.'
                    );

                }

                catch (

                    error

                ) {

                        console.error(

                            error

                        );

                        GPSTracker.showToast(
                            'error',
                            'Gagal',
                            error.message
                        );
                    }

                finally {

                    stopLoading();

                }

            }

        );

        /*
        |--------------------------------------------------------------------------
        | Geofence Availability (BR-01)
        |--------------------------------------------------------------------------
        | 1 kendaraan maksimal 1 geofence per tipe (radius / administrative /
        | custom). Tipe/kendaraan yang sudah lengkap dinonaktifkan supaya
        | tidak bisa ditambahkan lagi dari sini.
        |--------------------------------------------------------------------------
        */

        const GEOFENCE_TYPES = ['radius', 'administrative', 'custom'];

        let geofenceTypesByDevice = {};

        async function loadGeofenceAvailability() {

            try {

                const response = await fetch(
                    '/geofences/types',
                    {
                        headers: {
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    }
                );

                const result = await response.json();

                geofenceTypesByDevice = result.data ?? {};

            } catch (error) {

                console.error(error);

                geofenceTypesByDevice = {};

            }

            applyDeviceAvailability();

            applyTypeAvailability();

        }

        function deviceHasAllTypes(deviceId) {

            const types = geofenceTypesByDevice[deviceId] ?? [];

            return GEOFENCE_TYPES.every(
                type => types.includes(type)
            );

        }

        function applyDeviceAvailability() {

            let anyAvailable = false;

            Array.from(deviceSelect.options).forEach(option => {

                if (!option.value || option.value === 'all') {
                    return;
                }

                const exhausted = deviceHasAllTypes(option.value);

                option.disabled = exhausted;

                if (!exhausted) {
                    anyAvailable = true;
                }

            });

            const allOption = deviceSelect.querySelector(
                'option[value="all"]'
            );

            if (allOption) {
                allOption.disabled = !anyAvailable;
            }

            const addTrigger = document.getElementById('addGeofence');

            if (addTrigger) {

                addTrigger.disabled = !anyAvailable;

                addTrigger.classList.toggle('opacity-50', !anyAvailable);

                addTrigger.classList.toggle('cursor-not-allowed', !anyAvailable);

                addTrigger.title = anyAvailable
                    ? ''
                    : 'Semua kendaraan sudah memiliki seluruh tipe geofence';

            }

        }

        function isTypeAvailable(type) {

            const selected = deviceSelect.value;

            if (!selected) {
                return true;
            }

            if (selected === 'all') {

                return Array.from(deviceSelect.options).some(option => {

                    if (!option.value || option.value === 'all') {
                        return false;
                    }

                    const types = geofenceTypesByDevice[option.value] ?? [];

                    return !types.includes(type);

                });

            }

            const types = geofenceTypesByDevice[selected] ?? [];

            return !types.includes(type);

        }

        function applyTypeAvailability() {

            const availability = {

                radius: isTypeAvailable('radius'),

                administrative: isTypeAvailable('administrative'),

                custom: isTypeAvailable('custom'),

            };

            radiusType.disabled = !availability.radius;

            administrativeType.disabled = !availability.administrative;

            customType.disabled = !availability.custom;

            radiusCard.classList.toggle('opacity-40', !availability.radius);
            radiusCard.classList.toggle('cursor-not-allowed', !availability.radius);

            administrativeCard.classList.toggle('opacity-40', !availability.administrative);
            administrativeCard.classList.toggle('cursor-not-allowed', !availability.administrative);

            customCard.classList.toggle('opacity-40', !availability.custom);
            customCard.classList.toggle('cursor-not-allowed', !availability.custom);

            /*
            |--------------------------------------------------------------------------
            | Kalau tipe yang lagi dipilih ternyata tidak tersedia,
            | pindah otomatis ke tipe pertama yang masih tersedia.
            |--------------------------------------------------------------------------
            */

            const current =
                radiusType.checked ? 'radius'
                : administrativeType.checked ? 'administrative'
                : 'custom';

            if (!availability[current]) {

                const fallback = GEOFENCE_TYPES.find(
                    type => availability[type]
                );

                if (fallback === 'radius') radiusType.checked = true;
                if (fallback === 'administrative') administrativeType.checked = true;
                if (fallback === 'custom') customType.checked = true;

                toggleType();

            }

        }

        deviceSelect.addEventListener(
            'change',
            applyTypeAvailability
        );

        openButton?.addEventListener(
            'click',
            loadGeofenceAvailability
        );

        document.addEventListener(
            'gpstracker:geofence-created',
            loadGeofenceAvailability
        );

        document.addEventListener(
            'gpstracker:geofence-deleted',
            loadGeofenceAvailability
        );

        /*
        |--------------------------------------------------------------------------
        | Initialize
        |--------------------------------------------------------------------------
        */

        toggleType();

                if (deviceSelect.value) {

                    updateRadiusSource();

                }

                loadGeofenceAvailability();

            }

        );

</script>