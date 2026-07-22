{{-- resources/views/home/modals/delete-geofence.blade.php --}}

<div
    id="deleteGeofenceModal"
    class="fixed inset-0 z-[9999] hidden items-center justify-center bg-slate-950/50 backdrop-blur-sm">

    <div
        class="relative flex max-h-[92vh] w-full max-w-2xl flex-col overflow-hidden rounded-3xl bg-white shadow-2xl">

        {{-- ========================================================= --}}
        {{-- Header --}}
        {{-- ========================================================= --}}

        <div
            class="flex items-center justify-between border-b border-slate-200 px-8 py-6">

            <div>

                <h2
                    class="text-2xl font-bold text-slate-900">

                    Hapus Geofence

                </h2>

                <p
                    class="mt-2 text-sm text-slate-500">

                    Pilih kendaraan kemudian pilih geofence yang ingin dihapus.

                </p>

            </div>

            <button
                id="closeDeleteGeofence"
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
        {{-- Body --}}
        {{-- ========================================================= --}}

        <form
            id="deleteGeofenceForm"
            class="flex-1 overflow-y-auto">

            @csrf

            @method('DELETE')

            <div
                class="space-y-8 px-8 py-8">

                {{-- ========================================================= --}}
                {{-- Kendaraan --}}
                {{-- ========================================================= --}}

                <div>

                    <label
                        class="mb-2 block text-sm font-semibold text-slate-700">

                        Kendaraan

                    </label>

                    <select
                        id="deleteDevice"

                        name="device_id"

                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 transition focus:border-red-500 focus:outline-none focus:ring-4 focus:ring-red-100">

                        <option
                            value="">

                            -- Pilih Kendaraan --

                        </option>

                        @foreach($devices as $device)

                            <option
                                value="{{ $device['id'] }}">

                                {{ $device['vehicle_name'] }}

                                @if(!empty($device['plate_number']))

                                    ({{ $device['plate_number'] }})

                                @endif

                            </option>

                        @endforeach

                    </select>

                    <p
                        id="deleteDeviceError"
                        class="mt-2 hidden text-sm text-red-600">

                    </p>

                </div>

                {{-- ========================================================= --}}
                {{-- Geofence --}}
                {{-- ========================================================= --}}

                <div>

                    <div
                        class="mb-4 flex items-center justify-between">

                        <div>

                            <h3
                                class="font-semibold text-slate-900">

                                Daftar Geofence

                            </h3>

                            <p
                                class="mt-1 text-sm text-slate-500">

                                Setiap kendaraan hanya dapat memiliki satu Radius,
                                satu Administrative, dan satu Custom Polygon.

                            </p>

                        </div>

                    </div>

                    <div
                        id="deleteGeofenceLoading"
                        class="hidden rounded-2xl border border-slate-200 bg-slate-50 px-6 py-10 text-center">

                        <svg
                            class="mx-auto h-8 w-8 animate-spin text-red-600"
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

                        <div
                            class="mt-4 text-sm text-slate-500">

                            Memuat daftar geofence...

                        </div>

                    </div>

                    <div
                        id="deleteGeofenceEmpty"
                        class="hidden rounded-2xl border border-dashed border-slate-300 px-6 py-10 text-center">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="mx-auto h-10 w-10 text-slate-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 13h6m-3-3v6m8-4A9 9 0 113 12a9 9 0 0118 0z"/>

                        </svg>

                        <div
                            class="mt-4 font-medium text-slate-700">

                            Belum ada geofence.

                        </div>

                        <div
                            class="mt-1 text-sm text-slate-500">

                            Kendaraan ini belum memiliki geofence.

                        </div>

                    </div>

                    <div
                        id="deleteGeofenceList"
                        class="space-y-4">

                        {{-- AJAX Render --}}

                    </div>

                </div>

                {{-- ========================================================= --}}
                {{-- Information --}}
                {{-- ========================================================= --}}

                <div
                    class="rounded-2xl border border-amber-300 bg-amber-50 p-5">

                    <div
                        class="flex items-start gap-3">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="mt-0.5 h-5 w-5 text-amber-600"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01"/>

                        </svg>

                        <div>

                            <div
                                class="font-semibold text-amber-800">

                                Informasi

                            </div>

                            <ul
                                class="mt-2 space-y-1 text-sm text-amber-700">

                                <li>

                                    • Menghapus <b>Radius</b> tidak akan menghapus Administrative maupun Custom.

                                </li>

                                <li>

                                    • Menghapus <b>Administrative</b> tidak akan menghapus Radius maupun Custom.

                                </li>

                                <li>

                                    • Menghapus <b>Custom Polygon</b> tidak akan mempengaruhi geofence lainnya.

                                </li>

                                <li>

                                    • Setiap jenis geofence dihapus secara independen.

                                </li>

                            </ul>

                        </div>

                    </div>

                </div>
                                {{-- ========================================================= --}}
                {{-- Hidden --}}
                {{-- ========================================================= --}}

                <input
                    type="hidden"
                    id="deleteSelectedVehicle"
                    name="selected_vehicle">

            </div>

            {{-- ========================================================= --}}
            {{-- Footer --}}
            {{-- ========================================================= --}}

            <div
                class="flex items-center justify-between border-t border-slate-200 bg-white px-8 py-6">

                <div
                    id="deleteStatus"
                    class="text-sm text-slate-500">

                </div>

                <div
                    class="flex items-center gap-3">

                    <button
                        id="cancelDeleteGeofence"
                        type="button"

                        class="rounded-xl border border-slate-300 px-6 py-3 font-medium text-slate-700 transition hover:bg-slate-100">

                        Batal

                    </button>

                    <button
                        id="submitDeleteGeofence"
                        type="submit"

                        class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-6 py-3 font-semibold text-white transition hover:bg-red-700">

                        <svg
                            id="deleteLoading"
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
                            id="deleteSubmitText">

                            Hapus Geofence

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

        const modal = document.getElementById(

            'deleteGeofenceModal'

        );

        const form = document.getElementById(

            'deleteGeofenceForm'

        );

        const openButton = document.getElementById(

            'deleteGeofence'

        );

        const closeButton = document.getElementById(

            'closeDeleteGeofence'

        );

        const cancelButton = document.getElementById(

            'cancelDeleteGeofence'

        );

        const submitButton = document.getElementById(

            'submitDeleteGeofence'

        );

        const submitLoading = document.getElementById(

            'deleteLoading'

        );

        const submitText = document.getElementById(

            'deleteSubmitText'

        );

        const deviceSelect = document.getElementById(

            'deleteDevice'

        );

        const geofenceList = document.getElementById(

            'deleteGeofenceList'

        );

        const loading = document.getElementById(

            'deleteGeofenceLoading'

        );

        const empty = document.getElementById(

            'deleteGeofenceEmpty'

        );

        const status = document.getElementById(

            'deleteStatus'

        );

        const hiddenVehicle = document.getElementById(

            'deleteSelectedVehicle'

        );

        let loadingRequest = false;

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

            resetModal();

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

            event => {

                if (

                    event.target === modal

                ) {

                    closeModal();

                }

            }

        );

        /*
        |--------------------------------------------------------------------------
        | Reset
        |--------------------------------------------------------------------------
        */

        function resetModal() {

            form.reset();

            geofenceList.innerHTML = '';

            loading.classList.add(

                'hidden'

            );

            empty.classList.add(

                'hidden'

            );

            status.textContent = '';

            hiddenVehicle.value = '';

        }

        /*
        |--------------------------------------------------------------------------
        | Vehicle Changed
        |--------------------------------------------------------------------------
        */

        deviceSelect.addEventListener(

            'change',

            () => {

                hiddenVehicle.value =

                    deviceSelect.value;

                loadVehicleGeofence();

            }

        );
                /*
        |--------------------------------------------------------------------------
        | Load Geofence
        |--------------------------------------------------------------------------
        */

        async function loadVehicleGeofence() {

            if (

                !deviceSelect.value ||

                loadingRequest

            ) {

                geofenceList.innerHTML = '';

                empty.classList.add(

                    'hidden'

                );

                return;

            }

            loadingRequest = true;

            loading.classList.remove(

                'hidden'

            );

            empty.classList.add(

                'hidden'

            );

            geofenceList.innerHTML = '';

            status.textContent = '';

            try {

                const response = await fetch(

                    `/geofence/device/${deviceSelect.value}`,

                    {

                        headers: {

                            'Accept': 'application/json'

                        }

                    }

                );

                const result = await response.json();

                if (

                    !response.ok

                ) {

                    throw new Error(

                        result.message ??

                        'Gagal memuat geofence.'

                    );

                }

                renderGeofence(

                    result.data ?? []

                );

            }

            catch (

                error

            ) {

                console.error(

                    error

                );

                GPSTracker.showToast?.(

                    'error',

                    error.message

                );

            }

            finally {

                loading.classList.add(

                    'hidden'

                );

                loadingRequest = false;

            }

        }

        /*
        |--------------------------------------------------------------------------
        | Render
        |--------------------------------------------------------------------------
        */

        function renderGeofence(

            items

        ) {

            geofenceList.innerHTML = '';

            if (

                !items.length

            ) {

                empty.classList.remove(

                    'hidden'

                );

                return;

            }

            empty.classList.add(

                'hidden'

            );

            items.forEach(

                item => {

                    geofenceList.insertAdjacentHTML(

                        'beforeend',

                        createCard(

                            item

                        )

                    );

                }

            );

            status.textContent =

                `${items.length} geofence ditemukan`;

        }

        /*
        |--------------------------------------------------------------------------
        | Card
        |--------------------------------------------------------------------------
        */

        function createCard(

            item

        ) {

            let title = '';

            let description = '';

            let badge = '';

            switch (

                item.type

            ) {

                case 'radius':

                    title = 'Radius';

                    description =

                        `${item.radius} Meter`;

                    badge =

                        'bg-blue-100 text-blue-700';

                    break;

                case 'administrative':

                    title = 'Administrative';

                    description =

                        item.display_name;

                    badge =

                        'bg-emerald-100 text-emerald-700';

                    break;

                case 'custom':

                    title = 'Custom Polygon';

                    description =

                        'Polygon';

                    badge =

                        'bg-purple-100 text-purple-700';

                    break;

                default:

                    title =

                        item.type;

                    description = '';

                    badge =

                        'bg-slate-100 text-slate-700';

            }

            return `

                <label
                    class="flex cursor-pointer items-start gap-4 rounded-2xl border border-slate-200 p-5 transition hover:border-red-400 hover:bg-red-50">

                    <input
                        type="checkbox"
                        name="geofence_ids[]"
                        value="${item.id}"
                        class="mt-1 h-5 w-5 rounded border-slate-300 text-red-600 focus:ring-red-500">

                    <div
                        class="flex-1">

                        <div
                            class="flex items-center justify-between">

                            <div
                                class="font-semibold text-slate-900">

                                ${title}

                            </div>

                            <span
                                class="rounded-full px-3 py-1 text-xs font-semibold ${badge}">

                                ${title}

                            </span>

                        </div>

                        <div
                            class="mt-2 text-sm text-slate-500">

                            ${description}

                        </div>

                    </div>

                </label>

            `;

        }

        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        function validateDelete() {

            if (

                !deviceSelect.value

            ) {

                GPSTracker.showToast?.(

                    'error',

                    'Silakan pilih kendaraan.'

                );

                return false;

            }

            const checked = document.querySelectorAll(

                'input[name="geofence_ids[]"]:checked'

            );

            if (

                !checked.length

            ) {

                GPSTracker.showToast?.(

                    'error',

                    'Pilih minimal satu geofence.'

                );

                return false;

            }

            return true;

        }
                /*
        |--------------------------------------------------------------------------
        | Loading State
        |--------------------------------------------------------------------------
        */

        function startLoading() {

            submitButton.disabled = true;

            submitLoading.classList.remove(

                'hidden'

            );

            submitText.textContent =

                'Menghapus...';

        }

        function stopLoading() {

            submitButton.disabled = false;

            submitLoading.classList.add(

                'hidden'

            );

            submitText.textContent =

                'Hapus Geofence';

        }

        /*
        |--------------------------------------------------------------------------
        | Submit Delete
        |--------------------------------------------------------------------------
        */

        form.addEventListener(

            'submit',

            async function (

                event

            ) {

                event.preventDefault();

                if (

                    !validateDelete()

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

                                        .content

                            },

                            body: new FormData(

                                form

                            )

                        }

                    );

                    const result =

                        await response.json();

                    if (

                        !response.ok

                    ) {

                        throw new Error(

                            result.message ??

                            'Gagal menghapus geofence.'

                        );

                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Refresh GPSTracker
                    |--------------------------------------------------------------------------
                    */

                    await GPSTracker.loadGeofences?.();

                    await GPSTracker.refreshSidebarGeofence?.();

                    await GPSTracker.refreshVehicleGeofence?.();

                    await GPSTracker.refreshNotificationBadge?.();

                    /*
                    |--------------------------------------------------------------------------
                    | Reload List
                    |--------------------------------------------------------------------------
                    */

                    await loadVehicleGeofence();

                    /*
                    |--------------------------------------------------------------------------
                    | Success
                    |--------------------------------------------------------------------------
                    */

                    GPSTracker.showToast?.(

                        'success',

                        result.message ??

                        'Geofence berhasil dihapus.'

                    );

                    closeModal();

                }

                catch (

                    error

                ) {

                    console.error(

                        error

                    );

                    GPSTracker.showToast?.(

                        'error',

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
        | GPSTracker Events
        |--------------------------------------------------------------------------
        */

        window.addEventListener(

            'gpstracker:geofence-created',

            () => {

                if (

                    deviceSelect.value

                ) {

                    loadVehicleGeofence();

                }

            }

        );

        window.addEventListener(

            'gpstracker:geofence-deleted',

            () => {

                if (

                    deviceSelect.value

                ) {

                    loadVehicleGeofence();

                }

            }

        );

        window.addEventListener(

            'gpstracker:vehicle-changed',

            event => {

                if (

                    !event.detail?.device_id

                ) {

                    return;

                }

                deviceSelect.value =

                    event.detail.device_id;

                hiddenVehicle.value =

                    event.detail.device_id;

                loadVehicleGeofence();

            }

        );

        /*
        |--------------------------------------------------------------------------
        | Initialize
        |--------------------------------------------------------------------------
        */

        resetModal();

    }

);

</script>