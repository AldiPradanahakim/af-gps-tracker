<script>

document.addEventListener('gpstracker:map-ready', () => {

    /*
    |--------------------------------------------------------------------------
    | Popup State
    |--------------------------------------------------------------------------
    */

    GPSTracker.popup ??= {

        initialized: false,

        currentMarker: null,

        currentVehicle: null,

        currentPopup: null,

        opened: false,

        loading: false,

        popupCache: new Map(),

    };

    /*
    |--------------------------------------------------------------------------
    | Popup Configuration
    |--------------------------------------------------------------------------
    */

    GPSTracker.popupConfig = {

        maxWidth: 340,

        minWidth: 280,

        className: 'gps-tracker-popup',

        autoPan: true,

        autoClose: false,

        closeButton: true,

        closeOnClick: false,

        keepInView: true,

    };

    /*
    |--------------------------------------------------------------------------
    | Getter
    |--------------------------------------------------------------------------
    */

    GPSTracker.getPopup = function () {

        return this.popup;

    };

    GPSTracker.getPopupConfig = function () {

        return this.popupConfig;

    };

    GPSTracker.getCurrentPopup = function () {

        return this.popup.currentPopup;

    };

    GPSTracker.getCurrentMarker = function () {

        return this.popup.currentMarker;

    };

    GPSTracker.getCurrentVehicle = function () {

        return this.popup.currentVehicle;

    };

    /*
    |--------------------------------------------------------------------------
    | Setter
    |--------------------------------------------------------------------------
    */

    GPSTracker.setCurrentPopup = function (popup = null) {

        this.popup.currentPopup = popup;

    };

    GPSTracker.setCurrentMarker = function (marker = null) {

        this.popup.currentMarker = marker;

    };

    GPSTracker.setCurrentVehicle = function (vehicle = null) {

        this.popup.currentVehicle = vehicle;

    };

    GPSTracker.setPopupOpened = function (status = true) {

        this.popup.opened = Boolean(status);

    };

    GPSTracker.setPopupLoading = function (status = true) {

        this.popup.loading = Boolean(status);

    };

    GPSTracker.setPopupInitialized = function (status = true) {

        this.popup.initialized = Boolean(status);

    };

    /*
    |--------------------------------------------------------------------------
    | Checker
    |--------------------------------------------------------------------------
    */

    GPSTracker.isPopupOpened = function () {

        return this.popup.opened;

    };

    GPSTracker.isPopupLoading = function () {

        return this.popup.loading;

    };

    GPSTracker.isPopupInitialized = function () {

        return this.popup.initialized;

    };

    GPSTracker.hasPopup = function () {

        return this.popup.currentPopup !== null;

    };

    GPSTracker.hasCurrentVehicle = function () {

        return this.popup.currentVehicle !== null;

    };

    /*
    |--------------------------------------------------------------------------
    | Popup Cache
    |--------------------------------------------------------------------------
    */

    GPSTracker.cachePopup = function (deviceId, popup) {

        this.popup.popupCache.set(

            String(deviceId),

            popup

        );

    };

    GPSTracker.getCachedPopup = function (deviceId) {

        return this.popup.popupCache.get(

            String(deviceId)

        ) ?? null;

    };

    GPSTracker.removeCachedPopup = function (deviceId) {

        this.popup.popupCache.delete(

            String(deviceId)

        );

    };

    GPSTracker.clearPopupCache = function () {

        this.popup.popupCache.clear();

    };

    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */

    GPSTracker.getPopupVehicle = function (deviceId) {

        if (

            typeof this.findVehicle !== 'function'

        ) {

            return null;

        }

        return this.findVehicle(

            deviceId

        );

    };

    GPSTracker.closeCurrentPopup = function () {

        if (

            !this.hasPopup()

        ) {

            return;

        }

        this.popup.currentPopup.remove();

        this.setCurrentPopup(null);

        this.setCurrentMarker(null);

        this.setCurrentVehicle(null);

        this.setPopupOpened(false);

    };

    /*
    |--------------------------------------------------------------------------
    | Logger
    |--------------------------------------------------------------------------
    */

    GPSTracker.popupLog = function (...message) {

        console.log(

            '[Popup]',

            ...message

        );

    };

    GPSTracker.popupWarn = function (...message) {

        console.warn(

            '[Popup]',

            ...message

        );

    };

    GPSTracker.popupError = function (...message) {

        console.error(

            '[Popup]',

            ...message

        );

    };
        /*
    |--------------------------------------------------------------------------
    | Create Popup
    |--------------------------------------------------------------------------
    */

    GPSTracker.createPopup = function () {

        return L.popup({

            maxWidth: this.popupConfig.maxWidth,

            minWidth: this.popupConfig.minWidth,

            className: this.popupConfig.className,

            autoPan: this.popupConfig.autoPan,

            autoClose: this.popupConfig.autoClose,

            closeButton: this.popupConfig.closeButton,

            closeOnClick: this.popupConfig.closeOnClick,

            keepInView: this.popupConfig.keepInView,

        });

    };

    /*
    |--------------------------------------------------------------------------
    | Popup Content
    |--------------------------------------------------------------------------
    */

    GPSTracker.createPopupContent = function (vehicle) {

        return this.renderPopup(

            vehicle

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Create Vehicle Popup
    |--------------------------------------------------------------------------
    */

    GPSTracker.createVehiclePopup = function (vehicle) {

        const popup = this.createPopup();

        popup.setContent(

            this.createPopupContent(

                vehicle

            )

        );

        this.cachePopup(

            vehicle.device_id,

            popup

        );

        return popup;

    };

    /*
    |--------------------------------------------------------------------------
    | Get Vehicle Popup
    |--------------------------------------------------------------------------
    */

    GPSTracker.getVehiclePopup = function (deviceId) {

        return this.getCachedPopup(

            deviceId

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Get Or Create Popup
    |--------------------------------------------------------------------------
    */

    GPSTracker.getOrCreatePopup = function (vehicle) {

        let popup = this.getVehiclePopup(

            vehicle.device_id

        );

        if (!popup) {

            popup = this.createVehiclePopup(

                vehicle

            );

        }

        return popup;

    };

    /*
    |--------------------------------------------------------------------------
    | Update Popup
    |--------------------------------------------------------------------------
    */

    GPSTracker.updatePopup = function (vehicle) {

        if (!vehicle) {

            return;

        }

        const popup = this.getOrCreatePopup(

            vehicle

        );

        popup.setContent(

            this.createPopupContent(

                vehicle

            )

        );

        const marker = this.getMarker?.(vehicle.device_id);

        if (marker?.getPopup()) {

            marker.getPopup().setContent(

                this.createPopupContent(vehicle)

            );

        }

        if (

            this.hasCurrentVehicle() &&

            String(

                this.getCurrentVehicle().device_id

            ) === String(vehicle.device_id)

        ) {

            this.setCurrentVehicle(

                vehicle

            );

            this.setCurrentPopup(

                popup

            );

        }

    };

    /*
    |--------------------------------------------------------------------------
    | Destroy Popup
    |--------------------------------------------------------------------------
    */

    GPSTracker.destroyPopup = function (

        deviceId

    ) {

        const popup = this.getVehiclePopup(

            deviceId

        );

        if (!popup) {

            return;

        }

        popup.remove();

        this.removeCachedPopup(

            deviceId

        );

        if (

            this.getCurrentVehicle() &&

            String(

                this.getCurrentVehicle().device_id

            ) === String(deviceId)

        ) {

            this.setCurrentPopup(

                null

            );

            this.setCurrentMarker(

                null

            );

            this.setCurrentVehicle(

                null

            );

            this.setPopupOpened(

                false

            );

        }

    };

    /*
    |--------------------------------------------------------------------------
    | Refresh Popup
    |--------------------------------------------------------------------------
    */

    GPSTracker.refreshPopup = function (vehicle) {

        this.updatePopup(

            vehicle

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Render Popup
    |--------------------------------------------------------------------------
    */

    GPSTracker.renderPopup = function (vehicle) {

        return this.buildPopupHtml(

            vehicle

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Popup HTML
    |--------------------------------------------------------------------------
    */

    GPSTracker.buildPopupHtml = function (vehicle) {

        const online = Boolean(

            vehicle.is_active

        );

        return `

            <div
                class="space-y-4"
                data-popup="${vehicle.device_id}">

                <div class="flex items-center justify-between">

                    <div>

                        <h3 class="text-base font-bold text-slate-900">

                            ${vehicle.vehicle_name ?? '-'}

                        </h3>

                        <p class="text-sm text-slate-500">

                            ${(vehicle.plate_number ?? '-').toUpperCase()}

                        </p>

                    </div>

                    <span
                        class="rounded-full px-3 py-1 text-xs font-semibold
                        ${online
                            ? 'bg-green-100 text-green-700'
                            : 'bg-red-100 text-red-600'}">

                        ${online ? 'Online' : 'Offline'}

                    </span>

                </div>

                <div class="grid grid-cols-2 gap-4">

                    <div>

                        <p class="text-xs uppercase tracking-wide text-slate-400">

                            Kecepatan

                        </p>

                        <p class="mt-1 text-base font-semibold text-slate-900">

                            ${vehicle.speed ?? 0} km/j

                        </p>

                    </div>

                    <div>

                        <p class="text-xs uppercase tracking-wide text-slate-400">

                            Jenis

                        </p>

                        <p class="mt-1 text-base font-semibold text-slate-900">

                            ${vehicle.vehicle_type ?? '-'}

                        </p>

                    </div>

                </div>

                <div class="grid grid-cols-2 gap-4">

                    <div>

                        <p class="text-xs uppercase tracking-wide text-slate-400">

                            Baterai

                        </p>

                        <p class="mt-1 text-base font-semibold text-slate-900">

                            ${vehicle.battery ?? '-'}

                        </p>

                    </div>

                    <div>

                        <p class="text-xs uppercase tracking-wide text-slate-400">

                            Update

                        </p>

                        <p class="mt-1 text-sm font-medium text-slate-900">

                            ${vehicle.updated_at ?? '-'}

                        </p>

                    </div>

                </div>

                <div>

                    <p class="text-xs uppercase tracking-wide text-slate-400">

                        Lokasi

                    </p>

                    <p class="mt-1 text-sm leading-6 text-slate-700">

                        ${vehicle.address ?? '-'}

                    </p>

                </div>

                <div
                    class="flex items-center gap-2 pt-2">

                    <button
                        type="button"
                        data-popup-focus="${vehicle.device_id}"
                        class="flex-1 rounded-xl bg-[#2563EB] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">

                        Lihat di Peta

                    </button>

                    <button
                        type="button"
                        data-popup-detail="${vehicle.device_id}"
                        class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">

                        Detail

                    </button>

                </div>

            </div>

        `;

    };
        /*
    |--------------------------------------------------------------------------
    | Open Popup
    |--------------------------------------------------------------------------
    */

    GPSTracker.openPopup = function (deviceId) {

        if (

            typeof this.getMarker !== 'function'

        ) {

            return;

        }

        const marker = this.getMarker(

            deviceId

        );

        if (!marker) {

            return;

        }

        const vehicle = this.getPopupVehicle(

            deviceId

        );

        if (!vehicle) {

            return;

        }

        this.updatePopup(

            vehicle

        );

        const popup = marker.getPopup();

        if (popup) {

            popup.setContent(

                this.createPopupContent(vehicle)

            );

        }

        this.setCurrentMarker(

            marker

        );

        this.setCurrentVehicle(

            vehicle

        );

        this.setCurrentPopup(
            popup
        );

        this.setPopupOpened(

            true

        );

        marker.openPopup();

        this.bindPopupActions(

            vehicle

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Close Popup
    |--------------------------------------------------------------------------
    */

    GPSTracker.closePopup = function () {

        const marker = this.getCurrentMarker();

        if (!marker) {

            return;

        }

        marker.closePopup();

        this.setPopupOpened(false);

        this.setCurrentPopup(null);

        this.setCurrentMarker(null);

        this.setCurrentVehicle(null);

    };

    /*
    |--------------------------------------------------------------------------
    | Refresh Open Popup
    |--------------------------------------------------------------------------
    */

    GPSTracker.refreshOpenedPopup = function (

        vehicle

    ) {

        if (

            !this.hasCurrentVehicle()

        ) {

            return;

        }

        if (

            String(

                this.getCurrentVehicle().device_id

            ) !==

            String(

                vehicle.device_id

            )

        ) {

            return;

        }

        this.updatePopup(

            vehicle

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Popup Actions
    |--------------------------------------------------------------------------
    */

    GPSTracker.bindPopupActions = function (

        vehicle

    ) {

        requestAnimationFrame(() => {

            const focusButton = document.querySelector(

                `[data-popup-focus="${vehicle.device_id}"]`

            );

            const detailButton = document.querySelector(

                `[data-popup-detail="${vehicle.device_id}"]`

            );

            if (focusButton) {

                focusButton.onclick = () => {

                    if (

                        typeof this.focusVehicle === 'function'

                    ) {

                        this.focusVehicle(

                            vehicle.device_id

                        );

                    }

                };

            }

            if (detailButton) {

                detailButton.onclick = () => {

                    if (

                        typeof this.openVehicleDetail === 'function'

                    ) {

                        this.openVehicleDetail(

                            vehicle.device_id

                        );

                    }

                };

            }

        });

    };

    /*
    |--------------------------------------------------------------------------
    | Refresh Popup
    |--------------------------------------------------------------------------
    */

    GPSTracker.syncPopup = function (

        vehicle

    ) {

        this.refreshPopup(

            vehicle

        );

        this.refreshOpenedPopup(

            vehicle

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Reset Popup
    |--------------------------------------------------------------------------
    */

    GPSTracker.resetPopup = function () {

        this.clearPopupCache();

        this.setCurrentPopup(

            null

        );

        this.setCurrentMarker(

            null

        );

        this.setCurrentVehicle(

            null

        );

        this.setPopupOpened(

            false

        );

        this.setPopupLoading(

            false

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Destroy Popup
    |--------------------------------------------------------------------------
    */

    GPSTracker.destroyPopupModule = function () {

        this.closePopup();

        this.resetPopup();

    };
        /*
    |--------------------------------------------------------------------------
    | Sidebar Integration
    |--------------------------------------------------------------------------
    */

    GPSTracker.refreshSidebarPopup = function (vehicle) {

        if (

            typeof this.updateVehicleCard !== 'function'

        ) {

            return;

        }

        this.updateVehicleCard(

            vehicle

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Search Integration
    |--------------------------------------------------------------------------
    */

    GPSTracker.refreshSearchPopup = function (vehicle) {

        if (

            typeof this.refreshSearch !== 'function'

        ) {

            return;

        }

        this.refreshSearch(

            vehicle

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Notification Integration
    |--------------------------------------------------------------------------
    */

    GPSTracker.refreshNotificationPopup = function (vehicle) {

        if (

            typeof this.refreshNotification !== 'function'

        ) {

            return;

        }

        this.refreshNotification(

            vehicle

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Realtime Integration
    |--------------------------------------------------------------------------
    */

    GPSTracker.refreshRealtimePopup = function (vehicle) {

        if (!vehicle) {

            return;

        }

        this.syncPopup(

            vehicle

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Refresh Popup Modules
    |--------------------------------------------------------------------------
    */

    GPSTracker.refreshPopupModules = function (vehicle) {

        this.refreshSidebarPopup(

            vehicle

        );

        this.refreshSearchPopup(

            vehicle

        );

        this.refreshNotificationPopup(

            vehicle

        );

        this.refreshRealtimePopup(

            vehicle

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Initialize Popup
    |--------------------------------------------------------------------------
    */

    GPSTracker.initializePopup = function () {

        if (

            this.isPopupInitialized()

        ) {

            return;

        }

        this.popupLog(

            'Popup initialized.'

        );

        this.setPopupInitialized(

            true

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Reset Popup Manager
    |--------------------------------------------------------------------------
    */

    GPSTracker.resetPopupManager = function () {

        this.resetPopup();

    };

    /*
    |--------------------------------------------------------------------------
    | Destroy Popup Manager
    |--------------------------------------------------------------------------
    */

    GPSTracker.destroyPopupManager = function () {

        this.closePopup();

        this.resetPopupManager();

    };

    /*
    |--------------------------------------------------------------------------
    | Auto Initialize
    |--------------------------------------------------------------------------
    */

    GPSTracker.initializePopup();

});

</script>