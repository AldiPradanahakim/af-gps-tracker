<script>

document.addEventListener('DOMContentLoaded', () => {

    if (typeof GPSTracker === 'undefined') {

        console.error('GPSTracker belum diinisialisasi.');

        return;

    }

    /*
    |--------------------------------------------------------------------------
    | Default Configuration
    |--------------------------------------------------------------------------
    */

    const DEFAULT_CENTER = [
        -6.917464,
        107.619123,
    ];

    const defaultLayer = L.tileLayer(

    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',

        {

            maxZoom:22,

            minZoom:4,

            detectRetina:true,

        }

    );

    const satelliteLayer = L.tileLayer(

        'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',

        {

            maxZoom:22,

        }

    );

    const darkLayer = L.tileLayer(

        'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png',

        {

            subdomains:'abcd',

            maxZoom:22,

        }

    );

    /*
    |--------------------------------------------------------------------------
    | Initialize Leaflet
    |--------------------------------------------------------------------------
    */

    GPSTracker.map = L.map('map', {

        zoomControl: false,

        attributionControl: false,

        preferCanvas: true,

    }).setView(

        DEFAULT_CENTER,

        GPSTracker.config.defaultZoom

    );

    /*
    |--------------------------------------------------------------------------
    | Base Tile Layer
    |--------------------------------------------------------------------------
    */

    defaultLayer.addTo(GPSTracker.map);
    GPSTracker.baseLayers = {

        "Default": defaultLayer,

        "Satellite": satelliteLayer,

        "Dark": darkLayer,

    };

    /*
    |--------------------------------------------------------------------------
    | Controls
    |--------------------------------------------------------------------------
    */

    L.control.zoom({

        position: 'bottomright',

    }).addTo(GPSTracker.map);

    L.control.scale({

            imperial: false,

        }).addTo(GPSTracker.map);

    setTimeout(() => {

        const container = document.querySelector(
            '.leaflet-bottom.leaflet-right'
        );

        const zoom = document.querySelector(
            '.leaflet-control-zoom'
        );

        if (!container || !zoom) {
            return;
        }

      

    }, 100);
    /*
    |--------------------------------------------------------------------------
    | Layer Groups
    |--------------------------------------------------------------------------
    */

    GPSTracker.markerLayer =
        L.layerGroup().addTo(GPSTracker.map);

    GPSTracker.radiusLayer =
        L.layerGroup().addTo(GPSTracker.map);

    GPSTracker.administrativeLayer =
        L.layerGroup().addTo(GPSTracker.map);

    GPSTracker.routeLayer =
        L.layerGroup().addTo(GPSTracker.map);

    GPSTracker.playbackLayer =
        L.layerGroup().addTo(GPSTracker.map);

    GPSTracker.temporaryLayer =
        L.layerGroup().addTo(GPSTracker.map);

    /*
    |--------------------------------------------------------------------------
    | Coordinate Validation
    |--------------------------------------------------------------------------
    */

    GPSTracker.isValidCoordinate = function (

        latitude,

        longitude

    ) {

        return (

            Number.isFinite(Number(latitude)) &&

            Number.isFinite(Number(longitude))

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Fly To Location
    |--------------------------------------------------------------------------
    */

    GPSTracker.flyToLocation = function (

        latitude,

        longitude,

        zoom = GPSTracker.config.defaultZoom

    ) {

        if (

            !this.map ||

            !this.isValidCoordinate(latitude, longitude)

        ) {

            return;

        }

        this.map.flyTo(

            [

                Number(latitude),

                Number(longitude),

            ],

            zoom,

            {

                animate: true,

                duration: GPSTracker.config.animationDuration,

            }

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Fit Bounds
    |--------------------------------------------------------------------------
    */

    GPSTracker.fitBounds = function (bounds) {

        if (

            !this.map ||

            !bounds ||

            !bounds.isValid()

        ) {

            return;

        }

        this.map.fitBounds(

            bounds,

            {

                padding: GPSTracker.config.fitPadding,

                animate: true,

            }

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Clear Temporary Layer
    |--------------------------------------------------------------------------
    */

    GPSTracker.clearTemporary = function () {

        if (!this.temporaryLayer) {

            return;

        }

        this.temporaryLayer.clearLayers();

        this.temporaryMarker = null;

    };

    /*
    |--------------------------------------------------------------------------
    | Temporary Marker
    |--------------------------------------------------------------------------
    */

    GPSTracker.showTemporaryMarker = function (

        latitude,

        longitude,

        title = ''

    ) {

        if (

            !this.isValidCoordinate(latitude, longitude)

        ) {

            return;

        }

        this.clearTemporary();

        const marker = L.marker([

            Number(latitude),

            Number(longitude),

        ]);

        if (title) {

            marker.bindPopup(title);

        }

        marker.addTo(this.temporaryLayer);

        this.temporaryMarker = marker;

        this.flyToLocation(

            latitude,

            longitude

        );

    };
        /*
    |--------------------------------------------------------------------------
    | Refresh Map Size
    |--------------------------------------------------------------------------
    */

    GPSTracker.refreshMap = function () {

        if (!this.map) {

            return;

        }

        this.map.invalidateSize();

    };

    /*
    |--------------------------------------------------------------------------
    | Reset View
    |--------------------------------------------------------------------------
    */

    GPSTracker.resetView = function () {

        if (!this.map) {

            return;

        }

        this.map.flyTo(

            DEFAULT_CENTER,

            GPSTracker.config.defaultZoom,

            {

                animate: true,

                duration: GPSTracker.config.animationDuration,

            }

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Close All Dropdowns
    |--------------------------------------------------------------------------
    */

    GPSTracker.closeDropdowns = function () {

        [

            'geofenceDropdown',

            'notificationDropdown',

            'profileDropdown',

        ].forEach(id => {

            document
                .getElementById(id)
                ?.classList.add('hidden');

        });

    };

    /*
    |--------------------------------------------------------------------------
    | Leaflet Events
    |--------------------------------------------------------------------------
    */

    GPSTracker.map.on('click', () => {

        GPSTracker.closeDropdowns();

    });

    GPSTracker.map.on('movestart', () => {

        document.dispatchEvent(

            new CustomEvent(

                'gpstracker:map-movestart'

            )

        );

    });

    GPSTracker.map.on('moveend', () => {

        document.dispatchEvent(

            new CustomEvent(

                'gpstracker:map-moveend',

                {

                    detail: {

                        center: GPSTracker.map.getCenter(),

                        zoom: GPSTracker.map.getZoom(),

                    }

                }

            )

        );

    });

    GPSTracker.map.on('zoomend', () => {

        document.dispatchEvent(

            new CustomEvent(

                'gpstracker:map-zoom',

                {

                    detail: {

                        zoom: GPSTracker.map.getZoom(),

                    }

                }

            )

        );

    });

    /*
    |--------------------------------------------------------------------------
    | Window Resize
    |--------------------------------------------------------------------------
    */

    let resizeTimeout = null;

    window.addEventListener('resize', () => {

        clearTimeout(resizeTimeout);

        resizeTimeout = setTimeout(() => {

            GPSTracker.refreshMap();

        }, 150);

    });

    /*
    |--------------------------------------------------------------------------
    | Map Ready Event
    |--------------------------------------------------------------------------
    */

    document.dispatchEvent(

        new CustomEvent(

            'gpstracker:map-ready',

            {

                detail: {

                    map: GPSTracker.map,

                }

            }

        )

    );

    /*
    |--------------------------------------------------------------------------
    | Debug
    |--------------------------------------------------------------------------
    */

    console.info(

        '[GPSTracker] Map initialized successfully.'

    );

    /*
|--------------------------------------------------------------------------
| Custom Layer Selector
|--------------------------------------------------------------------------
*/

const layerButton = document.getElementById('layerButton');

const layerDropdown = document.getElementById('layerDropdown');

layerButton?.addEventListener('click', function (event) {

    event.stopPropagation();

    layerDropdown.classList.toggle('hidden');

    });

    document.addEventListener('click', function () {

        layerDropdown?.classList.add('hidden');

    });

    document.querySelectorAll('.layer-option').forEach(button => {

        button.addEventListener('click', function () {

            Object.values(GPSTracker.baseLayers).forEach(layer => {

                GPSTracker.map.removeLayer(layer);

            });

            switch (this.dataset.layer) {

                case 'satellite':

                    GPSTracker.baseLayers.Satellite.addTo(GPSTracker.map);

                    break;

                case 'dark':

                    GPSTracker.baseLayers.Dark.addTo(GPSTracker.map);

                    break;

                default:

                    GPSTracker.baseLayers.Default.addTo(GPSTracker.map);

                    break;

            }

            layerDropdown.classList.add('hidden');

        });

    });

});

</script>