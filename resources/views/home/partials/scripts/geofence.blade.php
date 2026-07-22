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

            layers: {

                radius: new Map(),

                administrative: new Map(),

                custom: new Map(),

            },

            preview: null,

            selected: null,
            
            drawing: {

                enabled: false,

                editing: false,

                editedLayer: null,

                layer: null,

                points: [],

                markers: [],

                polygon: null,

                geojson: null,

                editable: false,

                radius: {

                    center: null,

                    circle: null,

                    value: 100,

                },

                administrative: {

                    keyword: '',

                    feature: null,

                    geojson: null,

                },

            },

        };
       
        /*
        |--------------------------------------------------------------------------
        | Geofence Config
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

            customStyle: {

                color: '#F59E0B',

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
        | Geofence Type
        |--------------------------------------------------------------------------
        */

        GPSTracker.getGeofenceTypes = function () {

            return [

                'radius',

                'administrative',

                'custom',

            ];

        };

        GPSTracker.getGeofenceStyle = function (

            type

        ) {

            const styles = {

                radius: this.getGeofenceConfig()

                    .radiusStyle,

                administrative: this.getGeofenceConfig()

                    .administrativeStyle,

                custom: this.getGeofenceConfig()

                    .customStyle,

            };

            return styles[type] ?? {};

        };

        GPSTracker.getGeofenceLayerGroup = function (type) {

            return this.getLayer(type);

        };

        /*
        |--------------------------------------------------------------------------
        | Generic GeoJSON Renderer
        |--------------------------------------------------------------------------
        */

        GPSTracker.renderGeoJsonGeofence = function (

            geofence,

            type

        ) {

            if (

                !geofence?.config?.geojson

            ) {

                return;

            }

            if (
                !geofence.config.geojson.features ||
                geofence.config.geojson.features.length === 0
            ) {
                return;
            }

            const polygon = L.geoJSON(

                geofence.config.geojson,

                {

                    style: () => {

                        return this.getGeofenceStyle(

                            type

                        );

                    },

                }

            );

            polygon.bindPopup(

                `<strong>${geofence.name}</strong>`

            );

            polygon.on(
                'click',
                () => {
                    this.setSelectedGeofence(geofence);
                }
            );

            this.setGeofenceLayer(

                type,

                geofence.id,

                polygon

            );

            polygon.addTo(

                this.getGeofenceLayerGroup(

                    type

                )

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Generic Preview
        |--------------------------------------------------------------------------
        */

        GPSTracker.previewGeoJson = function (geojson, type) {

            this.removePreviewLayer();

            if (
                !geojson ||
                !geojson.features ||
                geojson.features.length === 0
            ) {
                return;
            }

            const preview = L.geoJSON(
                geojson,
                {
                    style: () => ({
                        ...this.getGeofenceStyle(type),
                        ...this.getGeofenceConfig().previewStyle,
                    }),
                }
            );

            preview.addTo(this.map);

            this.setPreviewLayer(preview);

            const bounds = preview.getBounds();

            if (bounds.isValid()) {
                this.fitBounds(bounds);
            }

        };

        /*
        |--------------------------------------------------------------------------
        | Generic Polygon Detection
        |--------------------------------------------------------------------------
        */

        GPSTracker.isPointInGeoJson = function (

            latitude,

            longitude,

            geofence,

            type

        ) {

            const layer = this.getGeofenceLayer(

                type,

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

                    Number(

                        longitude

                    ),

                    Number(

                        latitude

                    ),

                ]

            );

            return turf.booleanPointInPolygon(

                point,

                layer.toGeoJSON()

            );

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

        GPSTracker.getDrawingGeoJSON = function () {

            return this.geofence.drawing.geojson;

        };

        /*
        |--------------------------------------------------------------------------
        | Radius Getter
        |--------------------------------------------------------------------------
        */

        GPSTracker.getRadiusDrawing = function () {

            return this.geofence.drawing.radius;

        };
        
        /*
        |--------------------------------------------------------------------------
        | Administrative Getter
        |--------------------------------------------------------------------------
        */

        GPSTracker.getAdministrativeDrawing = function () {

            return this.geofence.drawing.administrative;

        };

        /*
        |--------------------------------------------------------------------------
        | Edit Getter
        |--------------------------------------------------------------------------
        */

        GPSTracker.isEditingDrawing = function () {

            return this.geofence.drawing.editing;

        };

        GPSTracker.getEditedLayer = function () {

            return this.geofence.drawing.editedLayer;

        };

        /*
        |--------------------------------------------------------------------------
        | Layer Getter
        |--------------------------------------------------------------------------
        */

        GPSTracker.getGeofenceLayers = function (

            type

        ) {

            return this.geofence.layers[type] ?? new Map();

        };

        GPSTracker.getRadiusLayers = function () {

            return this.getGeofenceLayers(

                'radius'

            );

        };

        GPSTracker.getAdministrativeLayers = function () {

            return this.getGeofenceLayers(

                'administrative'

            );

        };

        GPSTracker.getCustomLayers = function () {

            return this.getGeofenceLayers(

                'custom'

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Single Layer Getter
        |--------------------------------------------------------------------------
        */

        GPSTracker.getGeofenceLayer = function (

            type,

            id

        ) {

            return this.getGeofenceLayers(

                type

            ).get(

                String(id)

            ) ?? null;

        };

        GPSTracker.getRadiusLayer = function (

            id

        ) {

            return this.getGeofenceLayer(

                'radius',

                id

            );

        };

        GPSTracker.getAdministrativeLayer = function (

            id

        ) {

            return this.getGeofenceLayer(

                'administrative',

                id

            );

        };

        GPSTracker.getCustomLayer = function (

            id

        ) {

            return this.getGeofenceLayer(

                'custom',

                id

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Other Getter
        |--------------------------------------------------------------------------
        */

        GPSTracker.getPreviewLayer = function () {

            return this.geofence.preview;

        };

        GPSTracker.getSelectedGeofence = function () {

            return this.geofence.selected;

        };

        /*
        |--------------------------------------------------------------------------
        | Drawing Getter
        |--------------------------------------------------------------------------
        */

        GPSTracker.getDrawingState = function () {

            return this.geofence.drawing;

        };

        GPSTracker.getDrawingPoints = function () {

            return this.geofence.drawing.points;

        };

        GPSTracker.getDrawingMarkers = function () {

            return this.geofence.drawing.markers;

        };

        GPSTracker.getDrawingPolygon = function () {

            return this.geofence.drawing.polygon;

        };

        GPSTracker.getDrawingLayer = function () {

            return this.geofence.drawing.layer;

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
        | Edit Setter
        |--------------------------------------------------------------------------
        */

        GPSTracker.setEditingDrawing = function (

            status = true

        ) {

            this.geofence.drawing.editing = status;

        };

        GPSTracker.setEditedLayer = function (

            layer

        ) {

            this.geofence.drawing.editedLayer = layer;

        };

        /*
        |--------------------------------------------------------------------------
        | Enable Edit
        |--------------------------------------------------------------------------
        */

        GPSTracker.enableEditDrawing = function () {

            const polygon = this.getDrawingPolygon();

            if (!polygon) {

                return;

            }

            if (
                !polygon.pm
            ) {
                return;
            }

            polygon.pm.enable({
                allowSelfIntersection: false,
            });

            this.setEditedLayer(

                polygon

            );

            this.setEditingDrawing(

                true

            );

        };

        GPSTracker.disableEditDrawing = function () {

            const polygon = this.getEditedLayer();

            if (!polygon) {

                return;

            }

            if (
                !polygon.pm
            ) {
                return;
            }

            polygon.pm.disable();

            this.setEditingDrawing(

                false

            );

        };

        GPSTracker.bindEditEvents = function () {

            this.map.on(

                'pm:edit',

                event => {

                    if (!event.layer) {
                        return;
                    }

                    const geojson = event.layer.toGeoJSON();

                    this.setDrawingGeoJSON(
                        geojson
                    );

                }

            );

        };

        GPSTracker.saveEditedPolygon = function () {

            this.disableEditDrawing();

            const geojson = this.getDrawingGeoJSON();

            return geojson ?? null;;

        };

        /*
        |--------------------------------------------------------------------------
        | Layer Setter
        |--------------------------------------------------------------------------
        */

        GPSTracker.setGeofenceLayer = function (

            type,

            id,

            layer

        ) {

            this.getGeofenceLayers(

                type

            ).set(

                String(id),

                layer

            );

        };

        GPSTracker.setRadiusLayer = function (

            id,

            layer

        ) {

            this.setGeofenceLayer(

                'radius',

                id,

                layer

            );

        };

        GPSTracker.setAdministrativeLayer = function (

            id,

            layer

        ) {

            this.setGeofenceLayer(

                'administrative',

                id,

                layer

            );

        };

        GPSTracker.setCustomLayer = function (

            id,

            layer

        ) {

            this.setGeofenceLayer(

                'custom',

                id,

                layer

            );

        };

        GPSTracker.setDrawingGeoJSON = function (

            geojson

        ) {

            this.geofence.drawing.geojson = geojson;

        };

        /*
        |--------------------------------------------------------------------------
        | Drawing Setter
        |--------------------------------------------------------------------------
        */

        GPSTracker.setDrawingEnabled = function (status = true) {

            this.geofence.drawing.enabled = status;

        };

        GPSTracker.setDrawingLayer = function (layer) {

            this.geofence.drawing.layer = layer;

        };

        GPSTracker.setDrawingPoints = function (points) {

            this.geofence.drawing.points = points;

        };

        GPSTracker.setDrawingMarkers = function (markers) {

            this.geofence.drawing.markers = markers;

        };

        GPSTracker.setDrawingPolygon = function (polygon) {

            this.geofence.drawing.polygon = polygon;

        };

        GPSTracker.setDrawingEditable = function (status = true) {

            this.geofence.drawing.editable = status;

        };

        /*
        |--------------------------------------------------------------------------
        | Checker
        |--------------------------------------------------------------------------
        */

        GPSTracker.hasGeofenceLayer = function (

            type,

            id

        ) {

            return this.getGeofenceLayers(

                type

            ).has(

                String(id)

            );

        };

        GPSTracker.hasRadiusLayer = function (

            id

        ) {

            return this.hasGeofenceLayer(

                'radius',

                id

            );

        };

        GPSTracker.hasAdministrativeLayer = function (

            id

        ) {

            return this.hasGeofenceLayer(

                'administrative',

                id

            );

        };

        GPSTracker.hasCustomLayer = function (

            id

        ) {

            return this.hasGeofenceLayer(

                'custom',

                id

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
        | Drawing Checker
        |--------------------------------------------------------------------------
        */

        GPSTracker.isDrawingEnabled = function () {

            return this.geofence.drawing.enabled;

        };

        GPSTracker.hasDrawingPolygon = function () {

            return this.geofence.drawing.polygon !== null;

        };

        GPSTracker.hasDrawingLayer = function () {

            return this.geofence.drawing.layer !== null;

        };

        GPSTracker.isDrawingEditable = function () {

            return this.geofence.drawing.editable;

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

        GPSTracker.removeGeofenceLayer = function (

            type,

            id

        ) {

            const layer = this.getGeofenceLayer(

                type,

                id

            );

            if (!layer) {

                return;

            }

            const group = this.getGeofenceLayerGroup(type);

            if (

                group &&
                group.hasLayer(layer)

            ) {

                group.removeLayer(

                    layer

                );

            }

            this.getGeofenceLayers(type).delete(

                String(id)

            );

            const selected = this.getSelectedGeofence();

            if (

                selected &&
                String(selected.id) === String(id)

            ) {

                this.setSelectedGeofence(null);

            }

        };

        GPSTracker.clearGeofenceLayer = function (type) {

            const group = this.getGeofenceLayerGroup(type);

            this.getGeofenceLayers(type).forEach(layer => {

                if (
                    group &&
                    group.hasLayer(layer)
                ) {
                    group.removeLayer(layer);
                }

            });

            this.getGeofenceLayers(type).clear();

            this.setSelectedGeofence(null);

        };
        /*
        |--------------------------------------------------------------------------
        | Wrapper
        |--------------------------------------------------------------------------
        */
        GPSTracker.removeRadiusLayer = function (

            id

        ) {

            this.removeGeofenceLayer(

                'radius',

                id

            );

        };

        GPSTracker.removeAdministrativeLayer = function (

            id

        ) {

            this.removeGeofenceLayer(

                'administrative',

                id

            );

        };

        GPSTracker.removeCustomLayer = function (

            id

        ) {

            this.removeGeofenceLayer(

                'custom',

                id

            );

        };

        GPSTracker.clearRadiusLayers = function () {

            this.clearGeofenceLayer(

                'radius'

            );

        };

        GPSTracker.clearAdministrativeLayers = function () {

            this.clearGeofenceLayer(

                'administrative'

            );

        };

        GPSTracker.clearCustomLayers = function () {

            this.clearGeofenceLayer(

                'custom'

            );

        };

        GPSTracker.clearGeofenceLayers = function () {

            this.clearRadiusLayers();

            this.clearAdministrativeLayers();

            this.clearCustomLayers();

        };

        /*
        |--------------------------------------------------------------------------
        | Preview Layer
        |--------------------------------------------------------------------------
        */

        GGPSTracker.removePreviewLayer = function () {

            const preview = this.getPreviewLayer();

            if (!preview) {
                return;
            }

            if (this.map.hasLayer(preview)) {
                this.map.removeLayer(preview);
            }

            this.setPreviewLayer(null);

        };
        /*
        |--------------------------------------------------------------------------
        | Geofence Collection
        |--------------------------------------------------------------------------
        */

        GPSTracker.getGeofenceCount = function () {

            return (this.getGeofences() ?? []).length;

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

            if (
                isNaN(Number(geofence.config.center.latitude)) ||
                isNaN(Number(geofence.config.center.longitude))
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

                    ...this.getGeofenceConfig()

                        .radiusStyle,

                }

            );

            circle.bindPopup(`
                <strong>${geofence.name}</strong><br>
                Radius : ${Number(geofence.config.radius).toLocaleString()} m
            `);

            this.setRadiusLayer(

                geofence.id,

                circle

            );

            circle.addTo(
                this.getGeofenceLayerGroup('radius')
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

            this.renderGeoJsonGeofence(

                geofence,

                'administrative'

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Render Custom
        |--------------------------------------------------------------------------
        */

        GPSTracker.renderCustom = function (

            geofence

        ) {

            this.renderGeoJsonGeofence(

                geofence,

                'custom'

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

            const renderer = {

                radius: this.renderRadius,

                administrative: this.renderAdministrative,

                custom: this.renderCustom,

            };

            const callback = renderer[

                geofence.type

            ];

            if (

                typeof callback !== 'function'

            ) {

                this.geofenceWarn(

                    'Unknown geofence type:',

                    geofence.type

                );

                return;

            }

            callback.call(

                this,

                geofence

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Render Collection
        |--------------------------------------------------------------------------
        */

        GPSTracker.renderGeofences = function () {

            this.clearGeofenceLayers();

            this.setSelectedGeofence(null);

            const geofences = this.getGeofences() ?? [];

            geofences.forEach(
                geofence => {
                    this.renderGeofence(geofence);
                }
            );

        };

        /*
        |--------------------------------------------------------------------------
        | Start Radius Drawing
        |--------------------------------------------------------------------------
        */

        GPSTracker.startRadiusDrawing = function () {

            this.cancelRadiusDrawing();

            this.cancelAdministrativeDrawing();

            this.cancelCustomDrawing();

            this.setDrawingEnabled(true);

            this.geofence.drawing.mode = 'radius';

            this.geofence.drawing.radius.center = null;

            this.geofence.drawing.radius.circle = null;

            this.geofence.drawing.radius.value = 100;

            this.map.doubleClickZoom.disable();

            this.geofenceLog(

                'Radius drawing started.'

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Cancel Radius Drawing
        |--------------------------------------------------------------------------
        */

        GPSTracker.cancelRadiusDrawing = function () {

            const circle = this.geofence.drawing.radius.circle;

            if (

                circle &&

                this.map.hasLayer(circle)

            ) {

                this.map.removeLayer(

                    circle

                );

            }

            this.geofence.drawing.radius.center = null;

            this.geofence.drawing.radius.circle = null;

            this.setDrawingEnabled(false);

            this.geofence.drawing.mode = null;

            this.map.doubleClickZoom.enable();

        };

        /*
        |--------------------------------------------------------------------------
        | Reset Radius Drawing
        |--------------------------------------------------------------------------
        */

        GPSTracker.resetRadiusDrawing = function () {

            this.cancelRadiusDrawing();

            this.geofence.drawing.radius = {

                center: null,

                circle: null,

                value: 100,

            };

        };

        /*
        |--------------------------------------------------------------------------
        | Preview Radius Drawing
        |--------------------------------------------------------------------------
        */

        GPSTracker.previewRadiusDrawing = function (

            latitude,

            longitude,

            radius = null

        ) {

            if (radius === null) {

                radius = this.geofence.drawing.radius.value;

            }

            const drawing = this.geofence.drawing.radius;

            if (

                drawing.circle &&

                this.map.hasLayer(drawing.circle)

            ) {

                this.map.removeLayer(

                    drawing.circle

                );

            }

            drawing.center = {

                latitude,

                longitude,

            };

            drawing.circle = L.circle(

                [

                    Number(latitude),

                    Number(longitude),

                ],

                {

                    radius: Number(radius),

                    ...this.getGeofenceConfig().radiusStyle,

                }

            );

            drawing.circle.addTo(

                this.map

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Start Custom Drawing
        |--------------------------------------------------------------------------
        */

        GPSTracker.startCustomDrawing = function () {

            this.cancelCustomDrawing();

            this.cancelAdministrativeDrawing();

            this.cancelRadiusDrawing();

            this.setDrawingEnabled(true);

            this.geofence.drawing.mode = 'custom';

            this.setDrawingPoints([]);

            this.setDrawingMarkers([]);

            this.setDrawingPolygon(null);

            this.map.doubleClickZoom.disable();

            this.geofenceLog(

                'Custom drawing started.'

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Start Administrative Drawing
        |--------------------------------------------------------------------------
        */

        GPSTracker.startAdministrativeDrawing = function () {

            this.cancelCustomDrawing();

            this.cancelRadiusDrawing();

            this.removePreviewLayer();

            this.setDrawingGeoJSON(null);

            this.setDrawingEnabled(

                true

            );

            this.geofence.drawing.mode = 'administrative';

            this.geofenceLog(

                'Administrative drawing started.'

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Cancel Administrative Drawing
        |--------------------------------------------------------------------------
        */

        GPSTracker.cancelAdministrativeDrawing = function () {

            this.removePreviewLayer();

            this.removePreviewLayer();

            this.setDrawingEnabled(false);

            this.geofence.drawing.mode = null;

            this.geofence.drawing.administrative = {

                keyword: '',

                feature: null,

                geojson: null,

            };

            const result = document.getElementById(

                'administrativeResult'

            );

            if (

                result

            ) {

                result.innerHTML = '';

            }

            this.setDrawingEnabled(

                false

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Add Drawing Point
        |--------------------------------------------------------------------------
        */

        GPSTracker.addDrawingPoint = function (

            latitude,

            longitude

        ) {

            if (

                !this.isDrawingEnabled()

            ) {

                return;

            }

            const point = L.latLng(

                Number(latitude),

                Number(longitude)

            );

            this.getDrawingPoints().push(

                point

            );

            const marker = L.circleMarker(

                point,

                {

                    radius: 5,

                    color: '#F59E0B',

                    weight: 2,

                    fillOpacity: 1,

                }

            );

            marker.addTo(

                this.map

            );

            this.getDrawingMarkers().push(

                marker

            );

            this.updateDrawingPolygon();

                if (
                this.getDrawingPoints().length > 1000
            ) {
                this.geofenceWarn(
                    'Maximum drawing points reached.'
                );
                return;
            }

        };

        /*
        |--------------------------------------------------------------------------
        | Update Drawing Polygon
        |--------------------------------------------------------------------------
        */

        GPSTracker.updateDrawingPolygon = function () {

            const points = this.getDrawingPoints();

            if (
                points.length < 3
            ) {
                return;
            }

            if (

                this.hasDrawingPolygon()

            ) {

                this.map.removeLayer(

                    this.getDrawingPolygon()

                );

            }

            const polygon = L.polygon(

                points,

                {

                    ...this.getGeofenceStyle(

                        'custom'

                    ),

                    dashArray: '6',

                }

            );

            polygon.addTo(

                this.map

            );

            this.setDrawingPolygon(

                polygon

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Finish Custom Drawing
        |--------------------------------------------------------------------------
        */

        GPSTracker.finishCustomDrawing = function () {

            if (

                this.getDrawingPoints().length < 3

            ) {

                this.geofenceWarn(

                    'Polygon requires at least 3 points.'

                );

                return null;

            }

            this.setDrawingEnabled(false);

            this.map.doubleClickZoom.enable();

            const geojson = this.exportDrawingGeoJSON();

            if (!geojson) {
                return null;
            }

            this.setDrawingGeoJSON(

                geojson

            );

            return geojson;

        };

        /*
        |--------------------------------------------------------------------------
        | Export Drawing GeoJSON
        |--------------------------------------------------------------------------
        */

        GPSTracker.exportDrawingGeoJSON = function () {

            if (

                !this.hasDrawingPolygon()

            ) {

                return null;

            }

            const polygon = this.getDrawingPolygon();

            if (!polygon) {
                return null;
            }

            const geojson = polygon.toGeoJSON();

            return geojson ?? null;

        };

        /*
        |--------------------------------------------------------------------------
        | Cancel Custom Drawing
        |--------------------------------------------------------------------------
        */

        GPSTracker.cancelCustomDrawing = function () {

            this.getDrawingMarkers().forEach(

                marker => {

                    this.map.removeLayer(

                        marker

                    );

                }

            );

            if (

                this.hasDrawingPolygon()

            ) {

                this.map.removeLayer(

                    this.getDrawingPolygon()

                );

            }

            this.setDrawingPoints([]);

            this.setDrawingMarkers([]);

            this.setDrawingGeoJSON(null);

            this.setDrawingPolygon(null);

            this.setDrawingEnabled(false);

            this.map.doubleClickZoom.enable();

            this.geofence.drawing.mode = null;

        };

        /*
        |--------------------------------------------------------------------------
        | Clear Drawing
        |--------------------------------------------------------------------------
        */

        GPSTracker.clearDrawing = function () {

            this.cancelCustomDrawing();

            this.cancelRadiusDrawing();

            this.cancelAdministrativeDrawing();

        };

        /*
        |--------------------------------------------------------------------------
        | Drawing Map Event
        |--------------------------------------------------------------------------
        */

        GPSTracker.bindDrawingEvents = function () {

            this.map.on(

                'click',

                event => {

                    if (

                        !this.isDrawingEnabled()

                    ) {

                        return;

                    }

                    const mode = this.geofence.drawing.mode;

                    if (

                        mode === 'custom'

                    ) {

                        this.addDrawingPoint(

                            event.latlng.lat,

                            event.latlng.lng

                        );

                        return;

                    }

                    if (

                        mode === 'radius'

                    ) {

                        const radius = this.getRadiusDrawing().value;

                            this.previewRadiusDrawing(
                                event.latlng.lat,
                                event.latlng.lng,
                                radius
                            );

                            document.dispatchEvent(
                                new CustomEvent(
                                    'gpstracker:radius-finished',
                                    {
                                        detail: {
                                            latitude: event.latlng.lat,
                                            longitude: event.latlng.lng,
                                            radius
                                        }
                                    }
                                )
                            );

                    }

                }

            );

        };

        this.map.on(

            'dblclick',

            event => {

                if (

                    !this.isDrawingEnabled()

                ) {

                    return;

                }

                L.DomEvent.stop(

                    event

                );

                const geojson = this.finishCustomDrawing();

                if (

                    geojson

                ) {

                    document.dispatchEvent(

                        new CustomEvent(

                            'gpstracker:drawing-finished',

                            {

                                detail: {

                                    geojson,

                                }

                            }

                        )

                    );

                }

            }

        );

        /*
        |--------------------------------------------------------------------------
        | Drawing Finished
        |--------------------------------------------------------------------------
        */

        document.addEventListener(

            'gpstracker:drawing-finished',

            event => {

                const geojson = event.detail.geojson;

                const input = document.getElementById(

                    'customGeojson'

                );

                if (

                    input

                ) {

                    input.value = JSON.stringify(

                        geojson

                    );

                }

            }

        );

        document.addEventListener(

            'gpstracker:geofence-create-open',

            () => {

                GPSTracker.cancelCustomDrawing();

                GPSTracker.cancelRadiusDrawing();

            }

        );

        document.addEventListener(

            'gpstracker:geofence-create-close',

            () => {

                GPSTracker.cancelCustomDrawing();

            }

        );

        /*
        |--------------------------------------------------------------------------
        | Radius Finished
        |--------------------------------------------------------------------------
        */

        document.addEventListener(

            'gpstracker:radius-finished',

            event => {

                const inputLatitude = document.getElementById(

                    'radiusLatitude'

                );

                const inputLongitude = document.getElementById(

                    'radiusLongitude'

                );

                const inputRadius = document.getElementById(

                    'radiusValue'

                );

                if (

                    inputLatitude

                ) {

                    inputLatitude.value = event.detail.latitude;

                    inputLongitude.value = event.detail.longitude;

                    inputRadius.value = event.detail.radius;

                }

                if (

                    inputLongitude

                ) {

                    inputLongitude.value = event.detail.center.longitude;

                }

                if (

                    inputRadius

                ) {

                    inputRadius.value = event.detail.radius;

                }

            }

        );

        /*
        |--------------------------------------------------------------------------
        | Focus Geofence
        |--------------------------------------------------------------------------
        */

        GPSTracker.focusGeofence = function (id) {

            const geofence = (this.getGeofences() ?? []).find(
                geofence =>
                    String(geofence.id) === String(id)
            );

            if (!geofence) {
                return;
            }

            this.setSelectedGeofence(geofence);

            this.removePreviewLayer();

            const layer = this.getGeofenceLayer(
                geofence.type,
                id
            );

            if (!layer) {
                return;
            }

            if (
                typeof layer.getBounds === 'function'
            ) {

                const bounds = layer.getBounds();

                if (bounds && bounds.isValid()) {
                    this.fitBounds(bounds);
                }

                return;
            }

            if (
                typeof layer.getLatLng === 'function'
            ) {

                this.map.flyTo(
                    layer.getLatLng(),
                    16
                );

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

            const bounds = preview.getBounds();

            if (bounds && bounds.isValid()) {
                this.fitBounds(bounds);
            }
        };

        /*
        |--------------------------------------------------------------------------
        | Update Radius Drawing
        |--------------------------------------------------------------------------
        */

        GPSTracker.updateRadiusDrawing = function (
            latitude,
            longitude,
            radius
        ) {

            this.previewRadiusDrawing(
                Number(latitude),
                Number(longitude),
                Number(radius)
            );

        };

        /*
        |--------------------------------------------------------------------------
        | Finish Radius Drawing
        |--------------------------------------------------------------------------
        */

        GPSTracker.finishRadiusDrawing = function () {

            const drawing = this.geofence.drawing.radius;

            if (

                !drawing.center

            ) {

                this.geofenceWarn(

                    'Radius center not selected.'

                );

                return null;

            }

            this.setDrawingEnabled(

                false

            );

            this.map.doubleClickZoom.enable();

            return {

                center: drawing.center,

                radius: drawing.value,

            };

        };

        /*
        |--------------------------------------------------------------------------
        | Submit Radius Geofence
        |--------------------------------------------------------------------------
        */

        GPSTracker.submitRadiusGeofence = async function (

            form

        ) {

            const drawing = this.finishRadiusDrawing();

            if (

                !drawing

            ) {

                return false;

            }

            const formData = new FormData(

                form

            );

            formData.set(

                'latitude',

                drawing.center.latitude

            );

            formData.set(

                'longitude',

                drawing.center.longitude

            );

            formData.set(

                'radius',

                drawing.radius

            );

            try {

                const response = await fetch(

                    form.action,

                    {

                        method: form.method,

                        headers: {

                            'X-CSRF-TOKEN': document.querySelector(

                                'meta[name="csrf-token"]'

                            ).content,

                            'Accept': 'application/json',

                        },

                        body: formData,

                    }

                );

                const result = await response.json();

                if (

                    !response.ok

                ) {

                    throw result;

                }

                document.dispatchEvent(

                    new CustomEvent(

                        'gpstracker:geofence-created',

                        {

                            detail: result,

                        }

                    )

                );

                this.cancelRadiusDrawing();

                return true;

            }

            catch (

                error

            ) {

                console.error(

                    error

                );

                return false;

            }

        };

        /*
        |--------------------------------------------------------------------------
        | Bind Radius Input
        |--------------------------------------------------------------------------
        */

        GPSTracker.bindRadiusInput = function () {

            const input = document.getElementById(
                'geofenceRadius'
            );

            if (!input) {
                return;
            }

            input.addEventListener(

                'input',

                event => {

                    const drawing = this.getRadiusDrawing();

                    if (!drawing.center) {
                        return;
                    }

                    drawing.value = Number(event.target.value);

                    this.updateRadiusDrawing(
                        drawing.center.latitude,
                        drawing.center.longitude,
                        drawing.value
                    );

                }

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

            if (!geojson) {
                return;
            }

            this.previewGeoJson(
                geojson,
                'administrative'
            );

        };

        /*
        |--------------------------------------------------------------------------
        | Preview Administrative Result
        |--------------------------------------------------------------------------
        */

        GPSTracker.previewAdministrativeResult = function (

            feature

        ) {

            this.geofence.drawing.administrative.feature = feature;

            this.geofence.drawing.administrative.geojson = feature.geojson;

            this.previewAdministrative(

                feature.geojson

            );

        };


        /*
        |--------------------------------------------------------------------------
        | Preview Custom
        |--------------------------------------------------------------------------
        */

        GPSTracker.previewCustom = function (
            geojson
        ) {

            if (!geojson) {
                return;
            }

            this.previewGeoJson(
                geojson,
                'custom'
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

            if (
                latitude == null ||
                longitude == null
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

        GPSTracker.isPointInAdministrative = function (

                    latitude,

                    longitude,

                    geofence

                ) {

                    return this.isPointInGeoJson(

                        latitude,

                        longitude,

                        geofence,

                        'administrative'

                    );

                };
                

                GPSTracker.isPointInCustom = function (

            latitude,

            longitude,

            geofence

        ) {

            return this.isPointInGeoJson(

                latitude,

                longitude,

                geofence,

                'custom'

            );

        };

        GPSTracker.isPointInGeofence = function (

            latitude,

            longitude,

            geofence

        ) {

            const detector = {

                radius: this.isPointInRadius,

                administrative: this.isPointInAdministrative,

                custom: this.isPointInCustom,

            };

            const callback = detector[

                geofence.type

            ];

            if (

                typeof callback !== 'function'

            ) {

                return false;

            }

            return callback.call(

                this,

                latitude,

                longitude,

                geofence

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

            if (
                vehicle.latitude == null ||
                vehicle.longitude == null
            ) {
                return false;
            }

            return this.isPointInGeofence(

                vehicle.latitude,

                vehicle.longitude,

                geofence

            );

        };

        GPSTracker.detectGeofences = function (vehicle) {

            if (!vehicle) {
                return;
            }

            const geofences = this.getGeofences() ?? [];

            geofences.forEach(geofence => {

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

            });

        };

        /*
        |--------------------------------------------------------------------------
        | Toggle Layer
        |--------------------------------------------------------------------------
        */

        GPSTracker.showGeofenceLayer = function (

            type

        ) {

            const group = this.getGeofenceLayerGroup(type);

            if (!group) {

                return;

            }

            this.getGeofenceLayers(

                type

            ).forEach(

                layer => {

                    if (

                        !group.hasLayer(

                            layer

                        )

                    ) {

                        layer.addTo(

                            group

                        );
                        if (
                            layer &&
                            !group.hasLayer(layer)
                        ) {
                            layer.addTo(group);
                        }

                    }

                }

            );

        };

        GPSTracker.hideGeofenceLayer = function (

            type

        ) {

            const group = this.getGeofenceLayerGroup(type);

            if (!group) {

                return;

            }

            this.getGeofenceLayers(

                type

            ).forEach(

                layer => {

                    if (
                        layer &&
                        group.hasLayer(layer)
                    ) {
                        group.removeLayer(layer);
                    }


                }

            );

        };

        GPSTracker.showRadius = function () {

            this.showGeofenceLayer(

                'radius'

            );

        };

        GPSTracker.hideRadius = function () {

            this.hideGeofenceLayer(

                'radius'

            );

        };

        GPSTracker.showAdministrative = function () {

            this.showGeofenceLayer(

                'administrative'

            );

        };

        GPSTracker.hideAdministrative = function () {

            this.hideGeofenceLayer(

                'administrative'

            );

        };

        GPSTracker.showCustom = function () {

            this.showGeofenceLayer(

                'custom'

            );

        };

        GPSTracker.hideCustom = function () {

            this.hideGeofenceLayer(

                'custom'

            );

        };

        GPSTracker.showAllGeofences = function () {

            this.showRadius();

            this.showAdministrative();

            this.showCustom();

        };

        GPSTracker.hideAllGeofences = function () {

            this.hideRadius();

            this.hideAdministrative();

            this.hideCustom();

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

            if (
                typeof this.getGeofences !== 'function'
            ) {
                return;
            }

            this.renderGeofences();

            this.showAllGeofences();

            this.bindDrawingEvents();
            
            this.bindRadiusInput();

            this.bindGeofenceType();

            this.bindEditEvents();

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

            this.clearDrawing();

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

            this.cancelCustomDrawing();

            this.cancelRadiusDrawing();

            this.cancelAdministrativeDrawing();

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

            const dropdown = document.getElementById(

                'geofenceDropdown'

            );

            const arrow = document.getElementById(

                'geofenceArrow'

            );

            if (!dropdown) {

                return;

            }

            const opened = !dropdown.classList.contains(

                'hidden'

            );

            this.closeDropdowns();

            if (opened) {

                arrow?.classList.remove(

                    'rotate-180'

                );

                return;

            }

            dropdown.classList.remove(

                'hidden'

            );

            arrow?.classList.add(

                'rotate-180'

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Bind Geofence Type
        |--------------------------------------------------------------------------
        */

        GPSTracker.bindGeofenceType = function () {

            const select = document.getElementById(

                'geofenceType'

            );

            if (

                !select

            ) {

                return;

            }

            select.addEventListener(

                'change',

                () => {

                    this.cancelCustomDrawing();

                    this.cancelRadiusDrawing();

                    switch (

                        select.value

                    ) {

                        case 'radius':

                            this.startRadiusDrawing();

                            break;

                        case 'administrative':

                            this.startAdministrativeDrawing();

                            break;

                        case 'custom':

                            this.startCustomDrawing();

                            break;

                    }

                }

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Topbar Event
        |--------------------------------------------------------------------------
        */

        GPSTracker.bindGeofenceEvents = function () {

            const button = document.getElementById(

                'geofenceButton'

            );

            const toggleAll = document.getElementById(

                'toggleAllGeofence'

            );

            const toggleRadius = document.getElementById(

                'toggleRadius'

            );

            const toggleAdministrative = document.getElementById(

                'toggleAdministrative'

            );

            const toggleCustom = document.getElementById(

                'toggleCustom'

            );

            const addButton = document.getElementById(

                'addGeofence'

            );

            const deleteButton = document.getElementById(

                'deleteGeofence'

            );

            const homeButton = document.getElementById(

                'setHomeLocation'

            );

            button?.addEventListener(

                'click',

                event => {

                    event.stopPropagation();

                    this.toggleGeofenceDropdown();

                }

            );

            toggleRadius?.addEventListener(

                'change',

                () => {

                    toggleRadius.checked
                        ? this.showRadius()
                        : this.hideRadius();

                }

            );

            toggleAdministrative?.addEventListener(

                'change',

                () => {

                    toggleAdministrative.checked
                        ? this.showAdministrative()
                        : this.hideAdministrative();

                }

            );

            toggleCustom?.addEventListener(

                'change',

                () => {

                    toggleCustom.checked
                        ? this.showCustom()
                        : this.hideCustom();

                }

            );

            toggleAll?.addEventListener(

                'change',

                () => {

                    const checked = toggleAll.checked;

                    if (toggleRadius) {

                        toggleRadius.checked = checked;

                    }

                    if (toggleAdministrative) {

                        toggleAdministrative.checked = checked;

                    }

                    if (toggleCustom) {

                        toggleCustom.checked = checked;

                    }

                    checked
                        ? this.showAllGeofences()
                        : this.hideAllGeofences();

                }

            );

            const updateAllCheckbox = () => {

                if (!toggleAll) {

                    return;

                }

                toggleAll.checked =

                    Boolean(toggleRadius?.checked) &&

                    Boolean(toggleAdministrative?.checked) &&

                    Boolean(toggleCustom?.checked);

            };

            toggleRadius?.addEventListener(

                'change',

                updateAllCheckbox

            );

            toggleAdministrative?.addEventListener(

                'change',

                updateAllCheckbox

            );

            toggleCustom?.addEventListener(

                'change',

                updateAllCheckbox

            );

            homeButton?.addEventListener(

                'click',

                () => {

                    document.dispatchEvent(

                        new CustomEvent(

                            'gpstracker:home-location-open'

                        )

                    );

                }

            );

            addButton?.addEventListener(

                'click',

                () => {

                    document.dispatchEvent(

                        new CustomEvent(

                            'gpstracker:geofence-create-open'

                        )

                    );

                }

            );

            deleteButton?.addEventListener(

                'click',

                () => {

                    document.dispatchEvent(

                        new CustomEvent(

                            'gpstracker:geofence-delete-open'

                        )

                    );

                }

            );

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