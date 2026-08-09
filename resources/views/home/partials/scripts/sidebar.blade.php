<script>

document.addEventListener('gpstracker:map-ready', () => {

    /*
    |--------------------------------------------------------------------------
    | Sidebar State
    |--------------------------------------------------------------------------
    */

    GPSTracker.sidebar ??= {

        initialized: false,

        selectedVehicle: null,

        selectedCard: null,

        container: null,

        counter: null,

        emptyState: null,

        cards: new Map(),
        
        activateModal: null,

        vehicleModal: null,

    };

    /*
    |--------------------------------------------------------------------------
    | Sidebar Configuration
    |--------------------------------------------------------------------------
    */

    GPSTracker.sidebarConfig = {

        containerId: 'vehicle-container',

        listId: 'vehicle-list',

        counterId: 'vehicle-count',

        emptyStateId: 'vehicle-empty-state',

        cardClass: '.vehicle-card',

        activeClass: 'border-[#2563EB] ring-2 ring-blue-100 shadow-lg',

        inactiveClass: 'border-slate-200 shadow-sm',

        scrollBehavior: 'smooth',

        scrollBlock: 'center',

        activateUrl: '/home/devices/activate',
        
        vehicleUrl: '/home/vehicles',

    };

    /*
    |--------------------------------------------------------------------------
    | Sidebar Getter
    |--------------------------------------------------------------------------
    */

    GPSTracker.getSidebar = function () {

        return this.sidebar;

    };

    GPSTracker.getSidebarContainer = function () {

        if (!this.sidebar.container) {

            this.sidebar.container = document.getElementById(

                this.sidebarConfig.containerId

            );

        }

        return this.sidebar.container;

    };

    GPSTracker.getSidebarCounter = function () {

        if (!this.sidebar.counter) {

            this.sidebar.counter = document.getElementById(

                this.sidebarConfig.counterId

            );

        }

        return this.sidebar.counter;

    };

    GPSTracker.getSidebarEmptyState = function () {

        if (!this.sidebar.emptyState) {

            this.sidebar.emptyState = document.getElementById(

                this.sidebarConfig.emptyStateId

            );

        }

        return this.sidebar.emptyState;

    };

    GPSTracker.getVehicleCards = function () {

        return document.querySelectorAll(

            this.sidebarConfig.cardClass

        );

    };

    GPSTracker.getVehicleCard = function (deviceId) {

        return document.getElementById(

            `vehicle-${deviceId}`

        );

    };

    GPSTracker.getSelectedVehicle = function () {

        return this.sidebar.selectedVehicle;

    };

    /*
    |--------------------------------------------------------------------------
    | Modal Getter
    |--------------------------------------------------------------------------
    */

    GPSTracker.getActivateDeviceModal = function () {

        if (!this.sidebar.activateModal) {

            this.sidebar.activateModal = document.getElementById(
                'activateDeviceModal'
            );

        }

        return this.sidebar.activateModal;

    };

    GPSTracker.getVehicleInformationModal = function () {

        if (!this.sidebar.vehicleModal) {

            this.sidebar.vehicleModal = document.getElementById(
                'vehicleInformationModal'
            );

        }

        return this.sidebar.vehicleModal;

    };

    /*
    |--------------------------------------------------------------------------
    | Sidebar Setter
    |--------------------------------------------------------------------------
    */

    GPSTracker.setSelectedVehicle = function (deviceId = null) {

        this.sidebar.selectedVehicle = deviceId;

    };

    GPSTracker.setSelectedCard = function (card = null) {

        this.sidebar.selectedCard = card;

    };

    GPSTracker.setSidebarInitialized = function (status = true) {

        this.sidebar.initialized = Boolean(status);

    };

    /*
    |--------------------------------------------------------------------------
    | Sidebar Checker
    |--------------------------------------------------------------------------
    */

    GPSTracker.hasVehicleCard = function (deviceId) {

        return this.getVehicleCard(

            deviceId

        ) !== null;

    };

    GPSTracker.isSidebarInitialized = function () {

        return this.sidebar.initialized;

    };

    /*
    |--------------------------------------------------------------------------
    | Vehicle Collection
    |--------------------------------------------------------------------------
    */

    GPSTracker.getVehicleCollection = function () {

        if (!Array.isArray(this.vehicles)) {

            this.vehicles = [];

        }

        return this.vehicles;

    };

    GPSTracker.findVehicle = function (deviceId) {

        return this.getVehicleCollection().find(vehicle => {

            return String(vehicle.device_id) === String(deviceId);

        });

    };

    GPSTracker.hasVehicle = function (deviceId) {

        return !!this.findVehicle(

            deviceId

        );

    };

    GPSTracker.replaceVehicle = function (vehicle) {

        const vehicles = this.getVehicleCollection();

        const index = vehicles.findIndex(item => {

            return String(item.device_id) === String(vehicle.device_id);

        });

        if (index === -1) {

            vehicles.push(vehicle);

            return;

        }

        vehicles[index] = {

            ...vehicles[index],

            ...vehicle,

        };

    };

    /*
    |--------------------------------------------------------------------------
    | Modal Handler
    |--------------------------------------------------------------------------
    */

    GPSTracker.openActivateDeviceModal = function () {

        const modal = this.getActivateDeviceModal();

        if (!modal) {

            return;

        }

        modal.classList.remove('hidden');

        modal.classList.add('flex');

    };

    GPSTracker.closeActivateDeviceModal = function () {

        const modal = this.getActivateDeviceModal();

        if (!modal) {

            return;

        }

        modal.classList.add('hidden');

        modal.classList.remove('flex');

    };

    GPSTracker.openVehicleInformationModal = function () {

        const modal = this.getVehicleInformationModal();

        if (!modal) {

            return;

        }

        modal.classList.remove('hidden');

        modal.classList.add('flex');

    };

    GPSTracker.closeVehicleInformationModal = function () {

        const modal = this.getVehicleInformationModal();

        if (!modal) {

            return;

        }

        modal.classList.add('hidden');

        modal.classList.remove('flex');

    };

    /*
    |--------------------------------------------------------------------------
    | Sidebar Logger
    |--------------------------------------------------------------------------
    */

    GPSTracker.sidebarLog = function (...message) {

        console.log(

            '[Sidebar]',

            ...message

        );

    };

    GPSTracker.sidebarWarn = function (...message) {

        console.warn(

            '[Sidebar]',

            ...message

        );

    };

    GPSTracker.sidebarError = function (...message) {

        console.error(

            '[Sidebar]',

            ...message

        );

    };
        /*
    |--------------------------------------------------------------------------
    | Vehicle Counter
    |--------------------------------------------------------------------------
    */

    GPSTracker.getVehicleCount = function () {

        return this.getVehicleCollection().length;

    };

    GPSTracker.updateVehicleCounter = function () {

        const counter = this.getSidebarCounter();

        if (!counter) {

            return;

        }

        const total = this.getVehicleCount();

        counter.textContent =

            `${total} Kendaraan Terhubung`;

    };

    /*
    |--------------------------------------------------------------------------
    | Empty State
    |--------------------------------------------------------------------------
    */

    GPSTracker.toggleSidebarEmptyState = function () {

        const emptyState = this.getSidebarEmptyState();

        if (!emptyState) {

            return;

        }

        emptyState.classList.toggle(

            'hidden',

            this.getVehicleCount() > 0

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Vehicle Card Element
    |--------------------------------------------------------------------------
    */

    GPSTracker.createVehicleCard = function (vehicle) {

        const wrapper = document.createElement('div');

        wrapper.innerHTML = this.renderVehicleCard(

            vehicle

        );

        return wrapper.firstElementChild;

    };

    GPSTracker.appendVehicleCard = function (vehicle) {

        const container = this.getSidebarContainer();

        if (!container) {

            return null;

        }

        if (

            this.hasVehicleCard(

                vehicle.device_id

            )

        ) {

            return this.getVehicleCard(

                vehicle.device_id

            );

        }

        const card = this.createVehicleCard(

            vehicle

        );

        container.appendChild(

            card

        );

        this.sidebar.cards.set(

            String(vehicle.device_id),

            card

        );

        this.updateVehicleCounter();

        this.toggleSidebarEmptyState();

        return card;

    };

    /*
    |--------------------------------------------------------------------------
    | Remove Vehicle Card
    |--------------------------------------------------------------------------
    */

    GPSTracker.removeVehicleCard = function (deviceId) {

        const card = this.getVehicleCard(deviceId);

        if (!card) {

            return;

        }

        card.remove();

        this.sidebar.cards.delete(

            String(deviceId)

        );

        this.updateVehicleCounter();

        this.toggleSidebarEmptyState();

    };

    /*
    |--------------------------------------------------------------------------
    | Replace Vehicle Card
    |--------------------------------------------------------------------------
    */

    GPSTracker.replaceVehicleCard = function (vehicle) {

        const card = this.getVehicleCard(

            vehicle.device_id

        );

        if (!card) {

            return this.appendVehicleCard(

                vehicle

            );

        }

        this.updateVehicleCard(

            vehicle

        );

        return card;

    };

    /*
    |--------------------------------------------------------------------------
    | Render Vehicle Card
    |--------------------------------------------------------------------------
    */

    GPSTracker.renderVehicleCard = function (vehicle) {

        const online = Boolean(vehicle.is_active);

        const statusClass = online
            ? 'bg-green-100 text-green-700'
            : 'bg-red-100 text-red-600';

        const statusText = online
            ? 'Online'
            : 'Offline';

        const hasCoordinate =
            vehicle.latitude !== null &&
            vehicle.latitude !== undefined &&
            vehicle.longitude !== null &&
            vehicle.longitude !== undefined;

        const mapButtonClass = hasCoordinate
            ? 'bg-[#2563EB] text-white hover:bg-blue-700'
            : 'cursor-not-allowed bg-slate-300 text-slate-500';

        const mapButtonText = hasCoordinate
            ? 'Lihat di Peta'
            : 'Belum Ada GPS';

        const mapButtonDisabled = hasCoordinate
            ? ''
            : 'disabled';

        return `

        <div
            id="vehicle-${vehicle.device_id}"
            class="vehicle-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-200 hover:border-[#2563EB] hover:shadow-lg"
            data-device="${vehicle.device_id}"
            data-code="${vehicle.device_code ?? ''}"
            data-lat="${vehicle.latitude ?? ''}"
            data-lng="${vehicle.longitude ?? ''}"
            data-selected="false">

            <div class="flex items-start justify-between">

                <div>

                    <h3
                        id="vehicle-name-${vehicle.device_id}"
                        class="text-lg font-bold text-slate-900">

                        ${vehicle.vehicle_name ?? '-'}

                    </h3>

                    <p
                        id="vehicle-plate-${vehicle.device_id}"
                        class="mt-1 text-sm text-slate-500">

                        ${(vehicle.plate_number ?? '-').toUpperCase()}

                    </p>

                </div>

                <span
                    id="vehicle-status-${vehicle.device_id}"
                    data-status
                    data-online="${online ? 1 : 0}"
                    class="rounded-full px-3 py-1 text-xs font-semibold ${statusClass}">

                    ${statusText}

                </span>

            </div>

            <div class="mt-5 grid grid-cols-2 gap-4">

                <div>

                    <p class="text-xs uppercase tracking-wide text-slate-400">

                        Kecepatan

                    </p>

                    <p
                        id="vehicle-speed-${vehicle.device_id}"
                        data-speed
                        class="mt-1 text-base font-semibold text-slate-900">

                        ${vehicle.speed ?? 0} km/j

                    </p>

                </div>

                <div>

                    <p class="text-xs uppercase tracking-wide text-slate-400">

                        Jenis

                    </p>

                    <p
                        id="vehicle-type-${vehicle.device_id}"
                        data-type
                        class="mt-1 text-base font-semibold text-slate-900">

                        ${vehicle.vehicle_type ?? '-'}

                    </p>

                </div>

            </div>

            <div class="mt-5 grid grid-cols-2 gap-4">

                <div>

                    <p class="text-xs uppercase tracking-wide text-slate-400">

                        Baterai

                    </p>

                    <p
                        id="vehicle-battery-${vehicle.device_id}"
                        data-battery
                        class="mt-1 text-base font-semibold text-slate-900">

                        ${vehicle.battery ?? '-'}

                    </p>

                </div>

                <div>

                    <p class="text-xs uppercase tracking-wide text-slate-400">

                        Update

                    </p>

                    <p
                        id="vehicle-updated-${vehicle.device_id}"
                        data-updated
                        class="mt-1 text-sm font-medium text-slate-500">

                        ${vehicle.updated_at ?? '-'}

                    </p>

                </div>

            </div>

            <div class="mt-6 flex items-center gap-2">

                <button
                    type="button"
                    onclick="GPSTracker.focusVehicle('${vehicle.device_id}')"
                    ${mapButtonDisabled}
                    class="flex-1 rounded-xl px-4 py-2.5 text-sm font-semibold transition ${mapButtonClass}">

                    ${mapButtonText}

                </button>

                <a
                    href="/vehicles/${vehicle.device_id}"
                    class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">

                    Detail

                </a>

            </div>

        </div>

        `;

    };
        /*
    |--------------------------------------------------------------------------
    | Update Vehicle Card
    |--------------------------------------------------------------------------
    */

    GPSTracker.updateVehicleCard = function (vehicle) {

        const card = this.getVehicleCard(vehicle.device_id);

        if (!card) {

            this.appendVehicleCard(vehicle);

            return;

        }

        /*
        |--------------------------------------------------------------------------
        | Dataset
        |--------------------------------------------------------------------------
        */

        this.updateVehicleCardData(card, vehicle);

        /*
        |--------------------------------------------------------------------------
        | Header
        |--------------------------------------------------------------------------
        */

        this.updateVehicleCardHeader(vehicle);

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        this.updateVehicleCardStatus(vehicle);

        /*
        |--------------------------------------------------------------------------
        | Information
        |--------------------------------------------------------------------------
        */

        this.updateVehicleCardInformation(vehicle);

        /*
        |--------------------------------------------------------------------------
        | Map Button
        |--------------------------------------------------------------------------
        */

        const button = card.querySelector(
            'button[onclick*="focusVehicle"]'
        );

        if (!button) {

            return;

        }

        const hasCoordinate =
            vehicle.latitude !== null &&
            vehicle.latitude !== undefined &&
            vehicle.longitude !== null &&
            vehicle.longitude !== undefined;

        /*
        |--------------------------------------------------------------------------
        | Dataset Position
        |--------------------------------------------------------------------------
        */

        card.dataset.lat = hasCoordinate
            ? vehicle.latitude
            : '';

        card.dataset.lng = hasCoordinate
            ? vehicle.longitude
            : '';

        /*
        |--------------------------------------------------------------------------
        | Enable / Disable
        |--------------------------------------------------------------------------
        */

        button.disabled = !hasCoordinate;

        button.textContent = hasCoordinate
            ? 'Lihat di Peta'
            : 'Belum Ada GPS';

        button.classList.remove(
            'bg-[#2563EB]',
            'hover:bg-blue-700',
            'text-white',
            'bg-slate-300',
            'text-slate-500',
            'cursor-not-allowed'
        );

        if (hasCoordinate) {

            button.classList.add(
                'bg-[#2563EB]',
                'text-white',
                'hover:bg-blue-700'
            );

        } else {

            button.classList.add(
                'bg-slate-300',
                'text-slate-500',
                'cursor-not-allowed'
            );

        }

    };

    /*
    |--------------------------------------------------------------------------
    | Update Card Dataset
    |--------------------------------------------------------------------------
    */

    GPSTracker.updateVehicleCardData = function (card, vehicle) {

        card.dataset.device = vehicle.device_id;

        card.dataset.code = vehicle.device_code ?? '';

        card.dataset.lat = vehicle.latitude ?? '';

        card.dataset.lng = vehicle.longitude ?? '';

    };

    /*
    |--------------------------------------------------------------------------
    | Update Header
    |--------------------------------------------------------------------------
    */

    GPSTracker.updateVehicleCardHeader = function (vehicle) {

        const name = document.getElementById(

            `vehicle-name-${vehicle.device_id}`

        );

        const plate = document.getElementById(

            `vehicle-plate-${vehicle.device_id}`

        );

        if (name) {

            name.textContent =

                vehicle.vehicle_name ?? '-';

        }

        if (plate) {

            plate.textContent =

                (vehicle.plate_number ?? '-').toUpperCase();

        }

    };

    /*
    |--------------------------------------------------------------------------
    | Update Status
    |--------------------------------------------------------------------------
    */

    GPSTracker.updateVehicleCardStatus = function (vehicle) {

        const badge = document.getElementById(

            `vehicle-status-${vehicle.device_id}`

        );

        if (!badge) {

            return;

        }

        const online = Boolean(

            vehicle.is_active

        );

        badge.dataset.online = online ? 1 : 0;

        badge.textContent =

            online ? 'Online' : 'Offline';

        badge.classList.remove(

            'bg-green-100',

            'text-green-700',

            'bg-red-100',

            'text-red-600'

        );

        badge.classList.add(

            online

                ? 'bg-green-100'

                : 'bg-red-100',

            online

                ? 'text-green-700'

                : 'text-red-600'

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Update Information
    |--------------------------------------------------------------------------
    */

    GPSTracker.updateVehicleCardInformation = function (vehicle) {

        this.updateVehicleSpeed(

            vehicle

        );

        this.updateVehicleType(

            vehicle

        );

        this.updateVehicleBattery(

            vehicle

        );

        this.updateVehicleUpdatedTime(

            vehicle

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Update Speed
    |--------------------------------------------------------------------------
    */

    GPSTracker.updateVehicleSpeed = function (vehicle) {

        const element = document.getElementById(

            `vehicle-speed-${vehicle.device_id}`

        );

        if (!element) {

            return;

        }

        element.textContent =

            `${vehicle.speed ?? 0} km/j`;

    };

    /*
    |--------------------------------------------------------------------------
    | Update Type
    |--------------------------------------------------------------------------
    */

    GPSTracker.updateVehicleType = function (vehicle) {

        const element = document.getElementById(

            `vehicle-type-${vehicle.device_id}`

        );

        if (!element) {

            return;

        }

        element.textContent =

            vehicle.vehicle_type ?? '-';

    };

    /*
    |--------------------------------------------------------------------------
    | Update Battery
    |--------------------------------------------------------------------------
    */

    GPSTracker.updateVehicleBattery = function (vehicle) {

        const element = document.getElementById(

            `vehicle-battery-${vehicle.device_id}`

        );

        if (!element) {

            return;

        }

        element.textContent =

            vehicle.battery ?? '-';

    };

    /*
    |--------------------------------------------------------------------------
    | Update Updated Time
    |--------------------------------------------------------------------------
    */

    GPSTracker.updateVehicleUpdatedTime = function (vehicle) {

        const element = document.getElementById(

            `vehicle-updated-${vehicle.device_id}`

        );

        if (!element) {

            return;

        }

        element.textContent =

            vehicle.updated_at ?? '-';

    };
        /*
    |--------------------------------------------------------------------------
    | Update Vehicle Position
    |--------------------------------------------------------------------------
    */

    GPSTracker.updateVehiclePosition = function (vehicle) {

        const card = this.getVehicleCard(

            vehicle.device_id

        );

        if (!card) {

            return;

        }

        card.dataset.lat = vehicle.latitude ?? '';

        card.dataset.lng = vehicle.longitude ?? '';

    };

    /*
    |--------------------------------------------------------------------------
    | Update Vehicle Data
    |--------------------------------------------------------------------------
    */

    GPSTracker.updateVehicleData = function (vehicle) {

        this.replaceVehicle(

            vehicle

        );

        this.updateVehicleCard(

            vehicle

        );

        this.updateVehiclePosition(

            vehicle

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Select Vehicle
    |--------------------------------------------------------------------------
    */

    GPSTracker.selectVehicle = function (deviceId) {

        this.clearSelectedVehicle();

        const card = this.getVehicleCard(

            deviceId

        );

        if (!card) {

            return;

        }

        this.setSelectedVehicle(

            deviceId

        );

        this.setSelectedCard(

            card

        );

        card.dataset.selected = 'true';

        card.classList.remove(

            'border-slate-200',

            'shadow-sm'

        );

        this.sidebarConfig.activeClass

            .split(' ')

            .forEach(className => {

                card.classList.add(

                    className

                );

            });

    };

    /*
    |--------------------------------------------------------------------------
    | Clear Selected Vehicle
    |--------------------------------------------------------------------------
    */

    GPSTracker.clearSelectedVehicle = function () {

        const card = this.sidebar.selectedCard;

        if (!card) {

            return;

        }

        card.dataset.selected = 'false';

        this.sidebarConfig.activeClass

            .split(' ')

            .forEach(className => {

                card.classList.remove(

                    className

                );

            });

        this.sidebarConfig.inactiveClass

            .split(' ')

            .forEach(className => {

                card.classList.add(

                    className

                );

            });

        this.sidebar.selectedCard = null;

        this.sidebar.selectedVehicle = null;

    };

    /*
    |--------------------------------------------------------------------------
    | Highlight Vehicle
    |--------------------------------------------------------------------------
    */

    GPSTracker.highlightVehicleCard = function (deviceId) {

        this.selectVehicle(

            deviceId

        );

        this.scrollVehicleIntoView(

            deviceId

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Scroll Into View
    |--------------------------------------------------------------------------
    */

    GPSTracker.scrollVehicleIntoView = function (deviceId) {

        const card = this.getVehicleCard(

            deviceId

        );

        if (!card) {

            return;

        }

        card.scrollIntoView({

            behavior:

                this.sidebarConfig.scrollBehavior,

            block:

                this.sidebarConfig.scrollBlock,

        });

    };

    /*
    |--------------------------------------------------------------------------
    | Focus Vehicle
    |--------------------------------------------------------------------------
    */

    GPSTracker.focusVehicle = function (deviceId) {

        const vehicle = this.findVehicle(

            deviceId

        );

        if (!vehicle) {

            return;

        }

        this.setSelectedVehicle(deviceId);

        this.highlightVehicleCard(

            deviceId

        );

        if (

            typeof this.focusMarker === 'function'

        ) {

            this.focusMarker(

                deviceId

            );

        }

        if (

            typeof this.openMarkerPopup === 'function'

        ) {

            this.openMarkerPopup(

                deviceId

            );

        }

    };

    GPSTracker.handleActivateSuccess = async function (result) {

        document
            .getElementById('activateDeviceForm')
            ?.reset();

        this.showToast(

            'success',

            'Aktivasi Berhasil',

            result.message

        );

        await new Promise(resolve => {

            setTimeout(resolve, 1200);

        });

        this.closeActivateDeviceModal();

        this.openVehicleInformationModal();

    };

    GPSTracker.handleActivateValidation = function (result) {

    let message = 'Aktivasi perangkat gagal.';

    if (result.errors) {

        message = Object
            .values(result.errors)
            .flat()
            .join('\n');

    } else if (result.message) {

        message = result.message;

    }

    this.showToast(

            'error',

            'Aktivasi Gagal',

            message

        );

    };

    GPSTracker.handleActivateError = function () {

        this.showToast(

            'error',

            'Server Error',

            'Terjadi kesalahan pada server.'

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Activate Device
    |--------------------------------------------------------------------------
    */

    GPSTracker.submitActivateDevice = async function () {

        const form = document.getElementById(
            'activateDeviceForm'
        );

        if (!form) {

            return;

        }

        const button = form.querySelector(
            'button[type="submit"]'
        );

        const formData = new FormData(form);

        button.disabled = true;

        button.innerHTML = `
            <div class="flex items-center justify-center gap-2">
                <svg
                    class="h-5 w-5 animate-spin"
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

                <span>Mengaktivasi...</span>
            </div>
        `;

        try {

            const response = await fetch(

                this.sidebarConfig.activateUrl,

                {

                    method: 'POST',

                    headers: {

                        'X-CSRF-TOKEN': document
                            .querySelector('meta[name="csrf-token"]')
                            .content,

                        'Accept': 'application/json',

                        'X-Requested-With': 'XMLHttpRequest',

                    },

                    body: formData,

                }

            );

            const result = await response.json();

            /*
            |--------------------------------------------------------------------------
            | Validation / Business Error
            |--------------------------------------------------------------------------
            */

            if (!response.ok || result.success === false) {

                this.handleActivateValidation(result);

                return;

            }

            /*
            |--------------------------------------------------------------------------
            | Success
            |--------------------------------------------------------------------------
            */

            await this.handleActivateSuccess(result);

        } catch (error) {

            console.error(error);

            this.handleActivateError(error);

        } finally {

            button.disabled = false;

            button.innerHTML = 'Aktivasi';

        }

};

    /*
    |--------------------------------------------------------------------------
    | Vehicle Information
    |--------------------------------------------------------------------------
    */

    GPSTracker.submitVehicleInformation = async function () {

        const form = document.getElementById(
            'vehicleInformationForm'
        );

        if (!form) {

            return;

        }

        const button = form.querySelector(
            'button[type="submit"]'
        );

        const formData = new FormData(form);

        button.disabled = true;

        button.innerHTML = `
            <div class="flex items-center justify-center gap-2">
                <svg
                    class="h-5 w-5 animate-spin"
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

                <span>Menyimpan...</span>
                </div>
            `;

            try {

                const response = await fetch(

                    this.sidebarConfig.vehicleUrl,

                    {

                        method: 'POST',

                        headers: {

                            'X-CSRF-TOKEN': document
                                .querySelector('meta[name="csrf-token"]')
                                .content,

                            'Accept': 'application/json',

                            'X-Requested-With': 'XMLHttpRequest',

                        },

                        body: formData,

                    }

                );

                const result = await response.json();

                if (!response.ok || result.success === false) {

                    this.handleVehicleValidation(result);

                    return;

                }

                await this.handleVehicleSuccess(result);

            } catch (error) {

                console.error(error);

                this.handleVehicleError(error);

            } finally {

                button.disabled = false;

                button.innerHTML = 'Simpan Kendaraan';

            }

    };

    GPSTracker.handleVehicleSuccess = async function (result) {

    const vehicle = result.vehicle;

    this.showToast(

        'success',

        'Berhasil',

        'Informasi kendaraan berhasil disimpan.'

    );

    await new Promise(resolve => {

        setTimeout(resolve, 1200);

    });

    document
        .getElementById(
            'vehicleInformationForm'
        )
        ?.reset();

        this.replaceVehicle(

            vehicle

        );

        this.replaceVehicleCard(

            vehicle

        );

        this.refreshSidebarEvents();

        this.highlightVehicleCard(

            vehicle.device_id

        );

        this.closeVehicleInformationModal();

    };

    GPSTracker.handleVehicleValidation = function (result) {

        let message = 'Informasi kendaraan gagal disimpan.';

        if (result.errors) {

            message = Object
                .values(result.errors)
                .flat()
                .join('\n');

        } else if (result.message) {

            message = result.message;

        }

        this.showToast(

            'error',

            'Gagal',

            message

        );

    };

    GPSTracker.handleVehicleError = function () {

        this.showToast(

            'error',

            'Server Error',

            'Terjadi kesalahan pada server.'

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Sidebar Click Event
    |--------------------------------------------------------------------------
    */

    GPSTracker.bindSidebarCardEvents = function () {

        this.getVehicleCards().forEach(card => {

            if (card.dataset.sidebarBound === 'true') {

                return;

            }

            card.dataset.sidebarBound = 'true';

            card.addEventListener(

                'click',

                () => {

                    const deviceId =

                        card.dataset.device;

                    this.focusVehicle(

                        deviceId

                    );

                }

            );

        });

    };

    /*
    |--------------------------------------------------------------------------
    | Rebind Sidebar Events
    |--------------------------------------------------------------------------
    */

    GPSTracker.refreshSidebarEvents = function () {

        this.bindSidebarCardEvents();

    };

    /*
    |--------------------------------------------------------------------------
    | Refresh Sidebar
    |--------------------------------------------------------------------------
    */

    GPSTracker.refreshSidebar = function () {

        this.updateVehicleCounter();

        this.toggleSidebarEmptyState();

        this.refreshSidebarEvents();

    };

    /*
    |--------------------------------------------------------------------------
    | Reset Sidebar
    |--------------------------------------------------------------------------
    */

    GPSTracker.resetSidebar = function () {

        this.clearSelectedVehicle();

        this.updateVehicleCounter();

        this.toggleSidebarEmptyState();

    };

    /*
    |--------------------------------------------------------------------------
    | Destroy Sidebar
    |--------------------------------------------------------------------------
    */

    GPSTracker.destroySidebar = function () {

        this.resetSidebar();

        this.sidebar.cards.clear();

        this.sidebar.container = null;

        this.sidebar.counter = null;

        this.sidebar.emptyState = null;

        this.sidebar.selectedCard = null;

        this.sidebar.selectedVehicle = null;

        this.sidebar.activateModal = null;

        this.sidebar.vehicleModal = null;

        document.getElementById(

            'closeActivateDevice'

        )?.removeAttribute(

            'data-sidebar-bound'

        );

        document.getElementById(

            'cancelActivateDevice'

        )?.removeAttribute(

            'data-sidebar-bound'

        );

        document.getElementById(

            'addVehicle'

        )?.removeAttribute(

            'data-sidebar-bound'

        );

        document.getElementById(

            'activateDeviceForm'

        )?.removeAttribute(

            'data-sidebar-bound'

        );

        document.getElementById(

            'vehicleInformationForm'

        )?.removeAttribute(

            'data-sidebar-bound'

        );

        this.sidebar.initialized = false;

    };

    /*
    |--------------------------------------------------------------------------
    | Initialize Sidebar
    |--------------------------------------------------------------------------
    */

    GPSTracker.initializeSidebar = function () {

        if (

            this.isSidebarInitialized()

        ) {

            return;

        }

        this.sidebarLog(

            'Initialize Sidebar.'

        );

        this.getSidebarContainer();

        this.getSidebarCounter();

        this.getSidebarEmptyState();

        this.updateVehicleCounter();

        this.toggleSidebarEmptyState();

        const closeButton = document.getElementById(

            'closeActivateDevice'

        );

        if (

            closeButton &&

            closeButton.dataset.sidebarBound !== 'true'

        ) {

            closeButton.dataset.sidebarBound = 'true';

            closeButton.addEventListener(

                'click',

                () => {

                    this.closeActivateDeviceModal();

                }

            );

        }

        const cancelButton = document.getElementById(

            'cancelActivateDevice'

        );

        if (

            cancelButton &&

            cancelButton.dataset.sidebarBound !== 'true'

        ) {

            cancelButton.dataset.sidebarBound = 'true';

            cancelButton.addEventListener(

                'click',

                () => {

                    this.closeActivateDeviceModal();

                }

            );

        }

        const addButton = document.getElementById(

            'addVehicle'

        );

        if (

            addButton &&

            addButton.dataset.sidebarBound !== 'true'

        ) {

            addButton.dataset.sidebarBound = 'true';

            addButton.addEventListener(

                'click',

                () => {

                    this.openActivateDeviceModal();

                }

            );

        }

        this.bindSidebarCardEvents();

        const activateForm = document.getElementById(

            'activateDeviceForm'

        );

        if (

            activateForm &&

            activateForm.dataset.sidebarBound !== 'true'

        ) {

            activateForm.dataset.sidebarBound = 'true';

            activateForm.addEventListener(

                'submit',

                (event) => {

                    event.preventDefault();

                    this.submitActivateDevice();

                }

            );

        }

        const vehicleForm = document.getElementById(

            'vehicleInformationForm'

        );

        if (

            vehicleForm &&

            vehicleForm.dataset.sidebarBound !== 'true'

        ) {

            vehicleForm.dataset.sidebarBound = 'true';

            vehicleForm.addEventListener(

                'submit',

                (event) => {

                    event.preventDefault();

                    this.submitVehicleInformation();

                }

            );

        }

        this.setSidebarInitialized(

            true

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Auto Initialize
    |--------------------------------------------------------------------------
    */

    GPSTracker.initializeSidebar();

});

</script>