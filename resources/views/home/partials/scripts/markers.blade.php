<script>

document.addEventListener('DOMContentLoaded', () => {

    if (typeof GPSTracker === 'undefined') {

        return;

    }

    /*
    |--------------------------------------------------------------------------
    | Marker State
    |--------------------------------------------------------------------------
    */

    GPSTracker.markers = GPSTracker.markers || {};

    GPSTracker.markerGroup = GPSTracker.markerGroup || L.layerGroup().addTo(GPSTracker.map);

    /*
    |--------------------------------------------------------------------------
    | Marker Configuration
    |--------------------------------------------------------------------------
    */

    GPSTracker.markerConfig = {

        defaultZoom: 17,

        fitZoom: 16,

        animationDuration: 1.2,

        fitPadding: [60, 60],

        iconSize: [48, 48],

        iconAnchor: [24, 24],

        popupAnchor: [0, -22],

    };

    /*
    |--------------------------------------------------------------------------
    | Utilities
    |--------------------------------------------------------------------------
    */

    GPSTracker.hasMarker = function (deviceId) {

        return !!this.markers[deviceId];

    };

    GPSTracker.getMarker = function (deviceId) {

        return this.markers[deviceId] ?? null;

    };

    GPSTracker.setMarker = function (deviceId, marker) {

        this.markers[deviceId] = marker;

        return marker;

    };

    GPSTracker.deleteMarker = function (deviceId) {

        delete this.markers[deviceId];

    };

    GPSTracker.getLatLng = function (vehicle) {

        return [

            Number(vehicle.latitude),

            Number(vehicle.longitude),

        ];

    };

    GPSTracker.isValidCoordinate = function (vehicle) {

        return (

            vehicle &&

            vehicle.latitude !== null &&

            vehicle.longitude !== null &&

            !isNaN(vehicle.latitude) &&

            !isNaN(vehicle.longitude)

        );

    };

    GPSTracker.getStatusText = function (vehicle) {

        return vehicle.is_active

            ? 'Online'

            : 'Offline';

    };

    GPSTracker.getStatusColor = function (vehicle) {

        return vehicle.is_active

            ? 'text-green-600'

            : 'text-red-600';

    };

    GPSTracker.getSpeed = function (vehicle) {

        return Number(vehicle.speed ?? 0);

    };

    GPSTracker.getBattery = function (vehicle) {

        return Number(vehicle.battery ?? 0);

    };

    /*
    |--------------------------------------------------------------------------
    | Vehicle Icon
    |--------------------------------------------------------------------------
    */

    GPSTracker.createVehicleIcon = function (vehicle) {

        return L.divIcon({

            className: '',

            html: `

                <div class="relative">

                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-full border border-slate-200 bg-white shadow-lg">

                        <img
                            src="/images/marker/car.png"
                            class="h-7 w-7"
                            draggable="false">

                    </div>

                    <span
                        class="absolute -right-1 -top-1 h-3.5 w-3.5 rounded-full border-2 border-white ${vehicle.is_active ? 'bg-green-500' : 'bg-red-500'}">

                    </span>

                </div>

            `,

            iconSize: this.markerConfig.iconSize,

            iconAnchor: this.markerConfig.iconAnchor,

            popupAnchor: this.markerConfig.popupAnchor,

        });

    };

    /*
    |--------------------------------------------------------------------------
    | Vehicle Popup
    |--------------------------------------------------------------------------
    */

    GPSTracker.createPopup = function (vehicle) {

        return `

            <div class="w-[240px]">

                <div class="border-b border-slate-200 pb-3">

                    <div class="text-base font-bold text-slate-900">

                        ${vehicle.vehicle_name ?? '-'}

                    </div>

                    <div class="mt-1 text-sm text-slate-500">

                        ${vehicle.plate_number ?? '-'}

                    </div>

                </div>

                <div class="mt-4 space-y-2">

                    <div class="flex justify-between">

                        <span>Status</span>

                        <span class="${this.getStatusColor(vehicle)} font-semibold">

                            ${this.getStatusText(vehicle)}

                        </span>

                    </div>

                    <div class="flex justify-between">

                        <span>Kecepatan</span>

                        <span>

                            ${this.getSpeed(vehicle)} km/jam

                        </span>

                    </div>

                    <div class="flex justify-between">

                        <span>Baterai</span>

                        <span>

                            ${this.getBattery(vehicle)} %

                        </span>

                    </div>

                </div>

                <a
                    href="${GPSTracker.routes.vehicle}/${vehicle.device_id}"
                    class="mt-5 flex w-full items-center justify-center rounded-xl bg-[#2563EB] px-4 py-2 text-sm font-semibold text-white">

                    Detail Kendaraan

                </a>

            </div>

        `;

    };

    /*
    |--------------------------------------------------------------------------
    | Create Marker
    |--------------------------------------------------------------------------
    */

    GPSTracker.createMarker = function (vehicle) {

        if (!this.isValidCoordinate(vehicle)) {

            return null;

        }

        if (this.hasMarker(vehicle.device_id)) {

            return this.getMarker(vehicle.device_id);

        }

        const marker = L.marker(

            this.getLatLng(vehicle),

            {

                icon: this.createVehicleIcon(vehicle),

                rotationAngle: vehicle.heading ?? 0,

            }

        );

        marker.bindPopup(

            this.createPopup(vehicle)

        );
                marker.addTo(

            this.markerGroup

        );

        this.setMarker(

            vehicle.device_id,

            marker

        );

        return marker;

    };

    /*
    |--------------------------------------------------------------------------
    | Render Markers
    |--------------------------------------------------------------------------
    */

    GPSTracker.renderMarkers = function () {

        if (!Array.isArray(this.vehicles)) {

            return;

        }

        this.vehicles.forEach(vehicle => {

            this.createMarker(vehicle);

        });

    };

    /*
    |--------------------------------------------------------------------------
    | Update Marker Position
    |--------------------------------------------------------------------------
    */

    GPSTracker.updateMarkerPosition = function (marker, vehicle) {

        marker.setLatLng(

            this.getLatLng(vehicle)

        );

        if (

            typeof marker.setRotationAngle === 'function'

        ) {

            marker.setRotationAngle(

                vehicle.heading ?? 0

            );

        }

    };

    /*
    |--------------------------------------------------------------------------
    | Update Marker Icon
    |--------------------------------------------------------------------------
    */

    GPSTracker.updateMarkerIcon = function (marker, vehicle) {

        marker.setIcon(

            this.createVehicleIcon(vehicle)

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Update Marker Popup
    |--------------------------------------------------------------------------
    */

    GPSTracker.updateMarkerPopup = function (marker, vehicle) {

        marker.setPopupContent(

            this.createPopup(vehicle)

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Update Marker
    |--------------------------------------------------------------------------
    */

    GPSTracker.updateMarker = function (vehicle) {

        if (!this.isValidCoordinate(vehicle)) {

            return;

        }

        let marker = this.getMarker(

            vehicle.device_id

        );

        if (!marker) {

            marker = this.createMarker(vehicle);

            return;

        }

        this.updateMarkerPosition(

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
    | Refresh All Marker
    |--------------------------------------------------------------------------
    */

    GPSTracker.refreshMarkers = function () {

        if (!Array.isArray(this.vehicles)) {

            return;

        }

        this.vehicles.forEach(vehicle => {

            this.updateMarker(vehicle);

        });

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

        this.markerGroup.removeLayer(

            marker

        );

        this.deleteMarker(

            deviceId

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Remove All Marker
    |--------------------------------------------------------------------------
    */

    GPSTracker.clearMarkers = function () {

        Object.values(

            this.markers

        ).forEach(marker => {

            this.markerGroup.removeLayer(

                marker

            );

        });

        this.markers = {};

    };

    /*
    |--------------------------------------------------------------------------
    | Close Popup
    |--------------------------------------------------------------------------
    */

    GPSTracker.closeAllPopup = function () {

        Object.values(

            this.markers

        ).forEach(marker => {

            marker.closePopup();

        });

    };

    /*
    |--------------------------------------------------------------------------
    | Focus Marker
    |--------------------------------------------------------------------------
    */

    GPSTracker.focusMarker = function (deviceId) {

        const marker = this.getMarker(

            deviceId

        );

        if (!marker) {

            return;

        }

        this.closeAllPopup();

        this.map.flyTo(

            marker.getLatLng(),

            this.markerConfig.defaultZoom,

            {

                animate: true,

                duration: this.markerConfig.animationDuration,

            }

        );

        marker.openPopup();

    };

    /*
    |--------------------------------------------------------------------------
    | Fit Vehicle Bounds
    |--------------------------------------------------------------------------
    */
    GPSTracker.fitVehicles = function () {

        const markers = Object.values(

            this.markers

        );

        if (!markers.length) {

            return;

        }

        if (markers.length === 1) {

            this.map.setView(

                markers[0].getLatLng(),

                this.markerConfig.fitZoom

            );

            return;

        }

        const group = L.featureGroup(

            markers

        );

        this.map.fitBounds(

            group.getBounds(),

            {

                padding: this.markerConfig.fitPadding,

                animate: true,

            }

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Replace Vehicle
    |--------------------------------------------------------------------------
    */

    GPSTracker.replaceVehicle = function (vehicle) {

        const index = this.vehicles.findIndex(item => {

            return item.device_id === vehicle.device_id;

        });

        if (index === -1) {

            this.vehicles.push(

                vehicle

            );

        } else {

            this.vehicles[index] = {

                ...this.vehicles[index],

                ...vehicle,

            };

        }

        this.updateMarker(

            vehicle

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Add Vehicle
    |--------------------------------------------------------------------------
    */

    GPSTracker.addVehicle = function (vehicle) {

        const exists = this.vehicles.some(item => {

            return item.device_id === vehicle.device_id;

        });

        if (exists) {

            this.replaceVehicle(

                vehicle

            );

            return;

        }

        this.vehicles.push(

            vehicle

        );

        this.createMarker(

            vehicle

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Remove Vehicle
    |--------------------------------------------------------------------------
    */

    GPSTracker.removeVehicle = function (deviceId) {

        this.vehicles = this.vehicles.filter(vehicle => {

            return vehicle.device_id !== deviceId;

        });

        this.removeMarker(

            deviceId

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Get Visible Marker Count
    |--------------------------------------------------------------------------
    */

    GPSTracker.getMarkerCount = function () {

        return Object.keys(

            this.markers

        ).length;

    };

    /*
    |--------------------------------------------------------------------------
    | Get Vehicle Count
    |--------------------------------------------------------------------------
    */

    GPSTracker.getVehicleCount = function () {

        return this.vehicles.length;

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
    | Refresh Vehicle Collection
    |--------------------------------------------------------------------------
    */

    GPSTracker.setVehicles = function (vehicles) {

        this.vehicles = Array.isArray(vehicles)

            ? vehicles

            : [];

        this.rebuildMarkers();

    };

    /*
    |--------------------------------------------------------------------------
    | Refresh Single Vehicle
    |--------------------------------------------------------------------------
    */

    GPSTracker.syncVehicle = function (vehicle) {

        this.replaceVehicle(

            vehicle

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Initial Render
    |--------------------------------------------------------------------------
    */

    GPSTracker.renderMarkers();

    GPSTracker.fitVehicles();

    /*
    |--------------------------------------------------------------------------
    | Expose Helper
    |--------------------------------------------------------------------------
    */

    window.focusVehicle = function (deviceId) {

        GPSTracker.focusMarker(

            deviceId

        );

    };

    window.refreshMarkers = function () {

        GPSTracker.refreshMarkers();

    };

    window.syncVehicle = function (vehicle) {

        GPSTracker.syncVehicle(

            vehicle

        );

    };

});

</script>