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

        if (zoomInButton) {
            zoomInButton.addEventListener('click', () => this.zoomIn());
        }

        if (zoomOutButton) {
            zoomOutButton.addEventListener('click', () => this.zoomOut());
        }

        this.bindLayerSwitcher();

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

    /*
    |--------------------------------------------------------------------------
    | Layer Switcher (Default / Satellite / Dark)
    |--------------------------------------------------------------------------
    */

    bindLayerSwitcher() {

        const layerButton = document.getElementById('vehicleLayerButton');
        const layerDropdown = document.getElementById('vehicleLayerDropdown');

        if (!layerButton || !layerDropdown) {
            return;
        }

        layerButton.addEventListener('click', event => {

            event.stopPropagation();

            layerDropdown.classList.toggle('hidden');

        });

        document.addEventListener('click', () => {

            layerDropdown.classList.add('hidden');

        });

        document.querySelectorAll('.vehicle-layer-option').forEach(button => {

            button.addEventListener('click', () => {

                this.setBaseLayer(button.dataset.layer);

                layerDropdown.classList.add('hidden');

            });

        });

    },

    setBaseLayer(layer) {

        if (!this.map) {
            return;
        }

        Object.values(this.baseLayers).forEach(tileLayer => {

            if (this.map.hasLayer(tileLayer)) {
                this.map.removeLayer(tileLayer);
            }

        });

        const key = layer === 'satellite'
            ? 'Satellite'
            : (layer === 'dark' ? 'Dark' : 'Default');

        this.baseLayers[key].addTo(this.map);

        this.currentBaseLayer = key;

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

        const dark = L.tileLayer(

            'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png',

            {

                subdomains: 'abcd',

                maxZoom: 22,

                attribution: '&copy; CARTO'

            }

        );

        osm.addTo(this.map);

        this.baseLayers = {

            "Default": osm,

            "Satellite": satellite,

            "Dark": dark,

        };

        this.currentBaseLayer = 'Default';

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

    },

    /*
    |--------------------------------------------------------------------------
    | Temporary Marker (dipakai oleh hasil pencarian lokasi/tempat)
    |--------------------------------------------------------------------------
    */

    showTemporaryMarker(lat, lng, title = '') {

        if (

            !this.map ||

            lat === null ||

            lng === null

        ) {

            return;

        }

        const marker = L.marker([

            Number(lat),

            Number(lng),

        ]);

        if (title) {

            marker.bindPopup(title);

        }

        this.addOverlay('temporarySearch', marker);

        marker.openPopup();

    },

    /*
    |--------------------------------------------------------------------------
    | Clear Temporary Marker
    |--------------------------------------------------------------------------
    */

    clearTemporary() {

        this.removeOverlay('temporarySearch');

    },

};

</script>