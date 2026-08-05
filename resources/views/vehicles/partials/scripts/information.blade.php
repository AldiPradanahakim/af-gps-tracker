<script>

window.VehicleInformation = {

    /*
    |--------------------------------------------------------------------------
    | Properties
    |--------------------------------------------------------------------------
    */

    state: null,

    summaryEndpoint: null,

    activityEndpoint: null,

    updateEndpoint: null,

    isEditing: false,
    /*
    |--------------------------------------------------------------------------
    | Initialize
    |--------------------------------------------------------------------------
    */

    init(state) {

        this.state = state;

        this.summaryEndpoint =
            `/vehicles/${state.device.id}/summary`;

        this.activityEndpoint =
            `/vehicles/${state.device.id}/activity`;

        this.updateEndpoint =
            `/vehicles/${state.device.id}/information`;

        this.render();

        this.bindEditEvents();

        this.bindEvents();

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

                this.state.latestLocation = event.detail;

                this.renderLocation();

            }

        );

        const refreshButton = document.getElementById(

            'refreshVehicleActivity'

        );

        if (refreshButton) {

            refreshButton.addEventListener(

                'click',

                () => {

                    this.loadSummary();

                    this.loadActivity();

                }

            );

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Edit Button
    |--------------------------------------------------------------------------
    */

    bindEditEvents() {

        const editButton =
            document.getElementById(
                'editVehicleButton'
            );

        const cancelButton =
            document.getElementById(
                'cancelVehicleButton'
            );

        const saveButton =
            document.getElementById(
                'saveVehicleButton'
            );

        editButton?.addEventListener(

            'click',

            () => this.enableEdit()

        );

        cancelButton?.addEventListener(

            'click',

            () => this.disableEdit()

        );

        saveButton?.addEventListener(

            'click',

            () => this.saveInformation()

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Enable Edit
    |--------------------------------------------------------------------------
    */

    enableEdit() {

        this.isEditing = true;

        this.toggleField(

            'vehicleName'

        );

        this.toggleField(

            'vehiclePlate'

        );

        this.toggleField(

            'vehicleType'

        );

        document
            .getElementById(
                'editVehicleButton'
            )
            ?.classList.add('hidden');

        document
            .getElementById(
                'cancelVehicleButton'
            )
            ?.classList.remove('hidden');

        document
            .getElementById(
                'saveVehicleButton'
            )
            ?.classList.remove('hidden');

    },

    /*
    |--------------------------------------------------------------------------
    | Disable Edit
    |--------------------------------------------------------------------------
    */

    disableEdit() {

        this.isEditing = false;

        document.getElementById(

            'vehicleNameInput'

        ).value =

            document.getElementById(

                'vehicleNameText'

            ).textContent;

        document.getElementById(

            'vehiclePlateInput'

        ).value =

            document.getElementById(

                'vehiclePlateText'

            ).textContent;

        document.getElementById(

            'vehicleTypeInput'

        ).value =

            document.getElementById(

                'vehicleTypeText'

            ).textContent
                .toLowerCase();

        this.toggleField(

            'vehicleName'

        );

        this.toggleField(

            'vehiclePlate'

        );

        this.toggleField(

            'vehicleType'

        );

        document
            .getElementById(
                'editVehicleButton'
            )
            ?.classList.remove('hidden');

        document
            .getElementById(
                'cancelVehicleButton'
            )
            ?.classList.add('hidden');

        document
            .getElementById(
                'saveVehicleButton'
            )
            ?.classList.add('hidden');

    },

    /*
    |--------------------------------------------------------------------------
    | Toggle Field
    |--------------------------------------------------------------------------
    */

    toggleField(prefix) {

        const text = document.getElementById(

            `${prefix}Text`

        );

        const input = document.getElementById(

            `${prefix}Input`

        );

        text?.classList.toggle(

            'hidden'

        );

        input?.classList.toggle(

            'hidden'

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Save
    |--------------------------------------------------------------------------
    */

    async saveInformation() {

        try {

            this.setLoading(true);

            const response = await fetch(

                this.updateEndpoint,

                {

                    method: 'PATCH',

                    headers: {

                        'Content-Type': 'application/json',

                        'Accept': 'application/json',

                        'X-CSRF-TOKEN': document
                            .querySelector(
                                'meta[name="csrf-token"]'
                            )
                            .content,

                        'X-Requested-With':
                            'XMLHttpRequest',

                    },

                    body: JSON.stringify({

                        vehicle_name:
                            document.getElementById(
                                'vehicleNameInput'
                            ).value,

                        plate_number:
                            document.getElementById(
                                'vehiclePlateInput'
                            ).value,

                        vehicle_type:
                            document.getElementById(
                                'vehicleTypeInput'
                            ).value,

                    }),

                }

            );

            const json = await response.json();

            /*
            |--------------------------------------------------------------------------
            | Validation Error
            |--------------------------------------------------------------------------
            */

            if (!response.ok) {

                let message = 'Informasi kendaraan gagal diperbarui.';

                if (json.errors) {

                    message = Object
                        .values(json.errors)
                        .flat()
                        .join('\n');

                } else if (json.message) {

                    message = json.message;

                }

                GPSTracker.showToast(

                    'error',

                    'Gagal',

                    message

                );

                return;

            }

            /*
            |--------------------------------------------------------------------------
            | Business Error
            |--------------------------------------------------------------------------
            */

            if (!json.success) {

                GPSTracker.showToast(

                    'error',

                    'Gagal',

                    json.message ?? 'Terjadi kesalahan.'

                );

                return;

            }

            /*
            |--------------------------------------------------------------------------
            | Success
            |--------------------------------------------------------------------------
            */

            this.updateVehicleInformation(

                json.data

            );

            GPSTracker.showToast(

                'success',

                'Berhasil',

                json.message

            );

            this.disableEdit();

        }

        catch (error) {

            console.error(error);

            GPSTracker.showToast(

                'error',

                'Server Error',

                'Terjadi kesalahan pada server.'

            );

        }

        finally {

            this.setLoading(false);

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Update View
    |--------------------------------------------------------------------------
    */

    updateVehicleInformation(vehicle) {

        document.getElementById(

            'vehicleNameText'

        ).textContent =

            vehicle.vehicle_name;

        document.getElementById(

            'vehiclePlateText'

        ).textContent =

            vehicle.plate_number;

        document.getElementById(

            'vehicleTypeText'

        ).textContent =

            vehicle.vehicle_type.charAt(0)

            +

            vehicle.vehicle_type.slice(1);

        document.getElementById(
            'vehicleNameInput'
        ).value =
            vehicle.vehicle_name;

        document.getElementById(
            'vehiclePlateInput'
        ).value =
            vehicle.plate_number;

        document.getElementById(
            'vehicleTypeInput'
        ).value =
            vehicle.vehicle_type;

    },

    /*
    |--------------------------------------------------------------------------
    | Loading Button
    |--------------------------------------------------------------------------
    */

    setLoading(isLoading) {

        const button = document.getElementById(
            'saveVehicleButton'
        );

        if (!button) {
            return;
        }

        button.disabled = isLoading;

        if (isLoading) {

            button.innerHTML = `
                <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                Menyimpan...
            `;

        } else {

            button.innerHTML = `
                <i class="fa-solid fa-floppy-disk mr-2"></i>
                Simpan
            `;

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    render() {

        this.renderLocation();

        this.loadSummary();

        this.loadActivity();

    },

    /*
    |--------------------------------------------------------------------------
    | Render Location
    |--------------------------------------------------------------------------
    */

    renderLocation() {

        const location =

            this.state.latestLocation ?? {};

        this.updateCoordinate(

            location.lat,

            location.lng

        );

        this.updateSpeed(

            location.speed

        );

        this.updateDirection(

            location.heading ?? location.direction ?? null

        );

        this.updateBattery(

            location.battery

        );

        this.updateSatellite(

            location.satellite

        );

        this.updateAddress(

            location.address

        );

        this.updateLastUpdate(

            location.received_at

        );

        this.updateStatus(

            location

        );

    },

        /*
    |--------------------------------------------------------------------------
    | Load Summary
    |--------------------------------------------------------------------------
    */

    async loadSummary() {

        try {

            const response = await fetch(

                this.summaryEndpoint,

                {

                    headers: {

                        'Accept': 'application/json',

                        'X-Requested-With': 'XMLHttpRequest',

                    }

                }

            );

            const json = await response.json();

            if (!json.success) {

                return;

            }

            this.renderSummary(

                json.data

            );

        }

        catch (error) {

            console.error(

                '[VehicleInformation] Summary',

                error

            );

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Load Activity
    |--------------------------------------------------------------------------
    */

    async loadActivity() {

        try {

            const response = await fetch(

                this.activityEndpoint,

                {

                    headers: {

                        'Accept': 'application/json',

                        'X-Requested-With': 'XMLHttpRequest',

                    }

                }

            );

            const json = await response.json();

            if (!json.success) {

                return;

            }

            this.renderActivity(

                json.data

            );

        }

        catch (error) {

            console.error(

                '[VehicleInformation] Activity',

                error

            );

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Render Summary
    |--------------------------------------------------------------------------
    */

    renderSummary(summary) {

        this.setText(

            'todayDistance',

            `${summary.total_distance} km`

        );

        this.setText(

            'todayMovingTime',

            this.formatDuration(

                summary.moving_time

            )

        );

        this.setText(

            'todayMaxSpeed',

            `${summary.max_speed} km/jam`

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Render Activity
    |--------------------------------------------------------------------------
    */

    renderActivity(activities = []) {

        const container = document.getElementById(

            'vehicleActivityTimeline'

        );

        if (!container) {

            return;

        }

        if (!activities.length) {

            container.innerHTML = `

                <div class="py-10 text-center text-sm text-slate-500">

                    Belum ada aktivitas.

                </div>

            `;

            return;

        }

        container.innerHTML = activities.map(

            activity => `

                <div class="min-w-[280px] rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl ${
                                activity.type === 'moving'
                                    ? 'bg-emerald-100 text-emerald-700'
                                    : 'bg-red-100 text-red-700'
                            }">
                                <i class="fa-solid ${
                                    activity.type === 'moving'
                                        ? 'fa-route'
                                        : 'fa-stop'
                                }"></i>
                            </span>
                            <div>
                                <h4 class="text-sm font-semibold text-slate-900">${activity.title}</h4>
                                <p class="mt-1 text-xs text-slate-400">${activity.address ?? '-'}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] uppercase tracking-[0.24em] text-slate-400">Waktu</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900">${activity.received_at}</p>
                        </div>
                    </div>
                    <div class="mt-4 grid gap-2 text-sm text-slate-500">
                        <div class="flex items-center justify-between">
                            <span>Kecepatan</span>
                            <span class="font-semibold text-slate-900">${activity.speed ?? '-'} km/jam</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Lat</span>
                            <span class="font-semibold text-slate-900">${activity.lat ?? '-'}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Lng</span>
                            <span class="font-semibold text-slate-900">${activity.lng ?? '-'}</span>
                        </div>
                    </div>
                </div>

            `

        ).join('');

    },

        /*
    |--------------------------------------------------------------------------
    | Coordinate
    |--------------------------------------------------------------------------
    */

    updateCoordinate(latitude, longitude) {

        const valueLatitude = latitude != null
            ? Number(latitude).toFixed(6)
            : '-';

        const valueLongitude = longitude != null
            ? Number(longitude).toFixed(6)
            : '-';

        this.setText(

            'vehicleLatitude',

            valueLatitude

        );

        this.setText(

            'vehicleLongitude',

            valueLongitude

        );

        this.setText(

            'vehicleMapLatitude',

            valueLatitude

        );

        this.setText(

            'vehicleMapLongitude',

            valueLongitude

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Speed
    |--------------------------------------------------------------------------
    */

    updateSpeed(speed) {

        this.setText(

            'vehicleSpeed',

            `${Number(speed ?? 0).toFixed(0)} km/jam`

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Direction
    |--------------------------------------------------------------------------
    */

    updateDirection(heading) {

        const directions = [
            'Utara',
            'Timur Laut',
            'Timur',
            'Tenggara',
            'Selatan',
            'Barat Daya',
            'Barat',
            'Barat Laut',
        ];

        const value =
            heading != null && !Number.isNaN(Number(heading))
                ? directions[
                    Math.floor(
                        (((Number(heading) % 360) + 360) / 45) + 0.5
                    ) % 8
                ]
                : '-';

        this.setText(

            'vehicleDirection',

            value

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Battery
    |--------------------------------------------------------------------------
    */

    updateBattery(battery) {

        this.setText(

            'vehicleBattery',

            battery != null
                ? `${battery} %`
                : '-'

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Satellite
    |--------------------------------------------------------------------------
    */

    updateSatellite(satellite) {

        this.setText(

            'vehicleSatellite',

            satellite ?? '-'

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Address
    |--------------------------------------------------------------------------
    */

    updateAddress(address) {

        const displayValue =
            address ??
            'Alamat belum tersedia.';

        this.setText(

            'vehicleAddress',

            displayValue

        );

        this.setText(

            'vehicleMapCurrentAddress',

            displayValue

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Last Update
    |--------------------------------------------------------------------------
    */

    updateLastUpdate(datetime) {

        if (!datetime) {

            this.setText(

                'vehicleLastUpdate',

                '-'

            );

            this.setText(

                'vehicleMapLastUpdate',

                '-'

            );

            return;

        }

        const value = new Date(

            datetime

        ).toLocaleString(

            'id-ID'

        );

        this.setText(

            'vehicleLastUpdate',

            value

        );

        this.setText(

            'vehicleMapLastUpdate',

            value

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    updateStatus(location) {

        const element = document.getElementById(

            'vehicleStatus'

        );

        if (!element) {

            return;

        }

        if (

            !location ||

            location.lat == null ||

            location.lng == null

        ) {

            element.textContent = 'Offline';

            element.className =
                'rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700';

            return;

        }

        element.textContent = 'Online';

        element.className =
            'rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700';

    },

    /*
    |--------------------------------------------------------------------------
    | Set Text
    |--------------------------------------------------------------------------
    */

    setText(id, value) {

        const element = document.getElementById(

            id

        );

        if (!element) {

            return;

        }

        element.textContent = value;

    },

    /*
    |--------------------------------------------------------------------------
    | Format Duration
    |--------------------------------------------------------------------------
    */

    formatDuration(seconds = 0) {

        seconds = Number(seconds);

        const hour = Math.floor(

            seconds / 3600

        );

        const minute = Math.floor(

            (seconds % 3600) / 60

        );

        return String(hour)

            .padStart(2, '0')

            +

            ':'

            +

            String(minute)

                .padStart(2, '0');

    }

};

</script>