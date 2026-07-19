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

        const card = this.getVehicleCard(

            deviceId

        );

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

                        <h4
                            id="vehicle-name-${vehicle.device_id}"
                            class="text-lg font-bold text-slate-900">

                            ${vehicle.vehicle_name ?? '-'}

                        </h4>

                        <p
                            id="vehicle-plate-${vehicle.device_id}"
                            class="mt-1 text-sm text-slate-500">

                            ${(vehicle.plate_number ?? '-').toUpperCase()}

                        </p>

                    </div>

                    <span
                        id="vehicle-status-${vehicle.device_id}"
                        data-status
                        data-online="${vehicle.is_active ? 1 : 0}"
                        class="rounded-full px-3 py-1 text-xs font-semibold ${vehicle.is_active
                            ? 'bg-green-100 text-green-700'
                            : 'bg-red-100 text-red-600'}">

                        ${vehicle.is_active ? 'Online' : 'Offline'}

                    </span>

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

        const card = this.getVehicleCard(

            vehicle.device_id

        );

        if (!card) {

            this.appendVehicleCard(

                vehicle

            );

            return;
        }

        this.updateVehicleCardData(

            card,

            vehicle

        );

        this.updateVehicleCardHeader(

            vehicle

        );

        this.updateVehicleCardStatus(

            vehicle

        );

        this.updateVehicleCardInformation(

            vehicle

        );

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
        /*
    |--------------------------------------------------------------------------
    | Sidebar Click Event
    |--------------------------------------------------------------------------
    */

    GPSTracker.bindSidebarCardEvents = function () {

        this.getVehicleCards().forEach(card => {

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

        this.bindSidebarCardEvents();

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