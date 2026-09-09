<script>

window.VehiclePath = {

    /*
    |--------------------------------------------------------------------------
    | Properties
    |--------------------------------------------------------------------------
    */

    state: null,

    polyline: null,

    coordinates: [],

    /*
    |--------------------------------------------------------------------------
    | Playback Active
    |--------------------------------------------------------------------------
    |
    | Selama VehiclePlayback aktif (load() sampai stop()), polyline sudah
    | diisi penuh lewat setPath() - addPoint() (dipicu event
    | 'vehicle.location.updated' dari live tracking maupun dari tick
    | playback sendiri) harus diabaikan supaya tidak menambah titik di
    | atas rute yang sudah benar (lihat VehiclePlayback.load()/stop()).
    |--------------------------------------------------------------------------
    */

    playbackActive: false,

    /*
    |--------------------------------------------------------------------------
    | Initialize
    |--------------------------------------------------------------------------
    */

    init(state) {

        this.state = state;

        document.addEventListener(

            'vehicle.map.ready',

            () => {

                this.create();

            }

        );

        this.bindEvents();

    },

    /*
    |--------------------------------------------------------------------------
    | Create Polyline
    |--------------------------------------------------------------------------
    */

    create() {

        this.polyline = L.polyline(

            [],

            {

                color: '#2563eb',

                weight: 5,

                opacity: 0.9,

                lineJoin: 'round',

                lineCap: 'round',

            }

        );

        VehicleMap.addOverlay(

            'vehicle-path',

            this.polyline

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Bind Events
    |--------------------------------------------------------------------------
    */

    bindEvents() {

        document.addEventListener(

            'vehicle.location.updated',

            (event) => {

                this.addPoint(

                    event.detail

                );

            }

        );

    },

        /*
    |--------------------------------------------------------------------------
    | Add Point
    |--------------------------------------------------------------------------
    */

    addPoint(location) {

        if (

            this.playbackActive ||

            !this.polyline ||

            !location ||

            location.lat == null ||

            location.lng == null

        ) {

            return;

        }

        const point = [

            Number(location.lat),

            Number(location.lng),

        ];

        this.coordinates.push(

            point

        );

        this.polyline.setLatLngs(

            this.coordinates

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Set Path
    |--------------------------------------------------------------------------
    */

    setPath(points = []) {

        if (

            !Array.isArray(points)

        ) {

            points = [];

        }

        this.coordinates = points.map(

            point => [

                Number(point.lat),

                Number(point.lng),

            ]

        );

        if (

            this.polyline

        ) {

            this.polyline.setLatLngs(

                this.coordinates

            );

        }

    },

        /*
    |--------------------------------------------------------------------------
    | Clear
    |--------------------------------------------------------------------------
    */

    clear() {

        this.setPath([]);

    },

    /*
    |--------------------------------------------------------------------------
    | Get Coordinates
    |--------------------------------------------------------------------------
    */

    getCoordinates() {

        return this.coordinates;

    },

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    destroy() {

        if (!this.polyline) {

            return;

        }

        VehicleMap.removeOverlay(

            'vehicle-path'

        );
        this.polyline.remove();

        this.polyline = null;

        this.coordinates = [];

    }

};

</script>