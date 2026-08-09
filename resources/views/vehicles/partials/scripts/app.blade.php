<script>

window.Vehicle = {

    /*
    |--------------------------------------------------------------------------
    | State
    |--------------------------------------------------------------------------
    */

    state: {

        device: null,

        vehicles: [],

        latestLocation: null,

        homeLocation: null,

        geofences: [],

        todayTravel: [],

        travelHistories: [],
        

    },

    /*
    |--------------------------------------------------------------------------
    | Component
    |--------------------------------------------------------------------------
    */

    map: null,

    marker: null,

    path: null,

    information: null,

    history: null,

    playback: null,

    homeLocation: null,

    geofence: null,

    profile: null,

    search: null,

    navigation: null,

    realtime: null,

    stop: null,

    /*
    |--------------------------------------------------------------------------
    | Initialize
    |--------------------------------------------------------------------------
    */

    init() {

        this.loadState();

        if (window.VehicleApi) {

            VehicleApi.init(

                this.state

            );

        }

        this.initializeModules();

        console.info(

            '[Vehicle] initialized.'

        );

        if (window.VehiclePath) {

            this.path = window.VehiclePath;

            this.path.init(

                this.state

            );

        }

        if (window.VehicleHistory) {

            this.history = window.VehicleHistory;

            this.history.init(

                this.state

            );

        }

        if (window.VehicleStop) {

            this.stop = window.VehicleStop;

            this.stop.init(

                this.state

            );

        }

        this.activateInitialSection();

    },

    /*
    |--------------------------------------------------------------------------
    | Activate Initial Section
    |--------------------------------------------------------------------------
    | Beberapa tab (Riwayat Perjalanan, Kendaraan Berhenti) memuat datanya
    | secara lazy (baru fetch saat tab dibuka). Jika halaman dimuat dengan
    | hash URL yang mengarah ke tab tersebut (mis. setelah reload dari
    | halaman Stop Detection), tab itu langsung terlihat sejak awal
    | sehingga datanya perlu langsung diaktifkan juga.
    |--------------------------------------------------------------------------
    */

    activateInitialSection() {

        const hash = window.location.hash.replace('#', '');

        if (hash === 'history' && this.history?.activate) {

            this.history.activate();

        }

        if (hash === 'stop' && this.stop?.activate) {

            this.stop.activate();

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Load Initial State
    |--------------------------------------------------------------------------
    */

    loadState() {

        this.state = {

            ...window.VehicleData

        };

    },

    /*
    |--------------------------------------------------------------------------
    | Register Module
    |--------------------------------------------------------------------------
    */

    initializeModules() {

        if (window.VehicleMap) {

            this.map = window.VehicleMap;

            this.map.init(this.state);

        }

        if (window.VehicleMarker) {

            this.marker = window.VehicleMarker;

            this.marker.init(this.state);

        }

        if (window.VehicleInformation) {

            this.information = window.VehicleInformation;

            this.information.init(this.state);

        }

        if (window.VehicleGeofence) {

            this.geofence = window.VehicleGeofence;

            this.geofence.init(this.state);

        }

        if (window.VehiclePlayback) {

            this.playback = window.VehiclePlayback;

            this.playback.init(this.state);

        }

        if (window.VehicleNavigation) {

            this.navigation = window.VehicleNavigation;

            this.navigation.init(this.state);

        }

        if (window.VehicleRealtime) {

            this.realtime = window.VehicleRealtime;

            this.realtime.init(this.state);

        }

        if (window.VehicleHomeLocation) {

            this.homeLocation = window.VehicleHomeLocation;

            this.homeLocation.init(this.state);

        }

        if (window.VehicleProfile) {

            this.profile = window.VehicleProfile;

            this.profile.init(this.state);

        }

        if (window.VehicleSearch) {

            this.search = window.VehicleSearch;

            this.search.init(this.state);

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Update Latest Location
    |--------------------------------------------------------------------------
    */

    updateLatestLocation(location) {

        this.state.latestLocation = location;

        document.dispatchEvent(

            new CustomEvent(

                'vehicle.location.updated',

                {

                    detail: location

                }

            )

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Update Home Location
    |--------------------------------------------------------------------------
    */

    updateHomeLocation(location) {

        this.state.homeLocation = location;

    },

    /*
    |--------------------------------------------------------------------------
    | Update Geofence
    |--------------------------------------------------------------------------
    */

    updateGeofences(geofences) {

        this.state.geofences = geofences;

    },

    /*
    |--------------------------------------------------------------------------
    | Update Playback
    |--------------------------------------------------------------------------
    */

    updatePlayback(histories) {

        this.state.travelHistories = histories;

    },

    /*
    |--------------------------------------------------------------------------
    | Getter
    |--------------------------------------------------------------------------
    */

    getState() {

        return this.state;

    },

};

document.addEventListener(

    'DOMContentLoaded',

    () => Vehicle.init()

);

</script>