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

        GPSTracker.getMarker = function (deviceId) {

            return this.marker.markers[deviceId] ?? null;

        };

        GPSTracker.getSelectedMarker = function () {

            return this.marker.selectedMarker;

        };

        GPSTracker.getSelectedVehicle = function () {

            return this.marker.selectedVehicle;

        };

        GPSTracker.getMarkerLayer = function () {

            return this.markerLayer;

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

            this.marker.markers[deviceId] = marker;

        };

        GPSTracker.setSelectedMarker = function (

            marker

        ) {

            this.marker.selectedMarker = marker;

        };

        GPSTracker.setSelectedVehicle = function (

            deviceId

        ) {

            this.marker.selectedVehicle = deviceId;

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

        GPSTracker.hasSelectedMarker = function () {

            return this.getSelectedMarker() !== null;

        };

        GPSTracker.hasSelectedVehicle = function () {

            return this.getSelectedVehicle() !== null;

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
        | Create Marker Icon
        |--------------------------------------------------------------------------
        */

        GPSTracker.createMarkerIcon = function (vehicle) {

            return L.divIcon({

                className: '',

                html: `

                    <div class="relative">

                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-full border border-slate-200 bg-white shadow-lg">

                            <img

                                src="/images/marker/car.png"

                                class="h-7 w-7"

                                draggable="false"

                            >

                        </div>

                        <span

                            class="absolute -right-1 -top-1 h-3.5 w-3.5 rounded-full border-2 border-white

                            ${vehicle.is_active

                                ? 'bg-green-500'

                                : 'bg-red-500'}

                            ">

                        </span>

                    </div>

                `,

                iconSize:

                    this.markerConfig.iconSize,

                iconAnchor:

                    this.markerConfig.iconAnchor,

                popupAnchor:

                    this.markerConfig.popupAnchor,

            });

        };

        /*
        |--------------------------------------------------------------------------
        | Create Marker
        |--------------------------------------------------------------------------
        */

        GPSTracker.createMarker = function (vehicle) {

            if (

                !this.hasVehicleCoordinate(

                    vehicle

                )

            ) {

                return null;

            }

            const marker = L.marker(

                this.getVehicleLatLng(

                    vehicle

                ),

                {

                    icon: this.createMarkerIcon(

                        vehicle

                    ),

                    rotationAngle: this.getVehicleHeading(

                        vehicle

                    ),

                }

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

if (

    typeof this.bindMarkerPopup === 'function'

) {

    this.bindMarkerPopup(

        marker,

        vehicle

    );

}

marker.addTo(

    this.getMarkerLayer()

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

        GPSTracker.removeMarker = function (deviceId) {

            const marker = this.getMarker(

                deviceId

            );

            if (!marker) {

                return;

            }

            this.getMarkerLayer()

                .removeLayer(

                    marker

                );

            delete this.marker.markers[deviceId];

        };

        /*
        |--------------------------------------------------------------------------
        | Clear Markers
        |--------------------------------------------------------------------------
        */

        GPSTracker.clearMarkers = function () {

            Object.values(

                this.getMarkers()

            ).forEach(marker => {

                this.getMarkerLayer()

                    .removeLayer(

                        marker

                    );

            });

            this.marker.markers = {};

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
        | Marker Exists
        |--------------------------------------------------------------------------
        */

        GPSTracker.findMarker = function (deviceId) {

            return this.getMarker(

                deviceId

            );

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
        | Update Marker Icon
        |--------------------------------------------------------------------------
        */

        GPSTracker.updateMarkerIcon = function (

            marker,

            vehicle

        ) {

            if (!marker) {

                return;

            }

            marker.setIcon(

                this.createMarkerIcon(

                    vehicle

                )

            );

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
        | Update Marker
        |--------------------------------------------------------------------------
        */

        GPSTracker.updateMarker = function (

            vehicle

        ) {

            if (

                !this.hasVehicleCoordinate(

                    vehicle

                )

            ) {

                return;

            }

            const marker = this.getOrCreateMarker(

                vehicle

            );

            if (!marker) {

                return;

            }

            this.updateMarkerPosition(

                marker,

                vehicle

            );

            this.updateMarkerRotation(

                marker,

                vehicle

            );

            this.updateMarkerIcon(

                marker,

                vehicle

            );

            this.updateMarkerPopup(

                marker,

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

            this.vehicles.forEach(

                vehicle => {

                    this.renderMarker(

                        vehicle

                    );

                }

            );

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

            return this.getMarkerCount();

        };
        /*
        |--------------------------------------------------------------------------
        | Initialize Marker
        |--------------------------------------------------------------------------
        */

        GPSTracker.initializeMarker = function () {

            if (this.isMarkerInitialized()) {

                return;

            }

            this.renderMarkers();

            if (typeof this.fitVehicles === 'function') {

                this.fitVehicles();

            }

            if (typeof this.hideMapLoading === 'function') {

                this.hideMapLoading();

            }

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

        /*
        |--------------------------------------------------------------------------
        | Destroy Marker
        |--------------------------------------------------------------------------
        */

        GPSTracker.destroyMarker = function () {

            this.resetMarker();

        };

        /*
        |--------------------------------------------------------------------------
        | Vehicle Added
        |--------------------------------------------------------------------------
        */

        document.addEventListener(

            'gpstracker:vehicle-added',

            event => {

                GPSTracker.syncVehicle(

                    event.detail

                );

            }

        );

        /*
        |--------------------------------------------------------------------------
        | Vehicle Updated
        |--------------------------------------------------------------------------
        */

        document.addEventListener(

            'gpstracker:vehicle-updated',

            event => {

                GPSTracker.syncVehicle(

                    event.detail

                );

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

                if (

                    typeof GPSTracker.destroyPopup === 'function'

                ) {

                    GPSTracker.destroyPopup(

                        event.detail.device_id

                    );

                }

                GPSTracker.removeMarker(

                    event.detail.device_id

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

            GPSTracker.syncVehicle(

                vehicle

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