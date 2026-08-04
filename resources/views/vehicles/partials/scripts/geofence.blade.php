<script>

window.VehicleGeofence = {

    /*
    |--------------------------------------------------------------------------
    | Properties
    |--------------------------------------------------------------------------
    */

    state: null,

    layers: [],

    /*
    |--------------------------------------------------------------------------
    | Initialize
    |--------------------------------------------------------------------------
    */

    init(state) {

        this.state = state;

        this.bindEvents();

        this.load();

    },

    /*
    |--------------------------------------------------------------------------
    | Bind Events
    |--------------------------------------------------------------------------
    */

    bindEvents() {

        document.addEventListener(

            'click',

            event => {

                const button =

                    event.target.closest(

                        '.focus-geofence'

                    );

                if (!button) {

                    return;

                }

                this.focus(

                    button.dataset.id

                );

            }

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Load Geofence
    |--------------------------------------------------------------------------
    */

    async load() {

        try {

            const response = await VehicleApi.geofences();

            if (

                !response ||

                !response.success

            ) {

                return;

            }

            this.state.geofences =

                response.data ?? [];

            this.render();

        }

        catch (error) {

            console.error(

                '[VehicleGeofence]',

                error

            );

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    render() {

        this.clear();

        this.renderSummary();

        this.renderList();

        this.renderMap();

    },

    /*
    |--------------------------------------------------------------------------
    | Render Map
    |--------------------------------------------------------------------------
    */

    renderMap() {

        this.state.geofences.forEach(

            geofence => {

                this.renderItem(

                    geofence

                );

            }

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Render Summary
    |--------------------------------------------------------------------------
    */

    renderSummary() {

        const geofences =

            this.state.geofences ?? [];

        this.setText(

            'geofenceTotal',

            geofences.length

        );

        this.setText(

            'geofenceRadiusCount',

            geofences.filter(

                item => item.type === 'radius'

            ).length

        );

        this.setText(

            'geofenceAdministrativeCount',

            geofences.filter(

                item =>

                    item.type === 'administrative'

            ).length

        );

        this.setText(

            'geofencePolygonCount',

            geofences.filter(

                item =>

                    item.type === 'custom'

            ).length

        );

    },
    /*
    |--------------------------------------------------------------------------
    | Render List
    |--------------------------------------------------------------------------
    */

    renderList() {

        const container =

            document.getElementById(

                'geofenceList'

            );

        const empty =

            document.getElementById(

                'geofenceEmpty'

            );

        if (

            !container ||

            !empty

        ) {

            return;

        }

        if (

            !this.state.geofences.length

        ) {

            container.innerHTML = '';

            empty.classList.remove(

                'hidden'

            );

            return;

        }

        empty.classList.add(

            'hidden'

        );

        container.innerHTML =

            this.state.geofences.map(

                geofence =>

                    this.listItem(

                        geofence

                    )

            ).join('');

        this.bindList();

    },

    /*
    |--------------------------------------------------------------------------
    | Bind List
    |--------------------------------------------------------------------------
    */

    bindList() {

    },

    /*
    |--------------------------------------------------------------------------
    | Render Item
    |--------------------------------------------------------------------------
    */

    renderItem(geofence) {

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

            case 'custom':

                this.renderCustom(

                    geofence

                );

                break;

        }

    },

    /*
    |--------------------------------------------------------------------------
    | List Item
    |--------------------------------------------------------------------------
    */

    listItem(geofence) {

        return `

            <div
                class="flex items-center justify-between px-6 py-5"
            >

                <div>

                    <h4
                        class="font-semibold"
                    >

                        ${geofence.name}

                    </h4>

                    <p
                        class="mt-1 text-sm text-slate-500"
                    >

                        ${this.typeLabel(

                            geofence.type

                        )}

                    </p>

                </div>

                <button

                    class="focus-geofence rounded-lg border px-4 py-2"

                    data-id="${geofence.id}"

                >

                    Lihat

                </button>

            </div>

        `;

    },

        /*
    |--------------------------------------------------------------------------
    | Render Radius
    |--------------------------------------------------------------------------
    */

    renderRadius(geofence) {

        const config = geofence.config ?? {};

        if (

            config.lat == null ||

            config.lng == null ||

            config.radius == null

        ) {

            return;

        }

        const layer = L.circle(

            [

                Number(config.lat),

                Number(config.lng),

            ],

            {

                radius: Number(config.radius),

                color: '#2563eb',

                weight: 2,

                fillColor: '#3b82f6',

                fillOpacity: 0.15,

            }

        );

        VehicleMap.addOverlay(

            `geofence-${geofence.id}`,

            layer

        );

        this.layers.push({

            id: geofence.id,

            layer,

        });

    },

    /*
    |--------------------------------------------------------------------------
    | Type Label
    |--------------------------------------------------------------------------
    */

    typeLabel(type) {

        switch (type) {

            case 'radius':

                return 'Radius';

            case 'administrative':

                return 'Administrative';

            case 'custom':

                return 'Polygon';

            default:

                return '-';

        }

    },

        /*
    |--------------------------------------------------------------------------
    | Render Administrative
    |--------------------------------------------------------------------------
    */

    renderAdministrative(geofence) {

        const config = geofence.config ?? {};

        if (!config.geojson) {

            return;

        }

        const layer = L.geoJSON(

            config.geojson,

            {

                style: {

                    color: '#16a34a',

                    weight: 2,

                    fillColor: '#22c55e',

                    fillOpacity: 0.15,

                }

            }

        );

        VehicleMap.addOverlay(

            `geofence-${geofence.id}`,

            layer

        );

        this.layers.push({

            id: geofence.id,

            layer,

        });

    },

        /*
    |--------------------------------------------------------------------------
    | Render Custom
    |--------------------------------------------------------------------------
    */

    renderCustom(geofence) {

        const config = geofence.config ?? {};

        if (

            !Array.isArray(

                config.coordinates

            )

        ) {

            return;

        }

        const layer = L.polygon(

            config.coordinates,

            {

                color: '#dc2626',

                weight: 2,

                fillColor: '#ef4444',

                fillOpacity: 0.15,

            }

        );

        VehicleMap.addOverlay(

            `geofence-${geofence.id}`,

            layer

        );

        this.layers.push({

            id: geofence.id,

            layer,

        });

    },

    /*
    |--------------------------------------------------------------------------
    | Focus
    |--------------------------------------------------------------------------
    */

    focus(id) {

        const layer = this.layers.find(

            item => item.id == id

        );

        if (!layer) {

            return;

        }

        if (

            typeof layer.layer.getBounds ===

            'function'

        ){

            VehicleMap.fitBounds(

                layer.layer.getBounds()

            );

            return;

        }

        if (

            typeof layer.layer.getLatLng ===

            'function'

        ) {

            const point =

                layer.layer.getLatLng();

            VehicleMap.flyTo(

                point.lat,

                point.lng

            );

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */

    setText(id, value) {

        const element =

            document.getElementById(id);

        if (element) {

            element.textContent = value;

        }

    },

        /*
    |--------------------------------------------------------------------------
    | Clear
    |--------------------------------------------------------------------------
    */

    clear() {

        this.layers.forEach(

            item => {

                VehicleMap.removeOverlay(

                    `geofence-${item.id}`

                );

            }

        );

        this.layers = [];

    },

    /*
    |--------------------------------------------------------------------------
    | Reload
    |--------------------------------------------------------------------------
    */

    async reload() {

        this.clear();

        await this.load();

    },

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    destroy() {

        this.clear();

        this.layers = [];

        this.state = null;

    }

};

</script>