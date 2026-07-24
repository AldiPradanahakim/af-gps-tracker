<script>

document.addEventListener('gpstracker:map-ready', () => {

    /*
    |--------------------------------------------------------------------------
    | Realtime State
    |--------------------------------------------------------------------------
    */

    GPSTracker.realtime ??= {

        connected: false,

        connecting: false,

        reconnecting: false,

        subscribed: false,

        initialized: false,

        heartbeat: null,

        reconnectTimer: null,

        lastHeartbeat: null,

        lastPayload: null,

        lastVehicle: null,

        lastUpdate: null,

        echo: null,

        channels: {},

    };

    /*
    |--------------------------------------------------------------------------
    | Realtime Configuration
    |--------------------------------------------------------------------------
    */

    GPSTracker.realtimeConfig = {

        enabled: true,

        reconnectDelay: 5000,

        heartbeatInterval: 30000,

    };

    /*
    |--------------------------------------------------------------------------
    | Getter
    |--------------------------------------------------------------------------
    */

    GPSTracker.getRealtime = function () {

        return this.realtime;

    };

    GPSTracker.getRealtimeConfig = function () {

        return this.realtimeConfig;

    };

    GPSTracker.getRealtimeEcho = function () {

        return window.Echo ?? null;

    };

    GPSTracker.getRealtimeChannels = function () {

        return this.realtime.channels;

    };

    GPSTracker.getLastRealtimePayload = function () {

        return this.realtime.lastPayload;

    };

    GPSTracker.getLastRealtimeVehicle = function () {

        return this.realtime.lastVehicle;

    };

    /*
    |--------------------------------------------------------------------------
    | Setter
    |--------------------------------------------------------------------------
    */

    GPSTracker.setRealtimeConnected = function (status = true) {

        this.realtime.connected = Boolean(status);

    };

    GPSTracker.setRealtimeConnecting = function (status = true) {

        this.realtime.connecting = Boolean(status);

    };

    GPSTracker.setRealtimeSubscribed = function (status = true) {

        this.realtime.subscribed = Boolean(status);

    };

    GPSTracker.setRealtimeInitialized = function (status = true) {

        this.realtime.initialized = Boolean(status);

    };

    GPSTracker.setRealtimePayload = function (payload = null) {

        this.realtime.lastPayload = payload;

    };

    GPSTracker.setRealtimeVehicle = function (vehicle = null) {

        this.realtime.lastVehicle = vehicle;

    };

    GPSTracker.setRealtimeUpdateTime = function () {

        this.realtime.lastUpdate = new Date();

    };

    /*
    |--------------------------------------------------------------------------
    | Checker
    |--------------------------------------------------------------------------
    */

    GPSTracker.isRealtimeConnected = function () {

        return this.realtime.connected;

    };

    GPSTracker.isRealtimeInitialized = function () {

        return this.realtime.initialized;

    };

    /*
    |--------------------------------------------------------------------------
    | Logger
    |--------------------------------------------------------------------------
    */

    GPSTracker.realtimeLog = function (...args) {

        console.log('[Realtime]', ...args);

    };

    GPSTracker.realtimeWarn = function (...args) {

        console.warn('[Realtime]', ...args);

    };

    GPSTracker.realtimeError = function (...args) {

        console.error('[Realtime]', ...args);

    };

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    GPSTracker.canStartRealtime = function () {

        if (!this.realtimeConfig.enabled) {

            return false;

        }

        if (typeof window.Echo === 'undefined') {

            this.realtimeError(

                'Laravel Echo belum tersedia.'

            );

            return false;

        }

        if (!Array.isArray(this.vehicles)) {

            this.realtimeWarn(

                'Vehicle list kosong.'

            );

            return false;

        }

        return true;

    };

    /*
    |--------------------------------------------------------------------------
    | Connect Realtime
    |--------------------------------------------------------------------------
    */

    GPSTracker.connectRealtime = function () {

        if (!this.canStartRealtime()) {

            return;

        }

        if (this.isRealtimeConnected()) {

            return;

        }

        this.setRealtimeConnecting(true);

        try {

            this.realtime.echo = window.Echo;

            this.subscribeRealtimeChannels();

            this.setRealtimeConnected(true);

            this.setRealtimeInitialized(true);

            this.realtimeLog(

                'Realtime connected.'

            );

        } catch (error) {

            this.realtimeError(

                'Failed to connect realtime.',

                error

            );

        } finally {

            this.setRealtimeConnecting(false);

        }

    };

    /*
    |--------------------------------------------------------------------------
    | Disconnect Realtime
    |--------------------------------------------------------------------------
    */

    GPSTracker.disconnectRealtime = function () {

        const echo = this.getRealtimeEcho();

        if (!echo) {

            return;

        }

        Object.keys(

            this.realtime.channels

        ).forEach(channel => {

            echo.leave(

                channel

            );

        });

        this.realtime.channels = {};

        this.stopRealtimeHeartbeat();

        this.setRealtimeConnected(

            false

        );

        this.setRealtimeSubscribed(

            false

        );

        this.setRealtimeInitialized(

            false

        );

    };
    
    /*
    |--------------------------------------------------------------------------
    | Subscribe All Device Channels
    |--------------------------------------------------------------------------
    */

    GPSTracker.subscribeRealtimeChannels = function () {

        const echo = this.getRealtimeEcho();

        if (!echo) {

            return;

        }

        this.vehicles.forEach(vehicle => {

            const channelName = `vehicle.${vehicle.device_id}`;

            if (this.realtime.channels[channelName]) {

                return;

            }

            this.realtime.channels[channelName] = echo

                .private(channelName)

                .listen('.location.updated', (payload) => {

                    this.receiveRealtimePayload(payload);

                });

            this.realtimeLog(

                'Subscribed:',

                channelName

            );

        });

        this.setRealtimeSubscribed(true);

    };
    GPSTracker.receiveRealtimePayload = function (payload) {

        if (!payload) {

            return;

        }

        this.setRealtimePayload(

            payload

        );

        this.setRealtimeUpdateTime();

        const vehicle = payload;

        if (

            !vehicle ||

            !vehicle.device_id

        ) {

            return;

        }

        this.setRealtimeVehicle(

            vehicle

        );

        this.processRealtimeVehicle(

            vehicle

        );

    };



    /*
    |--------------------------------------------------------------------------
    | Process Vehicle
    |--------------------------------------------------------------------------
    */
    GPSTracker.processRealtimeVehicle = function (vehicle) {

        const index = this.vehicles.findIndex(item => {

            return String(

                item.device_id

            ) === String(

                vehicle.device_id

            );

        });

        if (index === -1) {

            this.realtimeWarn(

                'Vehicle tidak ditemukan.',

                vehicle.device_id

            );

            return;

        }

        this.syncRealtimeVehicle(

            index,

            vehicle

        );

    };



    /*
    |--------------------------------------------------------------------------
    | Sync Vehicle
    |--------------------------------------------------------------------------
    */

    GPSTracker.syncRealtimeVehicle = function (index, vehicle) {

        const current = this.vehicles[index];

        this.vehicles[index] = {

            ...current,

            ...vehicle,

        };

        this.refreshRealtimeModules(

            this.vehicles[index]

        );

    };



    /*
    |--------------------------------------------------------------------------
    | Refresh Modules
    |--------------------------------------------------------------------------
    */

    GPSTracker.refreshRealtimeModules = function (vehicle) {

        this.refreshRealtimeMarker(

            vehicle

        );

        this.refreshRealtimePopup(

            vehicle

        );

        this.refreshRealtimeSidebar(

            vehicle

        );

        this.refreshRealtimeSearch(

            vehicle

        );

        this.refreshRealtimeNotification(

            vehicle

        );

        this.refreshRealtimeVehicleDetail(

            vehicle

        );

        this.refreshRealtimeHomeLocation(

            vehicle

        );

        this.refreshRealtimeGeofence(

            vehicle

        );

        this.refreshRealtimeStopDetection(

            vehicle

        );

        this.refreshRealtimeAddress(

            vehicle

        );

        this.refreshRealtimeMap(

            vehicle

        );

    };



    /*
    |--------------------------------------------------------------------------
    | Replace Vehicle
    |--------------------------------------------------------------------------
    */

    GPSTracker.replaceVehicle = function (vehicle) {

        const index = this.vehicles.findIndex(item => {

            return Number(item.device_id) === Number(vehicle.device_id);

        });

        if (index === -1) {

            return false;

        }

        this.vehicles[index] = {

            ...this.vehicles[index],

            ...vehicle,

        };

        return true;

    };



    /*
    |--------------------------------------------------------------------------
    | Find Vehicle
    |--------------------------------------------------------------------------
    */

    GPSTracker.findRealtimeVehicle = function (deviceId) {

        return this.vehicles.find(vehicle => {

            return String(

                vehicle.device_id

            ) === String(

                deviceId

            );

        });

    };



    /*
    |--------------------------------------------------------------------------
    | Find Vehicle Index
    |--------------------------------------------------------------------------
    */

    GPSTracker.findRealtimeVehicleIndex = function (deviceId) {

        return this.vehicles.findIndex(vehicle => {

            return String(

                vehicle.device_id

            ) === String(

                deviceId

            );

        });

    };



    /*
    |--------------------------------------------------------------------------
    | Update Vehicle State
    |--------------------------------------------------------------------------
    */

    GPSTracker.updateRealtimeVehicleState = function (vehicle) {

        this.setRealtimeVehicle(

            vehicle

        );

        this.setRealtimePayload(

            vehicle

        );

        this.setRealtimeUpdateTime();

    };

    /*
    |--------------------------------------------------------------------------
    | Refresh Marker
    |--------------------------------------------------------------------------
    */

    GPSTracker.refreshRealtimeMarker = function (vehicle) {

        if (

            typeof this.updateMarker !== 'function'

        ) {

            return;

        }

        this.updateMarker(

            vehicle

        );

    };



    /*
    |--------------------------------------------------------------------------
    | Refresh Popup
    |--------------------------------------------------------------------------
    */

    GPSTracker.refreshRealtimePopup = function (vehicle) {

        if (

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
    | Refresh Sidebar
    |--------------------------------------------------------------------------
    */

    GPSTracker.refreshRealtimeSidebar = function (vehicle) {

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
    | Refresh Search
    |--------------------------------------------------------------------------
    */

    GPSTracker.refreshRealtimeSearch = function (vehicle) {

        if (

            typeof this.updateSearchResult !== 'function'

        ) {

            return;

        }

        this.updateSearchResult(

            vehicle

        );

    };



    /*
    |--------------------------------------------------------------------------
    | Refresh Notification
    |--------------------------------------------------------------------------
    */

    GPSTracker.refreshRealtimeNotification = function (vehicle) {

        if (

            typeof this.receiveNotification !== 'function'

        ) {

            return;

        }

        if (

            !vehicle.notification

        ) {

            return;

        }

        this.receiveNotification(

            vehicle.notification

        );

    };



    /*
    |--------------------------------------------------------------------------
    | Refresh Vehicle Detail
    |--------------------------------------------------------------------------
    */

    GPSTracker.refreshRealtimeVehicleDetail = function (vehicle) {

        if (

            typeof this.updateVehicleDetail !== 'function'

        ) {

            return;

        }

        this.updateVehicleDetail(

            vehicle

        );

    };



    /*
    |--------------------------------------------------------------------------
    | Refresh Home Location
    |--------------------------------------------------------------------------
    */

    GPSTracker.refreshRealtimeHomeLocation = function (vehicle) {

        if (

            typeof this.updateHomeLocation !== 'function'

        ) {

            return;

        }

        this.updateHomeLocation(

            vehicle

        );

    };



    /*
    |--------------------------------------------------------------------------
    | Refresh Geofence
    |--------------------------------------------------------------------------
    */

    GPSTracker.refreshRealtimeGeofence = function (vehicle) {

        if (

            typeof this.checkGeofence !== 'function'

        ) {

            return;

        }

        this.checkGeofence(

            vehicle

        );

    };



    /*
    |--------------------------------------------------------------------------
    | Refresh Stop Detection
    |--------------------------------------------------------------------------
    */

    GPSTracker.refreshRealtimeStopDetection = function (vehicle) {

        if (

            typeof this.detectVehicleStop !== 'function'

        ) {

            return;

        }

        this.detectVehicleStop(

            vehicle

        );

    };



    /*
    |--------------------------------------------------------------------------
    | Refresh Reverse Geocoding
    |--------------------------------------------------------------------------
    */

    GPSTracker.refreshRealtimeAddress = function (vehicle) {

        if (

            typeof this.reverseGeocode !== 'function'

        ) {

            return;

        }

        this.reverseGeocode(

            vehicle

        );

    };



    /*
    |--------------------------------------------------------------------------
    | Refresh Map
    |--------------------------------------------------------------------------
    */

    GPSTracker.refreshRealtimeMap = function (vehicle) {

        if (

            !this.map

        ) {

            return;

        }

        const latitude = Number(

            vehicle.latitude

        );

        const longitude = Number(

            vehicle.longitude

        );

        if (

            Number.isNaN(latitude) ||

            Number.isNaN(longitude)

        ) {

            return;

        }

        if (

            this.followVehicle === true

        ) {

            this.map.panTo(

                [

                    latitude,

                    longitude,

                ],

                {

                    animate: true,

                }

            );

        }

    };



    /*
    |--------------------------------------------------------------------------
    | Refresh All Components
    |--------------------------------------------------------------------------
    */

    GPSTracker.refreshRealtimeModules = function (vehicle) {

        this.refreshRealtimeMarker(

            vehicle

        );

        this.refreshRealtimePopup(

            vehicle

        );

        this.refreshRealtimeSidebar(

            vehicle

        );

        this.refreshRealtimeSearch(

            vehicle

        );

        this.refreshRealtimeNotification(

            vehicle

        );

        this.refreshRealtimeVehicleDetail(

            vehicle

        );

        this.refreshRealtimeHomeLocation(

            vehicle

        );

        this.refreshRealtimeGeofence(

            vehicle

        );

        this.refreshRealtimeStopDetection(

            vehicle

        );

        this.refreshRealtimeAddress(

            vehicle

        );

        this.refreshRealtimeMap(

            vehicle

        );

    };
    /*
    |--------------------------------------------------------------------------
    | Heartbeat
    |--------------------------------------------------------------------------
    */

    GPSTracker.startRealtimeHeartbeat = function () {

        this.stopRealtimeHeartbeat();

        this.realtime.heartbeat = setInterval(() => {

            if (

                !this.isRealtimeConnected()

            ) {

                return;

            }

            this.realtime.lastHeartbeat = new Date();

            this.realtimeLog(

                'Heartbeat',

                this.realtime.lastHeartbeat.toLocaleTimeString()

            );

        },

        this.realtimeConfig.heartbeatInterval);

    };



    GPSTracker.stopRealtimeHeartbeat = function () {

        if (

            !this.realtime.heartbeat

        ) {

            return;

        }

        clearInterval(

            this.realtime.heartbeat

        );

        this.realtime.heartbeat = null;

    };



    /*
    |--------------------------------------------------------------------------
    | Reset Realtime
    |--------------------------------------------------------------------------
    */

    GPSTracker.resetRealtime = function () {

        this.stopRealtimeHeartbeat();

        clearTimeout(

            this.realtime.reconnectTimer

        );

        this.realtime.connected = false;

        this.realtime.connecting = false;

        this.realtime.reconnecting = false;

        this.realtime.subscribed = false;

        this.realtime.initialized = false;

        this.realtime.lastHeartbeat = null;

        this.realtime.lastPayload = null;

        this.realtime.lastVehicle = null;

        this.realtime.lastUpdate = null;

    };



    /*
    |--------------------------------------------------------------------------
    | Destroy Realtime
    |--------------------------------------------------------------------------
    */

    GPSTracker.destroyRealtime = function () {

        const echo = this.getRealtimeEcho();

        if (echo) {

            Object.keys(this.realtime.channels).forEach(channel => {

                echo.leave(channel);

            });

        }

        this.realtime.channels = {};

        this.resetRealtime();

    };



    /*
    |--------------------------------------------------------------------------
    | Window Events
    |--------------------------------------------------------------------------
    */

    GPSTracker.bindRealtimeWindowEvents = function () {

        if (

            this.realtime.windowEventsBound

        ) {

            return;

        }

        this.realtime.windowEventsBound = true;

        window.addEventListener(

            'online',

            () => {

                this.realtimeLog(

                    'Internet connected.'

                );

                this.connectRealtime();

            }

        );

        window.addEventListener(

            'offline',

            () => {

                this.realtimeWarn(

                    'Internet disconnected.'

                );

            }

        );

        window.addEventListener(

            'beforeunload',

            () => {

                this.destroyRealtime();

            }

        );

    };



    /*
    |--------------------------------------------------------------------------
    | Initialize
    |--------------------------------------------------------------------------
    */

    GPSTracker.initializeRealtime = function () {

        if (

            this.isRealtimeInitialized()

        ) {

            return;

        }

        this.realtimeLog(

            'Initialize realtime...'

        );

        this.connectRealtime();

        this.bindRealtimeWindowEvents();

        this.startRealtimeHeartbeat();

    };



/*
|--------------------------------------------------------------------------
| Auto Initialize
|--------------------------------------------------------------------------
*/

GPSTracker.initializeRealtime();

});

</script>