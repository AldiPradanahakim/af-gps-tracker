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
    | Escape Helper
    |--------------------------------------------------------------------------
    */

    escapeHtml(value) {

        if (value === null || value === undefined) {

            return '';
        }

        return String(value).replace(/[&<>"']/g, function (char) {

            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[char];

        });

    },

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

                async () => {

                    /*
                    |--------------------------------------------------
                    | Tombol dikunci + ikon berputar selama request
                    | berlangsung, lalu ditutup toast. Tanpa umpan balik
                    | ini tombol terasa "tidak jalan" walau datanya
                    | sebenarnya sudah diperbarui.
                    |--------------------------------------------------
                    */

                    this.setRefreshLoading(refreshButton, true);

                    GPSLoading.show(
                        'Memuat informasi kendaraan',
                        'Mengambil data terbaru dari server…'
                    );

                    try {

                        await Promise.all([

                            this.loadSummary(),

                            this.loadActivity(),

                        ]);

                        GPSTracker.showToast(

                            'success',

                            'Diperbarui',

                            'Informasi kendaraan berhasil dimuat ulang.'

                        );

                    }

                    finally {

                        GPSLoading.hide();

                        this.setRefreshLoading(refreshButton, false);

                    }

                }

            );

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Refresh Loading State
    |--------------------------------------------------------------------------
    */

    setRefreshLoading(button, isLoading) {

        if (!button) {

            return;

        }

        const icon = button.querySelector('i');

        button.disabled = isLoading;

        button.classList.toggle('opacity-60', isLoading);

        button.classList.toggle('cursor-not-allowed', isLoading);

        icon?.classList.toggle('fa-spin', isLoading);

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

        this.toggleField(

            'vehicleMarkerIcon'

        );

        this.toggleField(

            'vehicleMarkerColor'

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
                .trim()
                .toLowerCase();

        // Marker icon mapping for reset
        const selectedIcon = document.getElementById('vehicleMarkerIconInput').dataset.selectedIcon;
        if (selectedIcon) {
            document.getElementById('vehicleMarkerIconInput').value = selectedIcon;
        }

        // Marker color mapping for reset
        const colorMap = {
            '🟢 Hijau': 'green',
            '🔵 Biru': 'blue',
            '🔴 Merah': 'red',
            '🟠 Orange': 'orange',
            '🟡 Kuning': 'yellow',
            '🟣 Ungu': 'purple',
            '⚫ Hitam': 'black',
            '⚪ Abu-abu': 'gray'
        };
        const colorText = document.getElementById('vehicleMarkerColorText').textContent.trim();
        if (colorMap[colorText]) {
            document.getElementById('vehicleMarkerColorInput').value = colorMap[colorText];
        }

        this.toggleField(

            'vehicleName'

        );

        this.toggleField(

            'vehiclePlate'

        );

        this.toggleField(

            'vehicleType'

        );

        this.toggleField(

            'vehicleMarkerIcon'

        );

        this.toggleField(

            'vehicleMarkerColor'

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

                        marker_icon:
                            document.getElementById(
                                'vehicleMarkerIconInput'
                            ).value,

                        marker_color:
                            document.getElementById(
                                'vehicleMarkerColorInput'
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

            vehicle.vehicle_type.charAt(0).toUpperCase()

            +

            vehicle.vehicle_type.slice(1);

        document.getElementById(

            'vehicleMarkerIconText'

        ).textContent =

            (vehicle.marker_icon || '-').replace(/-/g, ' ');

        const colorTextMap = {
            'green': '🟢 Hijau',
            'blue': '🔵 Biru',
            'red': '🔴 Merah',
            'orange': '🟠 Orange',
            'yellow': '🟡 Kuning',
            'purple': '🟣 Ungu',
            'black': '⚫ Hitam',
            'gray': '⚪ Abu-abu'
        };

        document.getElementById(

            'vehicleMarkerColorText'

        ).textContent =

            colorTextMap[vehicle.marker_color] || '-';

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

        document.getElementById(
            'vehicleMarkerIconInput'
        ).value =
            vehicle.marker_icon;
        
        document.getElementById(
            'vehicleMarkerIconInput'
        ).dataset.selectedIcon =
            vehicle.marker_icon;

        document.getElementById(
            'vehicleMarkerColorInput'
        ).value =
            vehicle.marker_color;

        /*
        |--------------------------------------------------------------------------
        | Refresh Marker di Peta
        |--------------------------------------------------------------------------
        |
        | state.device dipakai bersama oleh VehicleInformation dan
        | VehicleMarker (referensi objek yang sama), jadi cukup perbarui
        | marker_icon/marker_color di sini lalu minta VehicleMarker
        | menggambar ulang ikonnya - tanpa perlu reload halaman.
        |
        */

        if (this.state?.device?.vehicle) {
            this.state.device.vehicle.marker_icon = vehicle.marker_icon;
            this.state.device.vehicle.marker_color = vehicle.marker_color;
        }

        if (window.VehicleMarker) {
            window.VehicleMarker.refreshIcon();
        }

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

            'todayDuration',

            this.formatDuration(

                summary.moving_time

            )

        );

        this.setText(

            'todayMaxSpeed',

            `${summary.max_speed} km/jam`

        );

        this.setText(

            'todayStop',

            summary.stop_count ?? 0

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Render Activity
    |--------------------------------------------------------------------------
    */

    renderActivity(activities = []) {

        const container = document.getElementById(

            'vehicleLatestHistory'

        );

        const empty = document.getElementById(

            'vehicleLatestHistoryEmpty'

        );

        if (!container) {

            return;

        }

        if (!activities.length) {

            container.innerHTML = '';

            empty?.classList.remove('hidden');

            return;

        }

        empty?.classList.add('hidden');

        container.innerHTML = activities.slice(0, 10).map(

            activity => `

                <div class="grid grid-cols-12 items-center px-6 py-3 text-[12px] text-slate-700">
                    <div class="col-span-2 text-slate-500">${this.escapeHtml(activity.received_at_label ?? activity.received_at ?? '-')}</div>
                    <div class="col-span-2">${activity.lat ?? '-'}</div>
                    <div class="col-span-2">${activity.lng ?? '-'}</div>
                    <div class="col-span-2">${activity.speed ?? 0} km/jam</div>
                    <div class="col-span-4 truncate text-slate-500">${this.escapeHtml(activity.address ?? '-')}</div>
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

        const device = this.state.device;
        const now = new Date().getTime();
        const heartbeat = device && device.last_heartbeat ? new Date(device.last_heartbeat).getTime() : 0;
        const isOnline = device && device.is_active && (now - heartbeat <= 5 * 60 * 1000);

        if (!isOnline) {

            element.textContent = 'Offline';

            element.className =
                'text-[13px] font-semibold text-slate-500';

            const container = element.parentElement;
            if (container) {
                const circle = container.querySelector('span:first-child');
                if (circle) circle.className = 'h-2 w-2 rounded-full bg-slate-400';
            }

            return;

        }

        element.textContent = 'Online';

        element.className =
            'text-[13px] font-semibold text-emerald-600';

        const container = element.parentElement;
        if (container) {
            const circle = container.querySelector('span:first-child');
            if (circle) circle.className = 'h-2 w-2 rounded-full bg-emerald-500';
        }

    },

    /*
    |--------------------------------------------------------------------------
    | Speed
    |--------------------------------------------------------------------------
    */

    updateSpeed(location) {
        const element = document.getElementById('vehicleSpeed');
        const labelElement = document.getElementById('vehicleSpeedLabel');

        if (!element || !labelElement) {
            return;
        }

        const device = this.state.device;
        const now = new Date().getTime();
        const heartbeat = device && device.last_heartbeat ? new Date(device.last_heartbeat).getTime() : 0;
        const isOnline = device && device.is_active && (now - heartbeat <= 5 * 60 * 1000);

        if (!isOnline) {
            labelElement.textContent = 'Terakhir Update';
            
            let lastTime = '-';
            if (device && device.last_heartbeat) {
                const dt = new Date(device.last_heartbeat);
                const day = String(dt.getDate()).padStart(2, '0');
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
                const month = months[dt.getMonth()];
                const year = dt.getFullYear();
                const hours = String(dt.getHours()).padStart(2, '0');
                const minutes = String(dt.getMinutes()).padStart(2, '0');
                lastTime = `${day} ${month} ${year} ${hours}:${minutes}`;
            }
            element.textContent = lastTime;
            return;
        }

        labelElement.textContent = 'Kecepatan';

        if (!location || location.speed == null) {
            element.textContent = '0 km/jam';
            return;
        }

        element.textContent = Math.round(location.speed) + ' km/jam';
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