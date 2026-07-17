<script>

document.addEventListener('DOMContentLoaded', () => {

    if (typeof GPSTracker === 'undefined') {
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | State
    |--------------------------------------------------------------------------
    */

    GPSTracker.geofenceLayers = {

        radius: [],

        administrative: [],

        preview: null,

    };

    /*
    |--------------------------------------------------------------------------
    | Element
    |--------------------------------------------------------------------------
    */

    const toggleRadius = document.getElementById('toggleRadius');

    const toggleAdministrative = document.getElementById('toggleAdministrative');

    /*
    |--------------------------------------------------------------------------
    | Load Geofence
    |--------------------------------------------------------------------------
    */

    async function loadGeofence() {

        try {

            const response = await fetch('/api/geofences', {

                headers: {

                    Accept: 'application/json',

                    'X-Requested-With': 'XMLHttpRequest',

                },

                credentials: 'same-origin',

            });

            if (!response.ok) {

                throw new Error();

            }

            const geofences = await response.json();

            clearLayers();

            geofences.forEach(renderGeofence);

        } catch (error) {

            console.error(error);

        }

    }

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    function renderGeofence(geofence) {

        if (geofence.type === 'radius') {

            renderRadius(geofence);

            return;

        }

        if (geofence.type === 'administrative') {

            renderAdministrative(geofence);

        }

    }

    /*
    |--------------------------------------------------------------------------
    | Radius
    |--------------------------------------------------------------------------
    */

    function renderRadius(geofence) {

        if (!geofence.config.center) {

            return;

        }

        const circle = L.circle(

            [

                geofence.config.center.latitude,

                geofence.config.center.longitude,

            ],

            {

                radius: geofence.config.radius,

                color: '#2563EB',

                weight: 2,

                fillOpacity: .12,

            }

        ).bindPopup(

            `<strong>${geofence.name}</strong>`

        );

        GPSTracker.geofenceLayers.radius.push(circle);

        if (toggleRadius.checked) {

            circle.addTo(GPSTracker.map);

        }

    }

    /*
    |--------------------------------------------------------------------------
    | Administrative
    |--------------------------------------------------------------------------
    */

    function renderAdministrative(geofence) {

        if (!geofence.config.geojson) {

            return;

        }

        const polygon = L.geoJSON(

            geofence.config.geojson,

            {

                style() {

                    return {

                        color: '#16A34A',

                        weight: 2,

                        fillOpacity: .12,

                    };

                }

            }

        ).bindPopup(

            `<strong>${geofence.name}</strong>`

        );

        GPSTracker.geofenceLayers.administrative.push(

            polygon

        );

        if (toggleAdministrative.checked) {

            polygon.addTo(GPSTracker.map);

        }

    }

    /*
    |--------------------------------------------------------------------------
    | Clear
    |--------------------------------------------------------------------------
    */

    function clearLayers() {

        GPSTracker.geofenceLayers.radius.forEach(layer => {

            GPSTracker.map.removeLayer(layer);

        });

        GPSTracker.geofenceLayers.administrative.forEach(layer => {

            GPSTracker.map.removeLayer(layer);

        });

        GPSTracker.geofenceLayers.radius = [];

        GPSTracker.geofenceLayers.administrative = [];

    }
    /*
    |--------------------------------------------------------------------------
    | Toggle Radius
    |--------------------------------------------------------------------------
    */

    toggleRadius?.addEventListener('change', function () {

        GPSTracker.geofenceLayers.radius.forEach(layer => {

            if (this.checked) {

                layer.addTo(GPSTracker.map);

            } else {

                GPSTracker.map.removeLayer(layer);

            }

        });

    });

    /*
    |--------------------------------------------------------------------------
    | Toggle Administrative
    |--------------------------------------------------------------------------
    */

    toggleAdministrative?.addEventListener('change', function () {

        GPSTracker.geofenceLayers.administrative.forEach(layer => {

            if (this.checked) {

                layer.addTo(GPSTracker.map);

            } else {

                GPSTracker.map.removeLayer(layer);

            }

        });

    });

    /*
    |--------------------------------------------------------------------------
    | Preview Radius
    |--------------------------------------------------------------------------
    */

    GPSTracker.previewRadius = function (
        latitude,
        longitude,
        radius
    ) {

        removePreview();

        GPSTracker.geofenceLayers.preview = L.circle(

            [

                latitude,

                longitude,

            ],

            {

                radius: radius,

                color: '#2563EB',

                weight: 2,

                dashArray: '6',

                fillOpacity: .10,

            }

        ).addTo(GPSTracker.map);

        GPSTracker.map.fitBounds(

            GPSTracker.geofenceLayers.preview.getBounds(),

            {

                padding: [40, 40],

            }

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Preview Administrative
    |--------------------------------------------------------------------------
    */

    GPSTracker.previewAdministrative = function (geojson) {

        removePreview();

        GPSTracker.geofenceLayers.preview = L.geoJSON(

            geojson,

            {

                style() {

                    return {

                        color: '#16A34A',

                        weight: 2,

                        dashArray: '6',

                        fillOpacity: .10,

                    };

                }

            }

        ).addTo(GPSTracker.map);

        GPSTracker.map.fitBounds(

            GPSTracker.geofenceLayers.preview.getBounds(),

            {

                padding: [40, 40],

            }

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Remove Preview
    |--------------------------------------------------------------------------
    */

    function removePreview() {

        if (

            GPSTracker.geofenceLayers.preview &&

            GPSTracker.map.hasLayer(

                GPSTracker.geofenceLayers.preview

            )

        ) {

            GPSTracker.map.removeLayer(

                GPSTracker.geofenceLayers.preview

            );

        }

        GPSTracker.geofenceLayers.preview = null;

    }

    /*
    |--------------------------------------------------------------------------
    | Refresh Geofence
    |--------------------------------------------------------------------------
    */

    GPSTracker.reloadGeofence = function () {

        loadGeofence();

    };

    /*
    |--------------------------------------------------------------------------
    | Clear Preview
    |--------------------------------------------------------------------------
    */

    GPSTracker.clearPreviewGeofence = function () {

        removePreview();

    };

    /*
    |--------------------------------------------------------------------------
    | Initialize
    |--------------------------------------------------------------------------
    */

    loadGeofence();

});

</script>