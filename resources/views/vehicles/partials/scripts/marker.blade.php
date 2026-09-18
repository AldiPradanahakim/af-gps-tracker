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

        const vehicle = this.state.device.vehicle ?? {};

        const color = this.getMarkerColor(vehicle);

        const iconClass = this.getMarkerIconClass(vehicle);

        this.icon = L.divIcon({

            html: `
                <div style="width:56px;height:56px;display:flex;align-items:center;justify-content:center;">
                    <div style="width:46px;height:46px;border-radius:50%;background:${color};border:4px solid white;box-shadow:0 6px 16px rgba(15,23,42,0.35);display:flex;align-items:center;justify-content:center;">
                        <i class="${iconClass}" style="font-size:20px;color:white;"></i>
                    </div>
                </div>
            `,

            className: '',

            iconSize: [56, 56],

            iconAnchor: [28, 28],

            popupAnchor: [0, -28],

        });

        return this.icon;

    },

    /*
    |--------------------------------------------------------------------------
    | Refresh Icon (dipanggil setelah data marker_icon/marker_color berubah,
    | mis. setelah menyimpan Informasi Kendaraan, supaya marker di peta
    | langsung sesuai tanpa perlu reload halaman).
    |--------------------------------------------------------------------------
    */

    refreshIcon() {

        if (!this.marker) {
            return;
        }

        this.marker.setIcon(this.createIcon());

    },

    /*
    |--------------------------------------------------------------------------
    | Marker Color
    |--------------------------------------------------------------------------
    */

    getMarkerColor(vehicle) {

        const colors = {

            green: '#22c55e',
            blue: '#2563eb',
            red: '#ef4444',
            orange: '#f97316',
            yellow: '#eab308',
            purple: '#9333ea',
            black: '#111827',
            gray: '#6b7280',

        };

        return colors[vehicle.marker_color] ?? colors.blue;

    },

    /*
    |--------------------------------------------------------------------------
    | Marker Icon Class
    |--------------------------------------------------------------------------
    */

    getMarkerIconClass(vehicle) {

        const icons = {

            motorcycle: 'fa-solid fa-motorcycle',
            car: 'fa-solid fa-car',
            pickup: 'fa-solid fa-truck-pickup',
            truck: 'fa-solid fa-truck',
            bus: 'fa-solid fa-bus',
            ambulance: 'fa-solid fa-truck-medical',
            police: 'fa-solid fa-shield-halved',
            bicycle: 'fa-solid fa-bicycle',
            van: 'fa-solid fa-van-shuttle',
            taxi: 'fa-solid fa-taxi',

        };

        return icons[vehicle.marker_icon] ?? icons.motorcycle;

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

            <div class="min-w-[15rem] rounded-[1.25rem] bg-white p-4 shadow-[0_18px_48px_rgba(15,23,42,0.12)]">

                <div class="flex items-center justify-between gap-4">

                    <div>

                        <div class="text-sm font-semibold text-slate-900">${vehicle.vehicle_name ?? '-'}</div>

                        <div class="mt-1 text-xs text-slate-400">${vehicle.vehicle_type ?? '-'}</div>

                    </div>

                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-[0.625rem] font-semibold uppercase tracking-[0.18em] text-emerald-700">
                        Terhubung
                    </span>

                </div>

                <div class="mt-4 grid gap-3 text-sm text-slate-600">

                    <div class="rounded-2xl bg-slate-50 p-3">

                        <div class="text-[0.625rem] uppercase tracking-[0.18em] text-slate-400">ID Perangkat</div>

                        <div class="mt-2 font-semibold text-slate-900">${this.state.device.device_id}</div>

                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">

                        <div class="rounded-2xl bg-white p-3 border border-slate-100">
                            <div class="text-[0.625rem] uppercase tracking-[0.18em] text-slate-400">Kecepatan</div>
                            <div class="mt-2 font-semibold text-slate-900">${Number(location.speed ?? 0).toFixed(0)} km/h</div>
                        </div>

                        <div class="rounded-2xl bg-white p-3 border border-slate-100">
                            <div class="text-[0.625rem] uppercase tracking-[0.18em] text-slate-400">Baterai</div>
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

                // Selama Playback aktif, event ini juga dipicu oleh tiap
                // tick playback (lihat VehiclePlayback.next() ->
                // Vehicle.updateLatestLocation()) - abaikan supaya marker
                // tidak lompat-lompat antara posisi live dan posisi
                // playback yang sedang diputar. Sama seperti guard
                // VehiclePath.playbackActive untuk garis rute.
                if (window.VehiclePath?.playbackActive) {
                    return;
                }

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