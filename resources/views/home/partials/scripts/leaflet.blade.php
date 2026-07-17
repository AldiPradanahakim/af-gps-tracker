<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | Leaflet Map
    |--------------------------------------------------------------------------
    */

    GPSTracker.map = L.map('map', {

        zoomControl: false,

        attributionControl: false,

        preferCanvas: true,

    }).setView([-6.917464,107.619123],13);

    /*
    |--------------------------------------------------------------------------
    | Zoom Control
    |--------------------------------------------------------------------------
    */

    L.control.zoom({

        position:'bottomright',

    }).addTo(GPSTracker.map);

    /*
    |--------------------------------------------------------------------------
    | Scale
    |--------------------------------------------------------------------------
    */

    L.control.scale({

        imperial:false,

    }).addTo(GPSTracker.map);

    /*
    |--------------------------------------------------------------------------
    | OpenStreetMap
    |--------------------------------------------------------------------------
    */

    L.tileLayer(

        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',

        {

            maxZoom:22,

            minZoom:4,

            detectRetina:true,

        }

    ).addTo(GPSTracker.map);

    /*
    |--------------------------------------------------------------------------
    | Layer Group
    |--------------------------------------------------------------------------
    */

    GPSTracker.markerLayer = L.layerGroup().addTo(GPSTracker.map);

    GPSTracker.radiusLayer = L.layerGroup().addTo(GPSTracker.map);

    GPSTracker.administrativeLayer = L.layerGroup().addTo(GPSTracker.map);

    GPSTracker.routeLayer = L.layerGroup().addTo(GPSTracker.map);

    GPSTracker.temporaryLayer = L.layerGroup().addTo(GPSTracker.map);

    /*
    |--------------------------------------------------------------------------
    | Map Event
    |--------------------------------------------------------------------------
    */

    GPSTracker.map.on('click', function () {

        document
            .getElementById('geofenceDropdown')
            ?.classList.add('hidden');

        document
            .getElementById('notificationDropdown')
            ?.classList.add('hidden');

        document
            .getElementById('profileDropdown')
            ?.classList.add('hidden');

    });

    /*
    |--------------------------------------------------------------------------
    | Resize Fix
    |--------------------------------------------------------------------------
    */

    window.addEventListener('resize', function () {

        GPSTracker.map.invalidateSize();

    });

    /*
    |--------------------------------------------------------------------------
    | Fly To Location
    |--------------------------------------------------------------------------
    */

    GPSTracker.flyToLocation = function (

        latitude,

        longitude,

        zoom = 17

    ){

        GPSTracker.map.flyTo(

            [

                latitude,

                longitude

            ],

            zoom,

            {

                animate:true,

                duration:1.2,

            }

        );

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

    ){

        GPSTracker.clearTemporary();

        const marker = L.marker(

            [

                latitude,

                longitude

            ]

        );

        if(title){

            marker.bindPopup(title);

        }

        marker.addTo(

            GPSTracker.temporaryLayer

        );

        GPSTracker.flyToLocation(

            latitude,

            longitude

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Preview Radius
    |--------------------------------------------------------------------------
    */

    GPSTracker.previewRadius = function (

        latitude,

        longitude,

        radius

    ){

        GPSTracker.clearTemporary();

        L.circle(

            [

                latitude,

                longitude

            ],

            {

                radius:radius,

                color:'#2563EB',

                weight:2,

                fillColor:'#2563EB',

                fillOpacity:.18,

            }

        ).addTo(

            GPSTracker.temporaryLayer

        );

        L.marker(

            [

                latitude,

                longitude

            ]

        ).addTo(

            GPSTracker.temporaryLayer

        );

        GPSTracker.flyToLocation(

            latitude,

            longitude,

            16

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Preview Administrative
    |--------------------------------------------------------------------------
    */

    GPSTracker.previewAdministrative = function (

        geojson

    ){

        GPSTracker.clearTemporary();

        const polygon = L.geoJSON(

            geojson,

            {

                style:{

                    color:'#9333EA',

                    weight:2,

                    fillColor:'#9333EA',

                    fillOpacity:.18,

                }

            }

        );

        polygon.addTo(

            GPSTracker.temporaryLayer

        );

        GPSTracker.map.fitBounds(

            polygon.getBounds(),

            {

                padding:[50,50]

            }

        );

    };

});

</script>