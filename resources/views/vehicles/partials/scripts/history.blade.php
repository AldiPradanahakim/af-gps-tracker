<script>

window.VehicleHistory = {

    /*
    |--------------------------------------------------------------------------
    | Properties
    |--------------------------------------------------------------------------
    */

    state: null,

    histories: [],

    activeHistory: null,

    startDate: null,

    endDate: null,

    loaded: false,

    /*
    |--------------------------------------------------------------------------
    | Initialize
    |--------------------------------------------------------------------------
    */

    init(state) {

        this.state = state;

        this.startDate = this.today();

        this.endDate = this.today();

        this.setDateInputs();

        this.bindEvents();

    },

    /*
    |--------------------------------------------------------------------------
    | Activate (lazy load)
    |--------------------------------------------------------------------------
    | Dipanggil saat tab "Riwayat Perjalanan" pertama kali dibuka, supaya
    | data tidak diambil dari server sebelum tab-nya benar-benar dilihat.
    |--------------------------------------------------------------------------
    */

    activate() {

        if (this.loaded) {

            return;

        }

        this.load();

    },

    /*
    |--------------------------------------------------------------------------
    | Bind Events
    |--------------------------------------------------------------------------
    */

    bindEvents() {

        document.getElementById('historyLoadButton')
            ?.addEventListener('click', () => {

                this.startDate = document.getElementById('historyStartDate').value;

                this.endDate = document.getElementById('historyEndDate').value;

                this.load();

            });

        document.getElementById('historyTodayButton')
            ?.addEventListener('click', () => {

                this.startDate = this.today();

                this.endDate = this.today();

                this.setDateInputs();

                this.load();

            });

        document.getElementById('historyPlaybackButton')
            ?.addEventListener('click', () => {

                if (!this.histories.length) {

                    GPSTracker.showToast(
                        'error',
                        'Tidak Ada Data',
                        'Tidak ada riwayat perjalanan untuk dimainkan.'
                    );

                    return;

                }

                VehiclePlayback.load(this.histories);

            });

    },

    /*
    |--------------------------------------------------------------------------
    | Load
    |--------------------------------------------------------------------------
    */

    async load() {

        this.loaded = true;

        try {

            const response = await VehicleApi.history(

                this.startDate,

                this.endDate

            );

            if (!response || !response.success) {

                this.histories = [];

                this.render();

                return;

            }

            this.histories = response.data ?? [];

            this.render();

        }

        catch (error) {

            console.error('[VehicleHistory]', error);

            GPSTracker.showToast(
                'error',
                'Gagal',
                'Gagal mengambil riwayat perjalanan.'
            );

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    render() {

        this.toggleEmptyState();

        this.renderSummary();

        this.renderTimeline();

        this.renderMapPoints();

        this.renderMapLine();

    },

    /*
    |--------------------------------------------------------------------------
    | Empty State
    |--------------------------------------------------------------------------
    */

    toggleEmptyState() {

        const empty = document.getElementById('historyEmptyState');

        const content = document.getElementById('historyContent');

        const hasData = this.histories.length > 0;

        empty?.classList.toggle('hidden', hasData);

        content?.classList.toggle('hidden', !hasData);

    },

    /*
    |--------------------------------------------------------------------------
    | Render Map Points
    |--------------------------------------------------------------------------
    */

    renderMapPoints() {

        if (!window.VehicleMap) {

            return;

        }

        VehicleMap.removeOverlay('history-points');

        if (!this.histories.length) {

            return;

        }

        const group = L.layerGroup();

        this.histories.forEach(history => {

            if (
                history.lat == null ||
                history.lng == null
            ) {

                return;

            }

            const isMoving = Number(history.speed ?? 0) > 0;

            const point = L.circleMarker(
                [history.lat, history.lng],
                {
                    radius: 5,
                    weight: 2,
                    color: '#ffffff',
                    fillColor: isMoving ? '#2563eb' : '#f97316',
                    fillOpacity: 1,
                }
            );

            point.bindPopup(`
                <div class="min-w-[220px] p-1">
                    <div class="text-sm font-semibold text-slate-900">${history.address ?? 'Lokasi tidak diketahui'}</div>
                    <div class="mt-1 text-xs text-slate-500">${history.received_at ?? '-'}</div>
                    <div class="mt-2 grid grid-cols-2 gap-2 text-xs text-slate-600">
                        <div>Lat: <b>${Number(history.lat).toFixed(6)}</b></div>
                        <div>Lng: <b>${Number(history.lng).toFixed(6)}</b></div>
                    </div>
                    <div class="mt-2 text-xs font-medium ${isMoving ? 'text-blue-600' : 'text-orange-600'}">
                        ${Number(history.speed ?? 0).toFixed(0)} km/jam ${isMoving ? '&middot; Bergerak' : '&middot; Berhenti'}
                    </div>
                </div>
            `);

            point.on('click', () => {

                this.activeHistory = history.id;

                this.highlight();

            });

            point.addTo(group);

        });

        VehicleMap.addOverlay('history-points', group);

    },

    /*
    |--------------------------------------------------------------------------
    | Render Map Line
    |--------------------------------------------------------------------------
    | Menggambar garis rute perjalanan menghubungkan seluruh titik GPS
    | pada rentang tanggal yang dipilih, tanpa perlu menekan Playback.
    |--------------------------------------------------------------------------
    */

    renderMapLine() {

        if (!window.VehicleMap) {

            return;

        }

        VehicleMap.removeOverlay('history-line');

        const points = this.histories

            .filter(history => history.lat != null && history.lng != null)

            .map(history => [history.lat, history.lng]);

        if (points.length < 2) {

            return;

        }

        const line = L.polyline(points, {

            color: '#2563eb',

            weight: 4,

            opacity: 0.75,

            lineJoin: 'round',

            lineCap: 'round',

        });

        VehicleMap.addOverlay('history-line', line);

        if (
            window.VehicleMap.fitBounds &&
            typeof line.getBounds === 'function'
        ) {

            VehicleMap.fitBounds(line.getBounds());

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Render Summary
    |--------------------------------------------------------------------------
    */

    renderSummary() {

        const histories = this.histories;

        this.setText('historyTotalPoint', histories.length);

        if (!histories.length) {

            this.setText('historyTotalDistance', '0 km');
            this.setText('historyTotalDuration', '-');
            this.setText('historyMaxSpeed', '0 km/jam');
            this.setText('historyAverageSpeed', '0 km/jam');
            this.setText('historyFirstTime', '-');
            this.setText('historyLastTime', '-');

            return;

        }

        /*
        |--------------------------------------------------------------------------
        | Kecepatan
        |--------------------------------------------------------------------------
        */

        const speeds = histories.map(
            history => Number(history.speed ?? 0)
        );

        const maxSpeed = Math.max(...speeds);

        const averageSpeed = speeds.reduce((sum, speed) => sum + speed, 0) / speeds.length;

        this.setText('historyMaxSpeed', `${maxSpeed.toFixed(0)} km/jam`);

        this.setText('historyAverageSpeed', `${averageSpeed.toFixed(0)} km/jam`);

        /*
        |--------------------------------------------------------------------------
        | Jarak (Haversine antar titik berurutan)
        |--------------------------------------------------------------------------
        */

        let totalDistance = 0;

        for (let i = 1; i < histories.length; i++) {

            const previous = histories[i - 1];

            const current = histories[i];

            if (
                previous.lat == null || previous.lng == null ||
                current.lat == null || current.lng == null
            ) {

                continue;

            }

            totalDistance += this.haversineMeters(

                previous.lat,
                previous.lng,
                current.lat,
                current.lng

            );

        }

        this.setText('historyTotalDistance', `${(totalDistance / 1000).toFixed(2)} km`);

        /*
        |--------------------------------------------------------------------------
        | Waktu Awal / Akhir / Durasi
        |--------------------------------------------------------------------------
        */

        const first = histories[0];

        const last = histories[histories.length - 1];

        this.setText('historyFirstTime', first.received_at ?? '-');

        this.setText('historyLastTime', last.received_at ?? '-');

        if (first.received_at && last.received_at) {

            const durationSeconds = Math.max(
                0,
                (new Date(last.received_at) - new Date(first.received_at)) / 1000
            );

            this.setText('historyTotalDuration', this.formatDuration(durationSeconds));

        } else {

            this.setText('historyTotalDuration', '-');

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Haversine Distance (Meter)
    |--------------------------------------------------------------------------
    */

    haversineMeters(lat1, lng1, lat2, lng2) {

        const earthRadius = 6371000;

        const toRad = degree => degree * Math.PI / 180;

        const dLat = toRad(lat2 - lat1);

        const dLng = toRad(lng2 - lng1);

        const a =
            Math.sin(dLat / 2) ** 2 +
            Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLng / 2) ** 2;

        const c = 2 * Math.asin(Math.sqrt(a));

        return earthRadius * c;

    },

    /*
    |--------------------------------------------------------------------------
    | Format Duration
    |--------------------------------------------------------------------------
    */

    formatDuration(seconds) {

        seconds = Number(seconds) || 0;

        const hours = Math.floor(seconds / 3600);

        const minutes = Math.floor((seconds % 3600) / 60);

        if (hours > 0) {

            return `${hours}j ${minutes}m`;

        }

        return `${minutes}m`;

    },

    /*
    |--------------------------------------------------------------------------
    | Timeline
    |--------------------------------------------------------------------------
    */

    renderTimeline() {

        const container = document.getElementById('historyTimeline');

        if (!container) {

            return;

        }

        if (!this.histories.length) {

            container.innerHTML = `
                <div class="py-10 text-center text-slate-500">
                    Belum ada histori.
                </div>
            `;

            return;

        }

        container.innerHTML = this.histories.map(

            history => this.timelineItem(history)

        ).join('');

        this.bindTimeline();

    },

    /*
    |--------------------------------------------------------------------------
    | Timeline Item
    |--------------------------------------------------------------------------
    */

    timelineItem(history) {

        const isMoving = Number(history.speed ?? 0) > 0;

        return `

            <button

                type="button"

                class="block w-full border-b border-slate-100 px-6 py-4 text-left hover:bg-slate-50"

                data-id="${history.id}"

            >

                <div class="flex items-center justify-between gap-4">

                    <div class="min-w-0">

                        <div class="flex items-center gap-2">

                            <span class="inline-flex items-center gap-1.5 rounded-full ${isMoving ? 'bg-blue-50 text-blue-600' : 'bg-orange-50 text-orange-600'} px-2.5 py-0.5 text-[10px] font-semibold">
                                <span class="h-1.5 w-1.5 rounded-full ${isMoving ? 'bg-blue-500' : 'bg-orange-500'}"></span>
                                ${isMoving ? 'Bergerak' : 'Berhenti'}
                            </span>

                            <p class="text-xs text-slate-400">
                                ${history.received_at ?? '-'}
                            </p>

                        </div>

                        <h4 class="mt-1.5 truncate font-semibold text-slate-900">
                            ${history.address ?? '-'}
                        </h4>

                        <p class="mt-1 text-xs text-slate-400">
                            Lat ${history.lat != null ? Number(history.lat).toFixed(6) : '-'},
                            Lng ${history.lng != null ? Number(history.lng).toFixed(6) : '-'}
                        </p>

                    </div>

                    <div class="flex-shrink-0 text-right">

                        <div class="text-lg font-semibold text-slate-900">
                            ${Number(history.speed ?? 0).toFixed(0)}
                        </div>

                        <div class="text-[11px] text-slate-400">
                            km/jam
                        </div>

                    </div>

                </div>

            </button>

        `;

    },

    /*
    |--------------------------------------------------------------------------
    | Bind Timeline
    |--------------------------------------------------------------------------
    */

    bindTimeline() {

        document.querySelectorAll('#historyTimeline button').forEach(button => {

            button.addEventListener('click', () => {

                const history = this.histories.find(

                    item => String(item.id) === String(button.dataset.id)

                );

                if (!history) {

                    return;

                }

                this.activeHistory = history.id;

                this.highlight();

                if (history.lat != null && history.lng != null && window.VehicleMap) {

                    VehicleMap.flyTo(history.lat, history.lng, 17);

                }

                this.state.latestLocation = { ...history };

                Vehicle.updateLatestLocation({

                    lat: history.lat,
                    lng: history.lng,
                    speed: history.speed,
                    heading: history.heading,
                    battery: history.battery,
                    satellite: history.satellite,
                    address: history.address,
                    received_at: history.received_at,

                });

            });

        });

    },

    /*
    |--------------------------------------------------------------------------
    | Highlight
    |--------------------------------------------------------------------------
    */

    highlight() {

        document.querySelectorAll('#historyTimeline button').forEach(button => {

            button.classList.remove('bg-blue-50', 'border-l-4', 'border-blue-500');

            if (String(button.dataset.id) === String(this.activeHistory)) {

                button.classList.add('bg-blue-50', 'border-l-4', 'border-blue-500');

            }

        });

    },

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    setDateInputs() {

        const start = document.getElementById('historyStartDate');

        const end = document.getElementById('historyEndDate');

        if (start) start.value = this.startDate;

        if (end) end.value = this.endDate;

    },

    today() {

        return new Date().toISOString().split('T')[0];

    },

    setText(id, value) {

        const element = document.getElementById(id);

        if (element) {

            element.textContent = value;

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    destroy() {

        if (window.VehicleMap) {

            VehicleMap.removeOverlay('history-points');

            VehicleMap.removeOverlay('history-line');

        }

        this.histories = [];

        this.activeHistory = null;

    },

};

</script>
