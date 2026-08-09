<script>

window.GPSTracker = {

    /*
    |--------------------------------------------------------------------------
    | Application State
    |--------------------------------------------------------------------------
    */

    initialized: false,

    /*
    |--------------------------------------------------------------------------
    | Configuration
    |--------------------------------------------------------------------------
    */

    config: {

        defaultZoom: 17,

        fitZoom: 16,

        animationDuration: 1.2,

        fitPadding: [60, 60],

    },

    /*
    |--------------------------------------------------------------------------
    | Leaflet Instance
    |--------------------------------------------------------------------------
    */

    map: null,

    markerLayer: null,

    radiusLayer: null,

    administrativeLayer: null,

    routeLayer: null,

    temporaryLayer: null,
    
    baseLayers: {},

    /*
    |--------------------------------------------------------------------------
    | Collections
    |--------------------------------------------------------------------------
    */

    vehicles: Array.isArray(

        window.Home?.vehicles

    )

        ? [...window.Home.vehicles]

        : [],

    geofences: Array.isArray(

        window.Home?.geofences

    )

        ? [...window.Home.geofences]

        : [],

    notifications: Array.isArray(

        window.Home?.notifications

    )

        ? [...window.Home.notifications]

        : [],

    /*
    |--------------------------------------------------------------------------
    | Internal Index
    |--------------------------------------------------------------------------
    */

    vehicleIndex: new Map(),

    geofenceIndex: new Map(),

    notificationIndex: new Map(),

    /*
    |--------------------------------------------------------------------------
    | Configuration Helper
    |--------------------------------------------------------------------------
    */

    getConfig() {

        return this.config;

    },

    /*
    |--------------------------------------------------------------------------
    | Collection Getter
    |--------------------------------------------------------------------------
    */

    getVehicles() {

        return this.vehicles;

    },

    getGeofences() {

        return this.geofences;

    },

    getNotifications() {

        return this.notifications;

    },

    /*
    |--------------------------------------------------------------------------
    | Collection Setter
    |--------------------------------------------------------------------------
    */

    setVehicles(

        vehicles = []

    ) {

        this.vehicles = Array.isArray(

            vehicles

        )

            ? vehicles

            : [];

        return this.vehicles;

    },

    setGeofences(

        geofences = []

    ) {

        this.geofences = Array.isArray(

            geofences

        )

            ? geofences

            : [];

        return this.geofences;

    },

    setNotifications(

        notifications = []

    ) {

        this.notifications = Array.isArray(

            notifications

        )

            ? notifications

            : [];

        return this.notifications;

    },

    /*
    |--------------------------------------------------------------------------
    | Index Getter
    |--------------------------------------------------------------------------
    */

    getVehicleIndex() {

        return this.vehicleIndex;

    },

    getGeofenceIndex() {

        return this.geofenceIndex;

    },

    getNotificationIndex() {

        return this.notificationIndex;

    },

    /*
    |--------------------------------------------------------------------------
    | Application State
    |--------------------------------------------------------------------------
    */

    isInitialized() {

        return this.initialized;

    },

    setInitialized(

        status = true

    ) {

        this.initialized = Boolean(

            status

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Coordinate Helper
    |--------------------------------------------------------------------------
    */

    isValidCoordinate(

        latitude,

        longitude

    ) {

        return (

            latitude !== null &&

            longitude !== null &&

            !Number.isNaN(

                Number(

                    latitude

                )

            ) &&

            !Number.isNaN(

                Number(

                    longitude

                )

            )

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Logger
    |--------------------------------------------------------------------------
    */

    appLog(

        ...message

    ) {

        console.log(

            '[App]',

            ...message

        );

    },

    appWarn(

        ...message

    ) {

        console.warn(

            '[App]',

            ...message

        );

    },

    appError(

        ...message

    ) {

        console.error(

            '[App]',

            ...message

        );

    },
        /*
    |--------------------------------------------------------------------------
    | Vehicle Index
    |--------------------------------------------------------------------------
    */

    buildVehicleIndex() {

        this.vehicleIndex.clear();

        this.vehicles.forEach(

            vehicle => {

                if (

                    !vehicle?.device_id

                ) {

                    return;

                }

                this.vehicleIndex.set(

                    String(

                        vehicle.device_id

                    ),

                    vehicle

                );

            }

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Geofence Index
    |--------------------------------------------------------------------------
    */

    buildGeofenceIndex() {

        this.geofenceIndex.clear();

        this.geofences.forEach(

            geofence => {

                if (

                    !geofence?.id

                ) {

                    return;

                }

                this.geofenceIndex.set(

                    String(

                        geofence.id

                    ),

                    geofence

                );

            }

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Notification Index
    |--------------------------------------------------------------------------
    */

    buildNotificationIndex() {

        this.notificationIndex.clear();

        this.notifications.forEach(

            notification => {

                if (

                    !notification?.id

                ) {

                    return;

                }

                this.notificationIndex.set(

                    String(

                        notification.id

                    ),

                    notification

                );

            }

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Vehicle Service
    |--------------------------------------------------------------------------
    */

    getVehicle(

        deviceId

    ) {

        return this.vehicleIndex.get(

            String(

                deviceId

            )

        ) ?? null;

    },

    getVehicleByDeviceId(

        deviceId

    ) {

        return this.getVehicle(

            deviceId

        );

    },

    findVehicle(

        deviceId

    ) {

        return this.getVehicle(

            deviceId

        );

    },

    hasVehicle(

        deviceId

    ) {

        return this.vehicleIndex.has(

            String(

                deviceId

            )

        );

    },

    addVehicle(

        vehicle

    ) {

        if (

            !vehicle?.device_id

        ) {

            return null;

        }

        this.vehicles.push(

            vehicle

        );

        this.vehicleIndex.set(

            String(

                vehicle.device_id

            ),

            vehicle

        );

        return vehicle;

    },

    replaceVehicle(

        vehicle

    ) {

        if (

            !vehicle?.device_id

        ) {

            return null;

        }

        const deviceId = String(

            vehicle.device_id

        );

        const current =

            this.getVehicle(

                deviceId

            );

        if (

            current

        ) {

            Object.assign(

                current,

                vehicle

            );

            this.vehicleIndex.set(

                deviceId,

                current

            );

            return current;

        }

        return this.addVehicle(

            vehicle

        );

    },

    updateVehicle(

        deviceId,

        attributes = {}

    ) {

        const vehicle =

            this.getVehicle(

                deviceId

            );

        if (

            !vehicle

        ) {

            return null;

        }

        Object.assign(

            vehicle,

            attributes

        );

        return vehicle;

    },

    removeVehicle(

        deviceId

    ) {

        deviceId = String(

            deviceId

        );

        this.vehicles = this.vehicles.filter(

            vehicle => {

                return String(

                    vehicle.device_id

                ) !== deviceId;

            }

        );

        this.vehicleIndex.delete(

            deviceId

        );

    },

    clearVehicles() {

        this.vehicles = [];

        this.vehicleIndex.clear();

    },

    /*
    |--------------------------------------------------------------------------
    | Geofence Service
    |--------------------------------------------------------------------------
    */

    getGeofence(

        id

    ) {

        return this.geofenceIndex.get(

            String(

                id

            )

        ) ?? null;

    },

    hasGeofence(

        id

    ) {

        return this.geofenceIndex.has(

            String(

                id

            )

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Notification Service
    |--------------------------------------------------------------------------
    */

    getNotification(

        id

    ) {

        return this.notificationIndex.get(

            String(

                id

            )

        ) ?? null;

    },

    hasNotification(

        id

    ) {

        return this.notificationIndex.has(

            String(

                id

            )

        );

    },

    addNotification(

        notification

    ) {

        this.notifications.unshift(

            notification

        );

        if (

            notification?.id

        ) {

            this.notificationIndex.set(

                String(

                    notification.id

                ),

                notification

            );

        }

        return notification;

    },

    removeNotification(

        id

    ) {

        id = String(

            id

        );

        this.notifications = this.notifications.filter(

            notification => {

                return String(

                    notification.id

                ) !== id;

            }

        );

        this.notificationIndex.delete(

            id

        );

    },
        /*
    |--------------------------------------------------------------------------
    | Collection Synchronization
    |--------------------------------------------------------------------------
    */

    synchronizeVehicleIndex() {

        this.vehicleIndex.clear();

        this.vehicles.forEach(

            vehicle => {

                if (

                    !vehicle?.device_id

                ) {

                    return;

                }

                this.vehicleIndex.set(

                    String(

                        vehicle.device_id

                    ),

                    vehicle

                );

            }

        );

    },

    synchronizeGeofenceIndex() {

        this.geofenceIndex.clear();

        this.geofences.forEach(

            geofence => {

                if (

                    !geofence?.id

                ) {

                    return;

                }

                this.geofenceIndex.set(

                    String(

                        geofence.id

                    ),

                    geofence

                );

            }

        );

    },

    synchronizeNotificationIndex() {

        this.notificationIndex.clear();

        this.notifications.forEach(

            notification => {

                if (

                    !notification?.id

                ) {

                    return;

                }

                this.notificationIndex.set(

                    String(

                        notification.id

                    ),

                    notification

                );

            }

        );

    },

    synchronizeCollections() {

        this.synchronizeVehicleIndex();

        this.synchronizeGeofenceIndex();

        this.synchronizeNotificationIndex();

    },

    /*
    |--------------------------------------------------------------------------
    | Collection Setter
    |--------------------------------------------------------------------------
    */

    setVehicles(

        vehicles = []

    ) {

        this.vehicles = Array.isArray(

            vehicles

        )

            ? [

                ...vehicles,

            ]

            : [];

        this.synchronizeVehicleIndex();

        return this.vehicles;

    },

    setGeofences(

        geofences = []

    ) {

        this.geofences = Array.isArray(

            geofences

        )

            ? [

                ...geofences,

            ]

            : [];

        this.synchronizeGeofenceIndex();

        return this.geofences;

    },

    setNotifications(

        notifications = []

    ) {

        this.notifications = Array.isArray(

            notifications

        )

            ? [

                ...notifications,

            ]

            : [];

        this.synchronizeNotificationIndex();

        return this.notifications;

    },

    /*
    |--------------------------------------------------------------------------
    | Map Helper
    |--------------------------------------------------------------------------
    */

    getMap() {

        return this.map;

    },

    hasMap() {

        return this.map !== null;

    },

    getMarkerLayer() {

        return this.markerLayer;

    },

    getRadiusLayer() {

        return this.radiusLayer;

    },

    getAdministrativeLayer() {

        return this.administrativeLayer;

    },

    getRouteLayer() {

        return this.routeLayer;

    },

    getTemporaryLayer() {

        return this.temporaryLayer;

    },

    /*
    |--------------------------------------------------------------------------
    | Map Navigation
    |--------------------------------------------------------------------------
    */

    flyToLocation(

        latitude,

        longitude,

        zoom = null

    ) {

        if (

            !this.hasMap()

        ) {

            return;

        }

        if (

            !this.isValidCoordinate(

                latitude,

                longitude

            )

        ) {

            return;

        }

        this.map.flyTo(

            [

                Number(

                    latitude

                ),

                Number(

                    longitude

                )

            ],

            zoom ??

            this.config.defaultZoom,

            {

                animate: true,

                duration:

                    this.config.animationDuration,

            }

        );

    },

    panTo(

        latitude,

        longitude

    ) {

        if (

            !this.hasMap()

        ) {

            return;

        }

        if (

            !this.isValidCoordinate(

                latitude,

                longitude

            )

        ) {

            return;

        }

        this.map.panTo(

            [

                Number(

                    latitude

                ),

                Number(

                    longitude

                )

            ]

        );

    },

    zoomTo(

        zoom

    ) {

        if (

            !this.hasMap()

        ) {

            return;

        }

        this.map.setZoom(

            Number(

                zoom

            )

        );

    },

    fitBounds(

        bounds

    ) {

        if (

            !this.hasMap()

        ) {

            return;

        }

        this.map.fitBounds(

            bounds,

            {

                padding:

                    this.config.fitPadding,

            }

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Layer Helper
    |--------------------------------------------------------------------------
    */

    clearLayer(

        layer

    ) {

        if (

            !layer ||

            typeof layer.clearLayers !== 'function'

        ) {

            return;

        }

        layer.clearLayers();

    },

    clearTemporary() {

        this.clearLayer(

            this.temporaryLayer

        );

    },

    clearAllLayers() {

        this.clearLayer(

            this.markerLayer

        );

        this.clearLayer(

            this.radiusLayer

        );

        this.clearLayer(

            this.administrativeLayer

        );

        this.clearLayer(

            this.routeLayer

        );

        this.clearLayer(

            this.temporaryLayer

        );

    },
        /*
    |--------------------------------------------------------------------------
    | Vehicle Navigation
    |--------------------------------------------------------------------------
    */

    getVehicleCoordinate(

        deviceId

    ) {

        const vehicle =

            this.getVehicle(

                deviceId

            );

        if (

            !vehicle

        ) {

            return null;

        }

        if (

            !this.isValidCoordinate(

                vehicle.latitude,

                vehicle.longitude

            )

        ) {

            return null;

        }

        return {

            latitude: Number(

                vehicle.latitude

            ),

            longitude: Number(

                vehicle.longitude

            ),

        };

    },

    getVehicleLatLng(

        deviceId

    ) {

        const coordinate =

            this.getVehicleCoordinate(

                deviceId

            );

        if (

            !coordinate

        ) {

            return null;

        }

        return L.latLng(

            coordinate.latitude,

            coordinate.longitude

        );

    },

    hasVehicleCoordinate(

        deviceId

    ) {

        return (

            this.getVehicleCoordinate(

                deviceId

            ) !== null

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Vehicle Map Navigation
    |--------------------------------------------------------------------------
    */

    flyToVehicle(

        deviceId,

        zoom = null

    ) {

        const coordinate =

            this.getVehicleCoordinate(

                deviceId

            );

        if (

            !coordinate

        ) {

            return;

        }

        this.flyToLocation(

            coordinate.latitude,

            coordinate.longitude,

            zoom

        );

    },

    panToVehicle(

        deviceId

    ) {

        const coordinate =

            this.getVehicleCoordinate(

                deviceId

            );

        if (

            !coordinate

        ) {

            return;

        }

        this.panTo(

            coordinate.latitude,

            coordinate.longitude

        );

    },

    zoomToVehicle(

        deviceId,

        zoom

    ) {

        if (

            !this.hasVehicleCoordinate(

                deviceId

            )

        ) {

            return;

        }

        this.zoomTo(

            zoom

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Vehicle Focus
    |--------------------------------------------------------------------------
    */

    focusVehicle(

        deviceId,

        zoom = null

    ) {

        const vehicle =

            this.getVehicle(

                deviceId

            );

        if (

            !vehicle

        ) {

            return null;

        }

        this.flyToVehicle(

            deviceId,

            zoom

        );

        document.dispatchEvent(

            new CustomEvent(

                'gpstracker:vehicle-focused',

                {

                    detail: {

                        vehicle,

                    },

                }

            )

        );

        return vehicle;

    },

    focusVehicles(

        deviceIds = []

    ) {

        const bounds = [];

        deviceIds.forEach(

            deviceId => {

                const latlng =

                    this.getVehicleLatLng(

                        deviceId

                    );

                if (

                    latlng

                ) {

                    bounds.push(

                        latlng

                    );

                }

            }

        );

        if (

            !bounds.length

        ) {

            return;

        }

        this.fitBounds(

            L.latLngBounds(

                bounds

            )

        );

    },
        /*
    |--------------------------------------------------------------------------
    | Application Event
    |--------------------------------------------------------------------------
    */

    dispatch(

        name,

        detail = {}

    ) {

        document.dispatchEvent(

            new CustomEvent(

                name,

                {

                    detail,

                }

            )

        );

    },

    listen(

        name,

        callback

    ) {

        document.addEventListener(

            name,

            callback

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Vehicle Event
    |--------------------------------------------------------------------------
    */

    dispatchVehicleCreated(

        vehicle

    ) {

        this.dispatch(

            'gpstracker:vehicle-created',

            {

                vehicle,

            }

        );

    },

    dispatchVehicleUpdated(

        vehicle

    ) {

        this.dispatch(

            'gpstracker:vehicle-updated',

            {

                vehicle,

            }

        );

    },

    dispatchVehicleRemoved(

        vehicle

    ) {

        this.dispatch(

            'gpstracker:vehicle-removed',

            {

                vehicle,

            }

        );

    },

    dispatchVehicleFocused(

        vehicle

    ) {

        this.dispatch(

            'gpstracker:vehicle-focused',

            {

                vehicle,

            }

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Notification Event
    |--------------------------------------------------------------------------
    */

    dispatchNotificationCreated(

        notification

    ) {

        this.dispatch(

            'gpstracker:notification-created',

            {

                notification,

            }

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Geofence Event
    |--------------------------------------------------------------------------
    */

    dispatchGeofenceUpdated(

        geofence

    ) {

        this.dispatch(

            'gpstracker:geofence-updated',

            {

                geofence,

            }

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Map Event
    |--------------------------------------------------------------------------
    */

    dispatchMapReady() {

        this.dispatch(

            'gpstracker:map-ready'

        );

    },

    dispatchAppReady() {

        this.dispatch(

            'gpstracker:app-ready'

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Map Loading
    |--------------------------------------------------------------------------
    */

    showMapLoading() {

        const loading = document.getElementById('mapLoading');

        if (!loading) {

            return;

        }

        loading.classList.remove('hidden');

    },

    hideMapLoading() {

        const loading = document.getElementById('mapLoading');

        if (!loading) {

            return;

        }

        loading.classList.add('hidden');

    },
    
    /*
    |--------------------------------------------------------------------------
    | Application Bootstrap
    |--------------------------------------------------------------------------
    */

    initializeApplication() {

        if (

            this.isInitialized()

        ) {

            return;

        }

        this.appLog(

            'Initializing application...'

        );

        this.synchronizeCollections();

        this.setInitialized(

            true

        );

        this.dispatchAppReady();

        this.appLog(

            'Application initialized.'

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Application Reset
    |--------------------------------------------------------------------------
    */

    resetApplication() {

        this.clearVehicles();

        this.setGeofences(

            []

        );

        this.setNotifications(

            []

        );

        this.clearAllLayers();

        this.setInitialized(

            false

        );

        this.appLog(

            'Application reset.'

        );

    },

    

    /*
    |--------------------------------------------------------------------------
    | Application Destroy
    |--------------------------------------------------------------------------
    */

    destroyApplication() {

        this.resetApplication();

        this.appLog(

            'Application destroyed.'

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Application Refresh
    |--------------------------------------------------------------------------
    */

    refreshApplication() {

        this.synchronizeCollections();

        this.dispatch(

            'gpstracker:application-refreshed'

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Application Reload
    |--------------------------------------------------------------------------
    */

    reloadApplication() {

        this.refreshApplication();

        this.dispatch(

            'gpstracker:application-reloaded'

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Bootstrap Event
    |--------------------------------------------------------------------------
    */

    boot() {

        this.initializeApplication();

    },
        /*
    |--------------------------------------------------------------------------
    | Entry Point
    |--------------------------------------------------------------------------
    */

    start() {

        this.boot();

    }

};

    GPSTracker.showTemporaryMarker = function (
        latitude,
        longitude,
        title = ''
    ) {

        if (!this.hasMap()) {
            return;
        }

        this.clearTemporary();

        const marker = L.marker([
            Number(latitude),
            Number(longitude)
        ]);

        if (title) {
            marker.bindPopup(title);
        }

        marker.addTo(this.getTemporaryLayer());

        marker.openPopup();

    };

    document.addEventListener(

        'DOMContentLoaded',

        () => {

            window.GPSTracker.start();

        }

    );

    /*
    |--------------------------------------------------------------------------
    | Close All Dropdowns
    |--------------------------------------------------------------------------
    */

    GPSTracker.closeDropdowns = function () {

        const dropdowns = [

            'geofenceDropdown',

            'notificationDropdown',

            'profileDropdown',

            'searchResult',

        ];

        dropdowns.forEach(id => {

            document
                .getElementById(id)
                ?.classList.add('hidden');

        });

        document
            .getElementById('geofenceArrow')
            ?.classList.remove('rotate-180');

        document
            .getElementById('profileArrow')
            ?.classList.remove('rotate-180');

    };

</script>