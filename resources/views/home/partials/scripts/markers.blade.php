<script>

document.addEventListener(

    'gpstracker:map-ready',

    () => {

        /*
        |--------------------------------------------------------------------------
        | Marker State
        |--------------------------------------------------------------------------
        */

        GPSTracker.marker ??= {

            initialized: false,

            selectedVehicle: null,

            selectedMarker: null,

            markers: {},

            markerLayer: null,

            activePopup: null,

        };

        /*
        |--------------------------------------------------------------------------
        | Marker Configuration
        |--------------------------------------------------------------------------
        */

        GPSTracker.markerConfig = {

            iconSize: [48, 48],

            iconAnchor: [24, 24],

            popupAnchor: [0, -22],

            rotationOrigin: 'center center',

            defaultZoom: 17,

            animationDuration: 500,

        };

        /*
        |--------------------------------------------------------------------------
        | Getter
        |--------------------------------------------------------------------------
        */

        GPSTracker.getMarkerState = function () {

            return this.marker;

        };

        GPSTracker.getMarkerConfig = function () {

            return this.markerConfig;

        };

        GPSTracker.getMarkers = function () {

            return this.marker.markers;

        };

        GPSTracker.getMarker = function (

            deviceId

        ) {

            return this.getMarkers()[deviceId] ?? null;

        };

        GPSTracker.getSelectedMarker = function () {

            return this.marker.selectedMarker;

        };

        // getSelectedVehicle()/setSelectedVehicle() didefinisikan ulang di
        // sidebar.blade.php (di-load setelah file ini dan menang di
        // GPSTracker) dengan backing field this.sidebar.selectedVehicle,
        // bukan this.marker.selectedVehicle di bawah ini yang jadi dead
        // state karena tidak pernah ditulis oleh siapa pun lagi.

        GPSTracker.getMarkerLayer = function () {

            return this.getLayer(

                'marker'

            );

        };

        GPSTracker.getMarkerList = function () {

            return Object.values(

                this.getMarkers()

            );

        };

        GPSTracker.getMarkerIds = function () {

            return Object.keys(

                this.getMarkers()

            );

        };

        GPSTracker.getMarkerEntries = function () {

            return Object.entries(

                this.getMarkers()

            );

        };

        GPSTracker.getActivePopup = function () {

            return this.marker.activePopup;

        };

        /*
        |--------------------------------------------------------------------------
        | Setter
        |--------------------------------------------------------------------------
        */

        GPSTracker.setMarker = function (

            deviceId,

            marker

        ) {

            this.getMarkers()[deviceId] = marker;

        };

        GPSTracker.removeMarkerCache = function (

            deviceId

        ) {

            delete this.getMarkers()[deviceId];

        };

        GPSTracker.setSelectedMarker = function (

            marker

        ) {

            this.marker.selectedMarker = marker;

        };

        GPSTracker.setActivePopup = function (

            popup

        ) {

            this.marker.activePopup = popup;

        };

        GPSTracker.setMarkerInitialized = function (

            status = true

        ) {

            this.marker.initialized = status;

        };

        /*
        |--------------------------------------------------------------------------
        | Checker
        |--------------------------------------------------------------------------
        */

        GPSTracker.hasMarker = function (

            deviceId

        ) {

            return this.getMarker(

                deviceId

            ) !== null;

        };

        GPSTracker.hasMarkers = function () {

            return this.getMarkerCount() > 0;

        };

        GPSTracker.hasSelectedMarker = function () {

            return this.getSelectedMarker() !== null;

        };

        GPSTracker.hasSelectedVehicle = function () {

            return this.getSelectedVehicle() !== null;

        };

        GPSTracker.hasActivePopup = function () {

            return this.getActivePopup() !== null;

        };

        GPSTracker.isMarkerInitialized = function () {

            return this.marker.initialized;

        };

        /*
        |--------------------------------------------------------------------------
        | Logger
        |--------------------------------------------------------------------------
        */

        GPSTracker.markerLog = function (

            ...message

        ) {

            console.log(

                '[Marker]',

                ...message

            );

        };

        GPSTracker.markerWarn = function (

            ...message

        ) {

            console.warn(

                '[Marker]',

                ...message

            );

        };

        GPSTracker.markerError = function (

            ...message

        ) {

            console.error(

                '[Marker]',

                ...message

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Helper
        |--------------------------------------------------------------------------
        */

        GPSTracker.getVehicleLatLng = function (

            vehicle

        ) {

            return [

                Number(

                    vehicle.latitude

                ),

                Number(

                    vehicle.longitude

                ),

            ];

        };

        GPSTracker.hasVehicleCoordinate = function (

            vehicle

        ) {

            if (!vehicle) {

                return false;

            }

            return this.isValidCoordinate(

                vehicle.latitude,

                vehicle.longitude

            );

        };

        GPSTracker.getVehicleHeading = function (

            vehicle

        ) {

            return Number(

                vehicle.heading ?? 0

            );

        };

        GPSTracker.getVehicleSpeed = function (

            vehicle

        ) {

            return Number(

                vehicle.speed ?? 0

            );

        };

        GPSTracker.getVehicleBattery = function (

            vehicle

        ) {

            return Number(

                vehicle.battery ?? 0

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Marker Style
        |--------------------------------------------------------------------------
        */

        GPSTracker.getMarkerColor = function (vehicle) {

            return vehicle.marker_color || '#22c55e';

        };

        GPSTracker.getMarkerIconName = function (vehicle) {

            return vehicle.marker_icon ?? "car";

        };

        GPSTracker.getMarkerHeading = function (vehicle) {

            return Number(

                vehicle.heading ?? 0

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Create Marker Icon
        |--------------------------------------------------------------------------
        */

        GPSTracker.createMarkerIcon = function (vehicle) {

                const color = this.getMarkerColor(vehicle);

                const icons = {

                    motorcycle: "fa-solid fa-motorcycle",

                    car: "fa-solid fa-car",

                    pickup: "fa-solid fa-truck-pickup",

                    truck: "fa-solid fa-truck",

                    bus: "fa-solid fa-bus",

                    ambulance: "fa-solid fa-truck-medical",

                    police: "fa-solid fa-shield-halved",

                    bicycle: "fa-solid fa-bicycle",

                    van: "fa-solid fa-van-shuttle",

                    taxi: "fa-solid fa-taxi"

                };

                const iconClass =

                    icons[vehicle.marker_icon] ??

                    icons.car;

                return L.divIcon({

                    className: "",

                    iconSize: [60, 60],

                    iconAnchor: [30, 30],

                    popupAnchor: [0, -30],

                    html: `

            <div
            style="
            width:60px;
            height:60px;
            display:flex;
            justify-content:center;
            align-items:center;
            ">

            <div
            style="
            width:50px;
            height:50px;
            border-radius:50%;
            background:${color};
            border:4px solid white;
            box-shadow:0 6px 18px rgba(0,0,0,.35);
            display:flex;
            justify-content:center;
            align-items:center;
            ">

            <i
            class="${iconClass}"
            style="
            font-size:26px;
            color:white;
            ">
            </i>

            </div>

            </div>

            `

                });

            };

        // createPopupContent() dan updatePopup() didefinisikan di popup.blade.php
        // (di-load setelah file ini via scripts.blade.php, jadi versi itu yang
        // dipakai GPSTracker). Definisi lama di sini dihapus supaya tidak ada
        // dua implementasi yang saling menimpa secara tidak kentara.

        /*
        |--------------------------------------------------------------------------
        | Create Marker
        |--------------------------------------------------------------------------
        */

        GPSTracker.createMarker = function (vehicle) {

                if (!this.hasVehicleCoordinate(vehicle)) {

                    return null;

                }

                const marker = L.marker(

                    this.getVehicleLatLng(vehicle),

                    {

                        icon: this.createMarkerIcon(vehicle),

                        rotationAngle: this.getVehicleHeading(vehicle),

                        rotationOrigin: this.markerConfig.rotationOrigin,

                        riseOnHover: true,

                        keyboard: false,

                        bubblingMouseEvents: false,

                    }

                );

                marker.vehicle = {

                    ...vehicle

                };

                marker.deviceId = vehicle.device_id;

                marker.deviceCode = vehicle.device_code;

                // marker.bindPopup(

                //     this.createPopupContent(vehicle),

                //     {

                //         maxWidth: 320,

                //         closeButton: false,

                //         autoPan: true,

                //     }

                // );

                marker.on(

                    "click",

                    () => {

                        if (

                            typeof this.selectVehicle !== "function"

                        ) {

                            return;

                        }

                        this.selectVehicle(

                            marker.deviceId

                        );

                    }

                );

                /*
                |--------------------------------------------------------------------------
                | BENAR:
                |--------------------------------------------------------------------------
                */

                this.setMarker(

                    vehicle.device_id,

                    marker

                );

                return marker;

            };
        
        /*
        |--------------------------------------------------------------------------
        | Add Marker
        |--------------------------------------------------------------------------
        */

        GPSTracker.addMarker = function (vehicle) {

            if (

                this.hasMarker(

                    vehicle.device_id

                )

            ) {

                return this.getMarker(

                    vehicle.device_id

                );

            }

            const marker = this.createMarker(

                vehicle

            );

            if (!marker) {

                return null;

            }

            // if (

            //     typeof this.bindMarkerPopup === 'function'

            // ) {

            //     this.bindMarkerPopup(

            //         marker,

            //         vehicle

            //     );

            // }

            this.addLayerItem(

                'marker',

                marker

            );

            this.setMarker(

                vehicle.device_id,

                marker

            );

            return marker;

        };

        /*
        |--------------------------------------------------------------------------
        | Remove Marker
        |--------------------------------------------------------------------------
        */

        GPSTracker.removeMarker = function (

            deviceId

        ) {

            const marker = this.getMarker(

                deviceId

            );

            if (!marker) {

                return;

            }

            this.removeLayerItem(

                'marker',

                marker

            );

            this.removeMarkerCache(

                deviceId

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Clear Markers
        |--------------------------------------------------------------------------
        */

        GPSTracker.clearMarkers = function () {

            this.clearLayer(

                'marker'

            );

            this.marker.markers = {};

            this.setSelectedMarker(

                null

            );

            this.setSelectedVehicle(

                null

            );

            this.setActivePopup(

                null

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Marker Count
        |--------------------------------------------------------------------------
        */

        GPSTracker.getMarkerCount = function () {

            return Object.keys(

                this.getMarkers()

            ).length;

        };

        /*
        |--------------------------------------------------------------------------
        | Find Marker
        |--------------------------------------------------------------------------
        */

        GPSTracker.findMarker = function (

            deviceId

        ) {

            return this.getMarker(

                deviceId

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Marker Collection
        |--------------------------------------------------------------------------
        */

        GPSTracker.eachMarker = function (

                    callback

                ) {

                    this.getMarkerList().forEach(

                        callback

                    );

                };

                GPSTracker.findMarkerBy = function (

                    callback

                ) {

                    return this.getMarkerList().find(

                        callback

                    ) ?? null;

                };

                GPSTracker.filterMarkers = function (

                    callback

                ) {

                    return this.getMarkerList().filter(

                        callback

                    );

                };

                GPSTracker.getMarkerByDeviceCode = function (

                    deviceCode

                ) {

                    return this.findMarkerBy(

                        marker =>

                            marker.deviceCode === deviceCode

                    );

                };

                /*
                |--------------------------------------------------------------------------
                | Focus Marker
                |--------------------------------------------------------------------------
                */

                GPSTracker.focusMarker = function (

            deviceId

        ) {

            if (

                !Array.isArray(this.vehicles)

            ) {

                return;

            }

            const vehicle = this.vehicles.find(

                vehicle =>

                    String(vehicle.device_id) === String(deviceId)

            );

            if (

                !vehicle ||

                !this.hasVehicleCoordinate(vehicle)

            ) {

                alert(

                    'Kendaraan belum mengirim data GPS.'

                );

                return;

            }

            const marker = this.getMarker(

                deviceId

            );

            if (!marker) {

                alert(

                    'Marker kendaraan belum tersedia.'

                );

                return;

            }

            this.setSelectedMarker(

                marker

            );

            this.setSelectedVehicle(

                deviceId

            );

            this.flyToLocation(

                vehicle.latitude,

                vehicle.longitude,

                this.getMarkerConfig().defaultZoom

            );

            marker.openPopup();

        };

        /*
        |--------------------------------------------------------------------------
        | Create Or Add Marker
        |--------------------------------------------------------------------------
        */

        GPSTracker.getOrCreateMarker = function (vehicle) {

            let marker = this.findMarker(

                vehicle.device_id

            );

            if (marker) {

                return marker;

            }

            return this.addMarker(

                vehicle

            );

        };
        
        /*
        |--------------------------------------------------------------------------
        | Update Marker Position
        |--------------------------------------------------------------------------
        */

        GPSTracker.updateMarkerPosition = function (

            marker,

            vehicle

        ) {

            if (

                !marker ||

                !this.hasVehicleCoordinate(

                    vehicle

                )

            ) {

                return;

            }

            marker.setLatLng(

                this.getVehicleLatLng(

                    vehicle

                )

            );

        };
        
        /*
        |--------------------------------------------------------------------------
        | Update Marker Rotation
        |--------------------------------------------------------------------------
        */

        GPSTracker.updateMarkerRotation = function (

            marker,

            vehicle

        ) {

            if (

                !marker ||

                typeof marker.setRotationAngle !== 'function'

            ) {

                return;

            }

            marker.setRotationAngle(

                this.getVehicleHeading(

                    vehicle

                )

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Update Marker
        |--------------------------------------------------------------------------
        */

        GPSTracker.updateMarker = function (

            vehicle

        ) {

            if (!vehicle) {

                return;

            }

            let marker = this.getMarker(

                vehicle.device_id

            );

            /*
            |--------------------------------------------------------------------------
            | Marker belum ada
            |--------------------------------------------------------------------------
            */

            if (!marker) {

                marker = this.createMarker(
                    vehicle
                );

                if (!marker) {
                    return;
                }

                this.addLayerItem(
                    'marker',
                    marker
                );

            }

            /*
            |--------------------------------------------------------------------------
            | Cache Vehicle
            |--------------------------------------------------------------------------
            */

            marker.vehicle = {

                ...vehicle

            };

            marker.deviceId = vehicle.device_id;

            marker.deviceCode = vehicle.device_code;

            /*
            |--------------------------------------------------------------------------
            | Position
            |--------------------------------------------------------------------------
            */

            this.updateMarkerPosition(

                marker,

                vehicle

            );

            /*
            |--------------------------------------------------------------------------
            | Rotation
            |--------------------------------------------------------------------------
            */

            this.updateMarkerRotation(

                marker,

                vehicle

            );

            /*
            |--------------------------------------------------------------------------
            | Icon
            |--------------------------------------------------------------------------
            */

            marker.setIcon(

                this.createMarkerIcon(

                    vehicle

                )

            );

            /*
            |--------------------------------------------------------------------------
            | Popup
            |--------------------------------------------------------------------------
            */

            this.updateMarkerPopup(

                marker,

                vehicle

            );

            /*
            |--------------------------------------------------------------------------
            | Sidebar
            |--------------------------------------------------------------------------
            */

            if (

                typeof this.updateVehicleCard === 'function'

            ) {

                this.updateVehicleCard(

                    vehicle

                );

            }

        };

        /*
        |--------------------------------------------------------------------------
        | Update Marker Popup
        |--------------------------------------------------------------------------
        */

        GPSTracker.updateMarkerPopup = function (

            marker,

            vehicle

        ) {

            if (

                !marker ||

                typeof this.updatePopup !== 'function'

            ) {

                return;

            }

            this.updatePopup(

                vehicle

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Render Marker
        |--------------------------------------------------------------------------
        */

        GPSTracker.renderMarker = function (

            vehicle

        ) {

            this.updateMarker(

                vehicle

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Render All Marker
        |--------------------------------------------------------------------------
        */

        GPSTracker.renderMarkers = function () {

            if (

                !Array.isArray(

                    this.vehicles

                )

            ) {

                return;

            }

            this.vehicles.forEach(vehicle => {

                if (

                    this.hasVehicleCoordinate(vehicle)

                ) {

                    this.renderMarker(vehicle);

                }

            });

        };

        /*
        |--------------------------------------------------------------------------
        | Refresh Marker
        |--------------------------------------------------------------------------
        */

        GPSTracker.refreshMarkers = function () {

            this.renderMarkers();

        };

        /*
        |--------------------------------------------------------------------------
        | Rebuild Marker
        |--------------------------------------------------------------------------
        */

        GPSTracker.rebuildMarkers = function () {

            this.clearMarkers();

            this.renderMarkers();

        };

        /*
        |--------------------------------------------------------------------------
        | Visible Marker
        |--------------------------------------------------------------------------
        */

        GPSTracker.getVisibleMarkerCount = function () {

            return this.getLayerCount(

                'marker'

            );

        };
        
        /*
        |--------------------------------------------------------------------------
        | Fit Vehicles
        |--------------------------------------------------------------------------
        | Auto-center peta ke marker kendaraan saat halaman dimuat/refresh,
        | supaya user tidak perlu geser/zoom manual untuk melihat kendaraannya.
        |--------------------------------------------------------------------------
        */

        GPSTracker.fitVehicles = function () {

            if (!this.map) {

                return;

            }

            const located = (this.vehicles ?? []).filter(
                vehicle => this.hasVehicleCoordinate(vehicle)
            );

            if (!located.length) {

                return;

            }

            if (located.length === 1) {

                const vehicle = located[0];

                this.flyToLocation(

                    vehicle.latitude,

                    vehicle.longitude,

                    this.getMarkerConfig().defaultZoom

                );

                return;

            }

            const bounds = L.latLngBounds(

                located.map(
                    vehicle => this.getVehicleLatLng(vehicle)
                )

            );

            if (bounds.isValid()) {

                this.map.fitBounds(bounds, {

                    padding: [60, 60],

                    maxZoom: 16,

                });

            }

        };

        /*
        |--------------------------------------------------------------------------
        | Initialize Marker
        |--------------------------------------------------------------------------
        */

        GPSTracker.initializeMarker = function () {

            if (

                this.isMarkerInitialized()

            ) {

                return;

            }

            this.renderMarkers();

            if (

                typeof this.fitVehicles === 'function'

            ) {

                // this.fitVehicles();

            }

            if (

                typeof this.hideMapLoading === 'function'

            ) {

                this.hideMapLoading();

            }

            this.setMarkerInitialized(

                true

            );

            this.markerLog(

                'Marker initialized.'

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Reset Marker
        |--------------------------------------------------------------------------
        */

        GPSTracker.resetMarker = function () {

            this.clearMarkers();

            this.setSelectedMarker(

                null

            );

            this.setSelectedVehicle(

                null

            );

            this.setActivePopup(

                null

            );

            this.setMarkerInitialized(

                false

            );

        };

        // receiveRealtimePayload() didefinisikan di realtime.blade.php (di-load
        // setelah file ini, jadi versi itu yang dipakai GPSTracker). Definisi
        // lama di sini dihapus - lihat processRealtimeVehicle() di
        // realtime.blade.php untuk alur vehicle baru/update saat ini.

        /*
        |--------------------------------------------------------------------------
        | Sync Vehicle
        |--------------------------------------------------------------------------
        */

        GPSTracker.syncVehicle = function (

            vehicle

        ) {

            if (!vehicle) {

                return;

            }

            this.receiveRealtimePayload(

                vehicle

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Vehicle Added
        |--------------------------------------------------------------------------
        */

        document.addEventListener(

                'gpstracker:vehicle-updated',

                event => {

                    if (!event.detail?.vehicle) {
                        return;
                    }

                }

            );

        /*
        |--------------------------------------------------------------------------
        | Vehicle Removed
        |--------------------------------------------------------------------------
        */

        document.addEventListener(

            'gpstracker:vehicle-removed',

            event => {

                const vehicle =

                    event.detail;

                if (

                    typeof GPSTracker.destroyPopup === 'function'

                ) {

                    GPSTracker.destroyPopup(

                        vehicle.device_id

                    );

                }

                GPSTracker.removeMarker(

                    vehicle.device_id

                );

            }

        );

        /*
        |--------------------------------------------------------------------------
        | Global Helper
        |--------------------------------------------------------------------------
        */

        window.focusVehicle = function (

            deviceId

        ) {

            GPSTracker.focusMarker(

                deviceId

            );

        };

        window.refreshMarkers = function () {

            GPSTracker.refreshMarkers();

        };

        window.syncVehicle = function (

            vehicle

        ) {

            GPSTracker.receiveRealtimePayload(

                vehicle

            );

        };

        window.findMarker = function (

            deviceId

        ) {

            return GPSTracker.findMarker(

                deviceId

            );

        };

        window.removeMarker = function (

            deviceId

        ) {

            GPSTracker.removeMarker(

                deviceId

            );

        };

        window.clearMarkers = function () {

            GPSTracker.clearMarkers();

        };

        window.getMarkerCount = function () {

            return GPSTracker.getMarkerCount();

        };

        /*
        |--------------------------------------------------------------------------
        | Marker Visibility
        |--------------------------------------------------------------------------
        */

        GPSTracker.showMarkers = function () {

            this.showLayer(

                'marker'

            );

        };

        GPSTracker.hideMarkers = function () {

            this.hideLayer(

                'marker'

            );

        };

        GPSTracker.toggleMarkers = function (

            visible

        ) {

            this.toggleLayer(

                'marker',

                visible

            );

        };

        GPSTracker.isMarkerVisible = function () {

            return this.isLayerVisible(

                'marker'

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Marker Bounds
        |--------------------------------------------------------------------------
        */

        GPSTracker.fitMarkers = function () {

            this.fitLayer(

                'marker'

            );

        };

        GPSTracker.zoomToMarker = function (

            deviceId

        ) {

            const marker = this.getMarker(

                deviceId

            );

            if (!marker) {

                return;

            }

            this.flyToLocation(

                marker.getLatLng().lat,

                marker.getLatLng().lng,

                this.getMarkerConfig().defaultZoom

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Marker Cache
        |--------------------------------------------------------------------------
        */

        GPSTracker.resetMarkerCache = function () {

            this.marker.markers = {};

        };

        GPSTracker.destroyMarkers = function () {

            this.clearMarkers();

            this.resetMarkerCache();

        };

        GPSTracker.reloadMarkers = function () {

            this.destroyMarkers();

            this.renderMarkers();

        };

        /*
        |--------------------------------------------------------------------------
        | Destroy Marker
        |--------------------------------------------------------------------------
        */

        GPSTracker.destroyMarker = function () {

            this.destroyMarkers();

            this.setSelectedMarker(

                null

            );

            this.setSelectedVehicle(

                null

            );

            this.setActivePopup(

                null

            );

            this.setMarkerInitialized(

                false

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Marker Ready Event
        |--------------------------------------------------------------------------
        */

        document.dispatchEvent(

            new CustomEvent(

                'gpstracker:markers-ready',

                {

                    detail: {

                        count: GPSTracker.getMarkerCount(),

                    },

                }

            )

        );

        /*
        |--------------------------------------------------------------------------
        | Auto Initialize
        |--------------------------------------------------------------------------
        */

        GPSTracker.initializeMarker();

    }

);

</script>