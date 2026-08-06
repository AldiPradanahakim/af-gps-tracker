<script>

window.VehicleMap = {

    /*
    |--------------------------------------------------------------------------
    | Properties
    |--------------------------------------------------------------------------
    */

    data: {},

    map: null,

    layerControl: null,

    baseLayers: {},

    overlayLayers: {},

    layers: {},

    layerControlVisible: true,

    defaultZoom: 16,

    defaultLatitude: -6.917464,

    defaultLongitude: 107.619123,

    /*
    |--------------------------------------------------------------------------
    | Initialize
    |--------------------------------------------------------------------------
    */

    init(data = {}) {

        this.data = data;

        this.initMap();

        this.initTileLayers();

        this.bindControls();

        this.setInitialView();

        /*
        |--------------------------------------------------------------------------
        | Dispatch async: modul lain (Marker, Path) mendaftarkan listener
        | 'vehicle.map.ready' saat init-nya masing-masing dipanggil SETELAH
        | VehicleMap.init() ini. Dispatch harus ditunda ke tick berikutnya
        | supaya listener-listener itu sempat terpasang lebih dulu.
        |--------------------------------------------------------------------------
        */

        setTimeout(() => {

            document.dispatchEvent(

                new CustomEvent(

                    'vehicle.map.ready',

                    {

                        detail: this.map

                    }

                )

            );

        }, 0);

    },

    /*
    |--------------------------------------------------------------------------
    | Create Map
    |--------------------------------------------------------------------------
    */

    initMap() {

        this.map = L.map('vehicleMap', {

            zoomControl: false,

        });

    },

    /*
    |--------------------------------------------------------------------------
    | Controls
    |--------------------------------------------------------------------------
    */

    bindControls() {

        const zoomInButton = document.getElementById('vehicleZoomInButton');
        const zoomOutButton = document.getElementById('vehicleZoomOutButton');
        const layerButton = document.getElementById('vehicleLayerButton');

        if (zoomInButton) {
            zoomInButton.addEventListener('click', () => this.zoomIn());
        }

        if (zoomOutButton) {
            zoomOutButton.addEventListener('click', () => this.zoomOut());
        }

        if (layerButton) {
            layerButton.addEventListener('click', () => this.toggleBaseLayer());
        }

    },

    zoomIn() {

        if (!this.map) {
            return;
        }

        this.map.zoomIn();

    },

    zoomOut() {

        if (!this.map) {
            return;
        }

        this.map.zoomOut();

    },

    toggleBaseLayer() {

        if (!this.map) {
            return;
        }

        const nextLayer =
            this.currentBaseLayer === 'OpenStreetMap'
                ? 'Satellite'
                : 'OpenStreetMap';

        if (nextLayer === 'Satellite') {
            if (this.map.hasLayer(this.baseLayers.OpenStreetMap)) {
                this.map.removeLayer(this.baseLayers.OpenStreetMap);
            }
            if (!this.map.hasLayer(this.baseLayers.Satellite)) {
                this.baseLayers.Satellite.addTo(this.map);
            }
        } else {
            if (this.map.hasLayer(this.baseLayers.Satellite)) {
                this.map.removeLayer(this.baseLayers.Satellite);
            }
            if (!this.map.hasLayer(this.baseLayers.OpenStreetMap)) {
                this.baseLayers.OpenStreetMap.addTo(this.map);
            }
        }

        this.currentBaseLayer = nextLayer;

    },

    /*
    |--------------------------------------------------------------------------
    | Tile Layer
    |--------------------------------------------------------------------------
    */

    initTileLayers() {

        const osm = L.tileLayer(

            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',

            {

                maxZoom: 19,

                attribution: '&copy; OpenStreetMap'

            }

        );

        const satellite = L.tileLayer(

            'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',

            {

                attribution: 'Esri'

            }

        );

        osm.addTo(this.map);

        this.baseLayers = {

            "OpenStreetMap": osm,

            "Satellite": satellite,

        };

    },

    /*
    |--------------------------------------------------------------------------
    | Layer Control
    |--------------------------------------------------------------------------
    */


    refreshLayerControl() {

        return;

    },

    /*
    |--------------------------------------------------------------------------
    | Add Overlay
    |--------------------------------------------------------------------------
    */

    addOverlay(name, layer) {

        if (

            !layer ||

            !this.map

        ) {

            return;

        }

        this.removeOverlay(

            name

        );

        this.layers[name] = layer;

        this.overlayLayers[name] = layer;

        if (

            !this.map.hasLayer(layer)

        ) {

            layer.addTo(

                this.map

            );

        }

        this.refreshLayerControl();

    },

    /*
    |--------------------------------------------------------------------------
    | Remove Overlay
    |--------------------------------------------------------------------------
    */

    removeOverlay(name) {

        const layer = this.layers[name];

        if (!layer) {

            return;

        }

        if (

            this.map.hasLayer(layer)

        ) {

            this.map.removeLayer(

                layer

            );

        }

        delete this.layers[name];

        delete this.overlayLayers[name];

        this.refreshLayerControl();

    },

    /*
    |--------------------------------------------------------------------------
    | Get Overlay
    |--------------------------------------------------------------------------
    */

    getOverlay(name) {

        return this.layers[name] ?? null;

    },

    /*
    |--------------------------------------------------------------------------
    | Has Overlay
    |--------------------------------------------------------------------------
    */

    hasOverlay(name) {

        return this.layers[name] !== undefined;

    },

    /*
    |--------------------------------------------------------------------------
    | Clear Overlay
    |--------------------------------------------------------------------------
    */

    clearOverlay() {

        Object.keys(

            this.layers

        ).forEach(

            name => {

                this.removeOverlay(

                    name

                );

            }

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Initial View
    |--------------------------------------------------------------------------
    */

    setInitialView() {

        const location = this.data.latestLocation;

        if (

            location &&

            location.lat !== null &&

            location.lng !== null

        ) {

            this.map.setView(

                [

                    location.lat,

                    location.lng,

                ],

                this.defaultZoom

            );

            return;

        }

        this.map.setView(

            [

                this.defaultLatitude,

                this.defaultLongitude,

            ],

            this.defaultZoom

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Fly To
    |--------------------------------------------------------------------------
    */

    flyTo(lat, lng, zoom = 17) {

        this.map.flyTo(

            [

                lat,

                lng,

            ],

            zoom

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Fit Bounds
    |--------------------------------------------------------------------------
    */

    fitBounds(bounds) {

        this.map.fitBounds(

            bounds,

            {

                padding: [

                    50,

                    50,

                ],

            }

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Reset View
    |--------------------------------------------------------------------------
    */

    resetView() {

        const location = this.data.latestLocation;

        if (

            !location ||

            location.lat === null ||

            location.lng === null

        ) {

            return;

        }

        this.flyTo(

            location.lat,

            location.lng,

            this.defaultZoom

        );

    }

};

</script>