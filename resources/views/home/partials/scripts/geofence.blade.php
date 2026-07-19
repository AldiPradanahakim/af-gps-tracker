<script>

document.addEventListener(

    'gpstracker:map-ready',

    () => {

        /*
        |--------------------------------------------------------------------------
        | State
        |--------------------------------------------------------------------------
        */

        GPSTracker.geofence ??= {

            initialized: false,

            radius: new Map(),

            administrative: new Map(),

            preview: null,

            selected: null,

        };

        /*
        |--------------------------------------------------------------------------
        | Config
        |--------------------------------------------------------------------------
        */

        GPSTracker.geofenceConfig = {

            radiusStyle: {

                color: '#2563EB',

                weight: 2,

                fillOpacity: .12,

            },

            administrativeStyle: {

                color: '#16A34A',

                weight: 2,

                fillOpacity: .12,

            },

            previewStyle: {

                weight: 2,

                dashArray: '6',

                fillOpacity: .10,

            },

        };

        /*
        |--------------------------------------------------------------------------
        | Getter
        |--------------------------------------------------------------------------
        */

        GPSTracker.getGeofenceState = function () {

            return this.geofence;

        };

        GPSTracker.getGeofenceConfig = function () {

            return this.geofenceConfig;

        };

        GPSTracker.getRadiusLayers = function () {

            return this.geofence.radius;

        };

        GPSTracker.getAdministrativeLayers = function () {

            return this.geofence.administrative;

        };

        GPSTracker.getPreviewLayer = function () {

            return this.geofence.preview;

        };

        GPSTracker.getSelectedGeofence = function () {

            return this.geofence.selected;

        };

        /*
        |--------------------------------------------------------------------------
        | Setter
        |--------------------------------------------------------------------------
        */

        GPSTracker.setPreviewLayer = function (

            layer

        ) {

            this.geofence.preview = layer;

        };

        GPSTracker.setSelectedGeofence = function (

            geofence

        ) {

            this.geofence.selected = geofence;

        };

        GPSTracker.setGeofenceInitialized = function (

            status = true

        ) {

            this.geofence.initialized = status;

        };

        /*
        |--------------------------------------------------------------------------
        | Checker
        |--------------------------------------------------------------------------
        */

        GPSTracker.hasRadiusLayer = function (

            id

        ) {

            return this.geofence.radius.has(

                String(id)

            );

        };

        GPSTracker.hasAdministrativeLayer = function (

            id

        ) {

            return this.geofence.administrative.has(

                String(id)

            );

        };

        GPSTracker.hasPreviewLayer = function () {

            return this.geofence.preview !== null;

        };

        GPSTracker.hasSelectedGeofence = function () {

            return this.geofence.selected !== null;

        };

        GPSTracker.isGeofenceInitialized = function () {

            return this.geofence.initialized;

        };

        /*
        |--------------------------------------------------------------------------
        | Logger
        |--------------------------------------------------------------------------
        */

        GPSTracker.geofenceLog = function (

            ...message

        ) {

            console.log(

                '[Geofence]',

                ...message

            );

        };

        GPSTracker.geofenceWarn = function (

            ...message

        ) {

            console.warn(

                '[Geofence]',

                ...message

            );

        };

        GPSTracker.geofenceError = function (

            ...message

        ) {

            console.error(

                '[Geofence]',

                ...message

            );

        };
                /*
        |--------------------------------------------------------------------------
        | Layer Management
        |--------------------------------------------------------------------------
        */

        GPSTracker.setRadiusLayer = function (

            id,

            layer

        ) {

            this.geofence.radius.set(

                String(id),

                layer

            );

        };

        GPSTracker.setAdministrativeLayer = function (

            id,

            layer

        ) {

            this.geofence.administrative.set(

                String(id),

                layer

            );

        };

        GPSTracker.getRadiusLayer = function (

            id

        ) {

            return this.geofence.radius.get(

                String(id)

            ) ?? null;

        };

        GPSTracker.getAdministrativeLayer = function (

            id

        ) {

            return this.geofence.administrative.get(

                String(id)

            ) ?? null;

        };

        /*
        |--------------------------------------------------------------------------
        | Remove Layer
        |--------------------------------------------------------------------------
        */

        GPSTracker.removeRadiusLayer = function (

            id

        ) {

            const layer = this.getRadiusLayer(

                id

            );

            if (!layer) {

                return;

            }

            this.radiusLayer.removeLayer(

                layer

            );

            this.geofence.radius.delete(

                String(id)

            );

        };

        GPSTracker.removeAdministrativeLayer = function (

            id

        ) {

            const layer = this.getAdministrativeLayer(

                id

            );

            if (!layer) {

                return;

            }

            this.administrativeLayer.removeLayer(

                layer

            );

            this.geofence.administrative.delete(

                String(id)

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Clear Layer
        |--------------------------------------------------------------------------
        */

        GPSTracker.clearRadiusLayers = function () {

            this.geofence.radius.forEach(

                layer => {

                    this.radiusLayer.removeLayer(

                        layer

                    );

                }

            );

            this.geofence.radius.clear();

        };

        GPSTracker.clearAdministrativeLayers = function () {

            this.geofence.administrative.forEach(

                layer => {

                    this.administrativeLayer.removeLayer(

                        layer

                    );

                }

            );

            this.geofence.administrative.clear();

        };

        GPSTracker.clearGeofenceLayers = function () {

            this.clearRadiusLayers();

            this.clearAdministrativeLayers();

        };

        /*
        |--------------------------------------------------------------------------
        | Preview Layer
        |--------------------------------------------------------------------------
        */

        GPSTracker.removePreviewLayer = function () {

            const preview = this.getPreviewLayer();

            if (!preview) {

                return;

            }

            if (

                this.map.hasLayer(

                    preview

                )

            ) {

                this.map.removeLayer(

                    preview

                );

            }

            this.setPreviewLayer(

                null

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Geofence Collection
        |--------------------------------------------------------------------------
        */

        GPSTracker.getGeofenceCount = function () {

            return Array.isArray(

                this.geofences

            )

                ? this.geofences.length

                : 0;

        };

        GPSTracker.findGeofence = function (

            id

        ) {

            return this.geofences.find(

                geofence => {

                    return String(

                        geofence.id

                    ) === String(

                        id

                    );

                }

            ) ?? null;

        };

        GPSTracker.refreshGeofenceCollection = function () {

            this.clearGeofenceLayers();

        };
                /*
        |--------------------------------------------------------------------------
        | Render Radius
        |--------------------------------------------------------------------------
        */

        GPSTracker.renderRadius = function (

            geofence

        ) {

            if (

                !geofence?.config?.center

            ) {

                return;

            }

            const circle = L.circle(

                [

                    Number(

                        geofence.config.center.latitude

                    ),

                    Number(

                        geofence.config.center.longitude

                    ),

                ],

                {

                    radius: Number(

                        geofence.config.radius ?? 0

                    ),

                    ...this.getGeofenceConfig().radiusStyle,

                }

            );

            circle.bindPopup(

                `<strong>${geofence.name}</strong>`

            );

            this.setRadiusLayer(

                geofence.id,

                circle

            );

            circle.addTo(

                this.radiusLayer

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Render Administrative
        |--------------------------------------------------------------------------
        */

        GPSTracker.renderAdministrative = function (

            geofence

        ) {

            if (

                !geofence?.config?.geojson

            ) {

                return;

            }

            const polygon = L.geoJSON(

                geofence.config.geojson,

                {

                    style: () => {

                        return this.getGeofenceConfig()

                            .administrativeStyle;

                    },

                }

            );

            polygon.bindPopup(

                `<strong>${geofence.name}</strong>`

            );

            this.setAdministrativeLayer(

                geofence.id,

                polygon

            );

            polygon.addTo(

                this.administrativeLayer

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Render Geofence
        |--------------------------------------------------------------------------
        */

        GPSTracker.renderGeofence = function (

            geofence

        ) {

            if (!geofence) {

                return;

            }

            switch (

                geofence.type

            ) {

                case 'radius':

                    this.renderRadius(

                        geofence

                    );

                    break;

                case 'administrative':

                    this.renderAdministrative(

                        geofence

                    );

                    break;

            }

        };

        /*
        |--------------------------------------------------------------------------
        | Render Collection
        |--------------------------------------------------------------------------
        */

        GPSTracker.renderGeofences = function () {

            this.clearGeofenceLayers();

            this.getGeofences().forEach(

                geofence => {

                    this.renderGeofence(

                        geofence

                    );

                }

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Refresh
        |--------------------------------------------------------------------------
        */

        GPSTracker.refreshGeofence = function (

            id

        ) {

            const geofence = this.findGeofence(

                id

            );

            if (!geofence) {

                return;

            }

            this.removeRadiusLayer(

                id

            );

            this.removeAdministrativeLayer(

                id

            );

            this.renderGeofence(

                geofence

            );

        };

        GPSTracker.refreshGeofences = function () {

            this.renderGeofences();

        };
                /*
        |--------------------------------------------------------------------------
        | Focus Geofence
        |--------------------------------------------------------------------------
        */

        GPSTracker.focusGeofence = function (

            id

        ) {

            const geofence = this.findGeofence(

                id

            );

            if (!geofence) {

                return;

            }

            this.setSelectedGeofence(

                geofence

            );

            switch (

                geofence.type

            ) {

                case 'radius':

                    this.fitBounds(

                        this.getRadiusLayer(

                            id

                        ).getBounds()

                    );

                    break;

                case 'administrative':

                    this.fitBounds(

                        this.getAdministrativeLayer(

                            id

                        ).getBounds()

                    );

                    break;

            }

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

        ) {

            this.removePreviewLayer();

            const preview = L.circle(

                [

                    Number(latitude),

                    Number(longitude),

                ],

                {

                    radius: Number(radius),

                    color: '#2563EB',

                    ...this.getGeofenceConfig()

                        .previewStyle,

                }

            );

            preview.addTo(

                this.map

            );

            this.setPreviewLayer(

                preview

            );

            this.fitBounds(

                preview.getBounds()

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Preview Administrative
        |--------------------------------------------------------------------------
        */

        GPSTracker.previewAdministrative = function (

            geojson

        ) {

            this.removePreviewLayer();

            const preview = L.geoJSON(

                geojson,

                {

                    style: () => {

                        return {

                            color: '#16A34A',

                            ...this.getGeofenceConfig()

                                .previewStyle,

                        };

                    },

                }

            );

            preview.addTo(

                this.map

            );

            this.setPreviewLayer(

                preview

            );

            this.fitBounds(

                preview.getBounds()

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Geometry Helper
        |--------------------------------------------------------------------------
        */

        GPSTracker.isPointInRadius = function (

            latitude,

            longitude,

            geofence

        ) {

            if (

                !geofence?.config?.center

            ) {

                return false;

            }

            const center = L.latLng(

                Number(

                    geofence.config.center.latitude

                ),

                Number(

                    geofence.config.center.longitude

                )

            );

            const point = L.latLng(

                Number(latitude),

                Number(longitude)

            );

            return center.distanceTo(

                point

            ) <= Number(

                geofence.config.radius ?? 0

            );

        };

        GPSTracker.isPointInPolygon = function (

            latitude,

            longitude,

            geofence

        ) {

            const layer = this.getAdministrativeLayer(

                geofence.id

            );

            if (!layer) {

                return false;

            }

            if (

                typeof turf === 'undefined'

            ) {

                return false;

            }

            const point = turf.point(

                [

                    Number(longitude),

                    Number(latitude),

                ]

            );

            const polygon = layer.toGeoJSON();

            return turf.booleanPointInPolygon(

                point,

                polygon

            );

        };
                /*
        |--------------------------------------------------------------------------
        | Geofence Detection
        |--------------------------------------------------------------------------
        */

        GPSTracker.checkGeofence = function (

            vehicle,

            geofence

        ) {

            if (

                !vehicle ||

                !geofence

            ) {

                return false;

            }

            switch (

                geofence.type

            ) {

                case 'radius':

                    return this.isPointInRadius(

                        vehicle.latitude,

                        vehicle.longitude,

                        geofence

                    );

                case 'administrative':

                    return this.isPointInPolygon(

                        vehicle.latitude,

                        vehicle.longitude,

                        geofence

                    );

            }

            return false;

        };

        GPSTracker.detectGeofences = function (

            vehicle

        ) {

            this.getGeofences().forEach(

                geofence => {

                    const inside = this.checkGeofence(

                        vehicle,

                        geofence

                    );

                    document.dispatchEvent(

                        new CustomEvent(

                            'gpstracker:geofence-check',

                            {

                                detail: {

                                    vehicle,

                                    geofence,

                                    inside,

                                }

                            }

                        )

                    );

                }

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Toggle Layer
        |--------------------------------------------------------------------------
        */

        GPSTracker.showRadius = function () {

            this.getRadiusLayers().forEach(

                layer => {

                    layer.addTo(

                        this.radiusLayer

                    );

                }

            );

        };

        GPSTracker.hideRadius = function () {

            this.getRadiusLayers().forEach(

                layer => {

                    this.radiusLayer.removeLayer(

                        layer

                    );

                }

            );

        };

        GPSTracker.showAdministrative = function () {

            this.getAdministrativeLayers().forEach(

                layer => {

                    layer.addTo(

                        this.administrativeLayer

                    );

                }

            );

        };

        GPSTracker.hideAdministrative = function () {

            this.getAdministrativeLayers().forEach(

                layer => {

                    this.administrativeLayer.removeLayer(

                        layer

                    );

                }

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Initialize
        |--------------------------------------------------------------------------
        */

        GPSTracker.initializeGeofence = function () {

            if (

                this.isGeofenceInitialized()

            ) {

                return;

            }

            this.renderGeofences();

            this.setGeofenceInitialized(

                true

            );

            this.geofenceLog(

                'Geofence initialized.'

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Reset
        |--------------------------------------------------------------------------
        */

        GPSTracker.resetGeofence = function () {

            this.clearGeofenceLayers();

            this.removePreviewLayer();

            this.setSelectedGeofence(

                null

            );

            this.setGeofenceInitialized(

                false

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Destroy
        |--------------------------------------------------------------------------
        */

        GPSTracker.destroyGeofence = function () {

            this.resetGeofence();

        };

        /*
        |--------------------------------------------------------------------------
        | Event
        |--------------------------------------------------------------------------
        */

        document.addEventListener(

            'gpstracker:vehicle-updated',

            event => {

                GPSTracker.detectGeofences(

                    event.detail.vehicle

                );

            }

        );

        document.addEventListener(

            'gpstracker:geofence-created',

            () => {

                GPSTracker.refreshGeofences();

            }

        );

        document.addEventListener(

            'gpstracker:geofence-updated',

            () => {

                GPSTracker.refreshGeofences();

            }

        );

        document.addEventListener(

            'gpstracker:geofence-deleted',

            () => {

                GPSTracker.refreshGeofences();

            }

        );

        /*
        |--------------------------------------------------------------------------
        | Topbar Dropdown
        |--------------------------------------------------------------------------
        */

        GPSTracker.toggleGeofenceDropdown = function () {

            const dropdown = document.getElementById('geofenceDropdown');

            const arrow = document.getElementById('geofenceArrow');

            if (!dropdown) {

                return;

            }

            const opened = !dropdown.classList.contains('hidden');

            this.closeDropdowns();

            if (opened) {

                arrow?.classList.remove('rotate-180');

                return;

            }

            dropdown.classList.remove('hidden');

            arrow?.classList.add('rotate-180');

        };

        GPSTracker.bindGeofenceEvents = function () {

            const button = document.getElementById('geofenceButton');

            const radius = document.getElementById('toggleRadius');

            const administrative = document.getElementById('toggleAdministrative');

            button?.addEventListener('click', (event) => {

                event.stopPropagation();

                GPSTracker.toggleGeofenceDropdown();

            });

            radius?.addEventListener('change', function () {

                if (this.checked) {

                    GPSTracker.showRadius();

                } else {

                    GPSTracker.hideRadius();

                }

            });

            administrative?.addEventListener('change', function () {

                if (this.checked) {

                    GPSTracker.showAdministrative();

                } else {

                    GPSTracker.hideAdministrative();

                }

            });

        };

        /*
        |--------------------------------------------------------------------------
        | Ready
        |--------------------------------------------------------------------------
        */

        GPSTracker.bindGeofenceEvents();

        document.dispatchEvent(

            new CustomEvent(

                'gpstracker:geofence-ready',

                {

                    detail: {

                        count: GPSTracker.getGeofenceCount(),

                    }

                }

            )

        );

    }

);

</script>