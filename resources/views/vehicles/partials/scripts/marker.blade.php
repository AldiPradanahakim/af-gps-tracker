<script>

window.VehicleMarker = {

    /*
    |--------------------------------------------------------------------------
    | Properties
    |--------------------------------------------------------------------------
    */

    state: null,

    marker: null,

    icon: null,

    followMode: true,

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

                this.createMarker();

            }

        );

        this.bindEvents();

    },

    /*
    |--------------------------------------------------------------------------
    | Marker Icon
    |--------------------------------------------------------------------------
    */

    createIcon() {

        if (this.icon) {

            return this.icon;

        }

        const vehicle = this.state.device.vehicle ?? {};

        const defaultIcon = `<span class="vehicle-marker-icon"><i class="fa-solid fa-motorcycle"></i></span>`;

        const iconHtml = vehicle.marker_icon

            ? `<span class="vehicle-marker-icon"><img src="${vehicle.marker_icon}" alt="Marker" class="h-5 w-5 object-contain"/></span>`

            : defaultIcon;

        this.icon = L.divIcon({

            html: `<span class="vehicle-marker-icon-wrapper">${iconHtml}</span>`,

            className: '',

            iconSize: [48, 58],

            iconAnchor: [24, 58],

            popupAnchor: [0, -52],

        });

        return this.icon;

    },

    /*
    |--------------------------------------------------------------------------
    | Create Marker
    |--------------------------------------------------------------------------
    */

    createMarker() {

        const location = this.state.latestLocation;

        if (

            !location ||

            location.lat == null ||

            location.lng == null

        ) {

            return;

        }

        this.marker = L.marker(

            [

                location.lat,

                location.lng,

            ],

            {

                icon: this.createIcon(),

            }

        );

        this.marker.bindPopup(

            this.createPopup()

        );

        VehicleMap.addOverlay(

            'vehicle-marker',

            this.marker

        );

        VehicleMap.flyTo(

            location.lat,

            location.lng

        );

    },

        /*
    |--------------------------------------------------------------------------
    | Create Popup
    |--------------------------------------------------------------------------
    */

    createPopup() {

        const vehicle = this.state.device.vehicle ?? {};

        const location = this.state.latestLocation ?? {};

        return `

            <div class="min-w-[240px] rounded-[20px] bg-white p-4 shadow-[0_18px_48px_rgba(15,23,42,0.12)]">

                <div class="flex items-center justify-between gap-4">

                    <div>

                        <div class="text-sm font-semibold text-slate-900">${vehicle.vehicle_name ?? '-'}</div>

                        <div class="mt-1 text-xs text-slate-400">${vehicle.vehicle_type ?? '-'}</div>

                    </div>

                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.18em] text-emerald-700">
                        Online
                    </span>

                </div>

                <div class="mt-4 grid gap-3 text-sm text-slate-600">

                    <div class="rounded-2xl bg-slate-50 p-3">

                        <div class="text-[10px] uppercase tracking-[0.18em] text-slate-400">Device ID</div>

                        <div class="mt-2 font-semibold text-slate-900">${this.state.device.device_id}</div>

                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">

                        <div class="rounded-2xl bg-white p-3 border border-slate-100">
                            <div class="text-[10px] uppercase tracking-[0.18em] text-slate-400">Speed</div>
                            <div class="mt-2 font-semibold text-slate-900">${Number(location.speed ?? 0).toFixed(0)} km/h</div>
                        </div>

                        <div class="rounded-2xl bg-white p-3 border border-slate-100">
                            <div class="text-[10px] uppercase tracking-[0.18em] text-slate-400">Battery</div>
                            <div class="mt-2 font-semibold text-slate-900">${location.battery ?? '-'}</div>
                        </div>

                    </div>

                </div>

            </div>

        `;

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

                this.update(

                    event.detail

                );

            }

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Update Marker
    |--------------------------------------------------------------------------
    */

    update(location) {

        if (

            !this.marker ||

            location.lat == null ||

            location.lng == null

        ) {

            return;

        }

        this.state.latestLocation = {

            ...location

        };

        this.marker.setLatLng([

            Number(location.lat),

            Number(location.lng),

        ]);

        if (

            typeof this.marker.setRotationAngle === 'function'

        ) {

            this.marker.setRotationAngle(

                location.heading ?? 0

            );

        }

        this.updatePopup();

        if (

            this.followMode

        ) {

            VehicleMap.flyTo(

                location.lat,

                location.lng

            );

        }

    },

        /*
    |--------------------------------------------------------------------------
    | Update Popup
    |--------------------------------------------------------------------------
    */

    updatePopup() {

        if (!this.marker) {

            return;

        }

        this.marker.setPopupContent(

            this.createPopup()

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Show Popup
    |--------------------------------------------------------------------------
    */

    showPopup() {

        if (!this.marker) {

            return;

        }

        this.marker.openPopup();

    },

    /*
    |--------------------------------------------------------------------------
    | Hide Popup
    |--------------------------------------------------------------------------
    */

    hidePopup() {

        if (!this.marker) {

            return;

        }

        this.marker.closePopup();

    },

    /*
    |--------------------------------------------------------------------------
    | Focus Marker
    |--------------------------------------------------------------------------
    */

    focus(zoom = 17) {

        if (!this.marker) {

            return;

        }

        const position = this.marker.getLatLng();

        VehicleMap.flyTo(

            position.lat,

            position.lng,

            zoom

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Toggle Follow
    |--------------------------------------------------------------------------
    */

    toggleFollow() {

        this.followMode = !this.followMode;

        return this.followMode;

    },

    /*
    |--------------------------------------------------------------------------
    | Set Follow
    |--------------------------------------------------------------------------
    */

    setFollow(value = true) {

        this.followMode = Boolean(value);

    },

    /*
    |--------------------------------------------------------------------------
    | Get Marker
    |--------------------------------------------------------------------------
    */

    getMarker() {

        return this.marker;

    },

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    destroy() {

        if (!this.marker) {

            return;

        }

        VehicleMap.removeOverlay(

            'vehicle-marker'

        );

        this.marker.remove();

        this.marker = null;

    }

};

</script>