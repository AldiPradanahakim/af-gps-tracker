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

        pusher: null,

        channel: null,

    };

    /*
    |--------------------------------------------------------------------------
    | Realtime Configuration
    |--------------------------------------------------------------------------
    */

    GPSTracker.realtimeConfig = {

        enabled: true,

        driver: 'pusher',

        key: window.PUSHER_APP_KEY ?? '',

        cluster: window.PUSHER_APP_CLUSTER ?? '',

        channel: 'vehicle-location',

        event: 'vehicle.location.updated',

        forceTLS: true,

        encrypted: true,

        activityTimeout: 30000,

        pongTimeout: 15000,

        reconnectDelay: 5000,

        heartbeatInterval: 30000,

    };

    /*
    |--------------------------------------------------------------------------
    | Realtime Getter
    |--------------------------------------------------------------------------
    */

    GPSTracker.getRealtime = function () {

        return this.realtime;

    };

    GPSTracker.getRealtimeConfig = function () {

        return this.realtimeConfig;

    };

    GPSTracker.getRealtimeChannel = function () {

        return this.realtime.channel;

    };

    GPSTracker.getRealtimePusher = function () {

        return this.realtime.pusher;

    };

    GPSTracker.getLastRealtimePayload = function () {

        return this.realtime.lastPayload;

    };

    GPSTracker.getLastRealtimeVehicle = function () {

        return this.realtime.lastVehicle;

    };

    /*
    |--------------------------------------------------------------------------
    | Realtime Setter
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
    | Realtime Checker
    |--------------------------------------------------------------------------
    */

    GPSTracker.isRealtimeConnected = function () {

        return this.realtime.connected;

    };

    GPSTracker.isRealtimeConnecting = function () {

        return this.realtime.connecting;

    };

    GPSTracker.isRealtimeSubscribed = function () {

        return this.realtime.subscribed;

    };

    GPSTracker.isRealtimeInitialized = function () {

        return this.realtime.initialized;

    };

    /*
    |--------------------------------------------------------------------------
    | Logger
    |--------------------------------------------------------------------------
    */

    GPSTracker.realtimeLog = function (...message) {

        console.log(

            '[Realtime]',

            ...message

        );

    };

    GPSTracker.realtimeWarn = function (...message) {

        console.warn(

            '[Realtime]',

            ...message

        );

    };

    GPSTracker.realtimeError = function (...message) {

        console.error(

            '[Realtime]',

            ...message

        );

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

        if (typeof Pusher === 'undefined') {

            this.realtimeError(

                'Pusher library not found.'

            );

            return false;

        }

        if (!this.realtimeConfig.key) {

            this.realtimeError(

                'Pusher key is empty.'

            );

            return false;

        }

        if (!this.realtimeConfig.cluster) {

            this.realtimeError(

                'Pusher cluster is empty.'

            );

            return false;

        }

        return true;

    };

    /*
    |--------------------------------------------------------------------------
    | Create Connection
    |--------------------------------------------------------------------------
    */

    GPSTracker.createRealtimeConnection = function () {

        if (!this.canStartRealtime()) {

            return null;

        }

        if (this.realtime.pusher) {

            return this.realtime.pusher;

        }

        this.setRealtimeConnecting(true);

        this.realtimeLog(

            'Create realtime connection.'

        );

        this.realtime.pusher = new Pusher(

            this.realtimeConfig.key,

            {

                cluster: this.realtimeConfig.cluster,

                forceTLS: this.realtimeConfig.forceTLS,

                enabledTransports: [

                    'ws',

                    'wss',

                ],

                activityTimeout:

                    this.realtimeConfig.activityTimeout,

                pongTimeout:

                    this.realtimeConfig.pongTimeout,

            }

        );

        return this.realtime.pusher;

    };

    /*
    |--------------------------------------------------------------------------
    | Destroy Connection
    |--------------------------------------------------------------------------
    */

    GPSTracker.destroyRealtimeConnection = function () {

        if (!this.realtime.pusher) {

            return;

        }

        this.realtime.pusher.disconnect();

        this.realtime.pusher = null;

        this.realtime.channel = null;

        this.setRealtimeConnected(false);

        this.setRealtimeConnecting(false);

        this.setRealtimeSubscribed(false);

        this.setRealtimeInitialized(false);

    };
        /*
    |--------------------------------------------------------------------------
    | Connect Realtime
    |--------------------------------------------------------------------------
    */

    GPSTracker.connectRealtime = function () {

        if (this.isRealtimeConnected()) {

            return;

        }

        const pusher = this.createRealtimeConnection();

        if (!pusher) {

            return;

        }

        this.bindRealtimeConnection();

    };

    /*
    |--------------------------------------------------------------------------
    | Disconnect Realtime
    |--------------------------------------------------------------------------
    */

    GPSTracker.disconnectRealtime = function () {

        this.realtimeLog(

            'Disconnect realtime.'

        );

        this.destroyRealtimeConnection();

    };

    /*
    |--------------------------------------------------------------------------
    | Reconnect Realtime
    |--------------------------------------------------------------------------
    */

    GPSTracker.reconnectRealtime = function () {

        if (this.realtime.reconnecting) {

            return;

        }

        this.realtime.reconnecting = true;

        clearTimeout(

            this.realtime.reconnectTimer

        );

        this.realtime.reconnectTimer = setTimeout(() => {

            this.realtimeLog(

                'Reconnect realtime.'

            );

            this.disconnectRealtime();

            this.connectRealtime();

            this.realtime.reconnecting = false;

        },

        this.realtimeConfig.reconnectDelay);

    };

    /*
    |--------------------------------------------------------------------------
    | Bind Connection Event
    |--------------------------------------------------------------------------
    */

    GPSTracker.bindRealtimeConnection = function () {

        if (!this.realtime.pusher) {

            return;

        }

        const connection =

            this.realtime.pusher.connection;

        /*
        |--------------------------------------------------------------------------
        | Connected
        |--------------------------------------------------------------------------
        */

        connection.bind(

            'connected',

            () => {

                this.realtimeLog(

                    'Connected.'

                );

                this.setRealtimeConnecting(false);

                this.setRealtimeConnected(true);

                this.subscribeRealtimeChannel();

            }

        );

        /*
        |--------------------------------------------------------------------------
        | Connecting
        |--------------------------------------------------------------------------
        */

        connection.bind(

            'connecting',

            () => {

                this.realtimeLog(

                    'Connecting...'

                );

                this.setRealtimeConnecting(true);

            }

        );

        /*
        |--------------------------------------------------------------------------
        | Disconnected
        |--------------------------------------------------------------------------
        */

        connection.bind(

            'disconnected',

            () => {

                this.realtimeWarn(

                    'Disconnected.'

                );

                this.setRealtimeConnected(false);

                this.setRealtimeSubscribed(false);

                this.setRealtimeInitialized(false);

                this.reconnectRealtime();

            }

        );

        /*
        |--------------------------------------------------------------------------
        | Error
        |--------------------------------------------------------------------------
        */

        connection.bind(

            'error',

            error => {

                this.realtimeError(

                    error

                );

            }

        );

        /*
        |--------------------------------------------------------------------------
        | State Change
        |--------------------------------------------------------------------------
        */

        connection.bind(

            'state_change',

            states => {

                this.realtimeLog(

                    'State:',

                    states.previous,

                    '→',

                    states.current

                );

            }

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Subscribe Channel
    |--------------------------------------------------------------------------
    */

    GPSTracker.subscribeRealtimeChannel = function () {

        if (!this.realtime.pusher) {

            return;

        }

        if (this.realtime.channel) {

            return;

        }

        this.realtimeLog(

            'Subscribe channel:',

            this.realtimeConfig.channel

        );

        this.realtime.channel =

            this.realtime.pusher.subscribe(

                this.realtimeConfig.channel

            );

    };
        /*
    |--------------------------------------------------------------------------
    | Channel Event
    |--------------------------------------------------------------------------
    */

    GPSTracker.bindRealtimeChannel = function () {

        if (!this.realtime.channel) {

            return;

        }

        /*
        |--------------------------------------------------------------------------
        | Subscription Success
        |--------------------------------------------------------------------------
        */

        this.realtime.channel.bind(

            'pusher:subscription_succeeded',

            () => {

                this.realtimeLog(

                    'Subscription succeeded.'

                );

                this.setRealtimeSubscribed(true);

                this.setRealtimeInitialized(true);

                this.bindRealtimePayload();

            }

        );

        /*
        |--------------------------------------------------------------------------
        | Subscription Error
        |--------------------------------------------------------------------------
        */

        this.realtime.channel.bind(

            'pusher:subscription_error',

            error => {

                this.realtimeError(

                    'Subscription failed.',

                    error

                );

                this.setRealtimeSubscribed(false);

            }

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Listen Payload
    |--------------------------------------------------------------------------
    */

    GPSTracker.bindRealtimePayload = function () {

        if (!this.realtime.channel) {

            return;

        }

        this.realtime.channel.bind(

            this.realtimeConfig.event,

            payload => {

                this.receiveRealtimePayload(

                    payload

                );

            }

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Receive Payload
    |--------------------------------------------------------------------------
    */

    GPSTracker.receiveRealtimePayload = function (payload) {

        if (!payload) {

            return;

        }

        this.setRealtimePayload(

            payload

        );

        this.setRealtimeUpdateTime();

        const vehicle =

            payload.vehicle ??

            payload.data ??

            payload;

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

        if (

            !vehicle ||

            !vehicle.device_id

        ) {

            return;

        }

        this.syncRealtimeVehicle(

            vehicle

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Sync Vehicle
    |--------------------------------------------------------------------------
    */

    GPSTracker.syncRealtimeVehicle = function (vehicle) {

        this.replaceVehicle(

            vehicle

        );

        this.refreshRealtimeModules(

            vehicle

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

        if (

            this.followVehicle !== true

        ) {

            return;

        }

        this.map.panTo(

            [

                Number(vehicle.latitude),

                Number(vehicle.longitude)

            ],

            {

                animate: true

            }

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Refresh All Realtime Components
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

        this.disconnectRealtime();

        this.resetRealtime();

    };

    /*
    |--------------------------------------------------------------------------
    | Window Event
    |--------------------------------------------------------------------------
    */

    GPSTracker.bindRealtimeWindowEvents = function () {

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