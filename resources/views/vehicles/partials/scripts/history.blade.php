<script>

window.VehicleHistory = {

    /*
    |--------------------------------------------------------------------------
    | Batas Render di Sisi Klien
    |--------------------------------------------------------------------------
    |
    | Satu kendaraan bisa menghasilkan ribuan titik GPS untuk rentang
    | beberapa hari (mis. 6.200 titik untuk 3 hari). Merender semuanya
    | sekaligus membuat tab browser "Page Unresponsive", karena tiga hal
    | terjadi serentak di satu tick:
    |
    |   - timeline  : ribuan elemen HTML + satu listener per barisnya
    |   - peta      : ribuan L.circleMarker beserta popup-nya
    |   - playback  : dua <select> diisi ribuan <option> satu per satu
    |
    | Batas di bawah ini menjaga jumlah elemen DOM tetap masuk akal.
    | Yang DIBATASI hanya tampilannya - data mentahnya tetap utuh di
    | this.histories, sehingga garis rute, ringkasan jarak/kecepatan, dan
    | export PDF tetap memakai seluruh titik.
    |
    */

    MAX_TIMELINE_ROWS: 200,

    MAX_MAP_MARKERS: 1200,

    MAX_PLAYBACK_OPTIONS: 400,

    /**
     * Ambil maksimal $max item yang tersebar merata di sepanjang array.
     * Titik pertama dan terakhir selalu ikut, dan indeks aslinya
     * dipertahankan supaya penomoran yang dilihat pengguna tetap benar.
     */
    sample(items, max) {

        if (items.length <= max) {

            return items.map((item, index) => ({ item, index }));

        }

        const step = (items.length - 1) / (max - 1);

        const picked = [];

        for (let i = 0; i < max; i++) {

            const index = Math.round(i * step);

            picked.push({ item: items[index], index });

        }

        return picked;

    },


    /*
    |--------------------------------------------------------------------------
    | Properties
    |--------------------------------------------------------------------------
    */

    state: null,

    histories: [],

    activeHistory: null,

    // 'desc' = terbaru di atas, 'asc' = terlama di atas.
    sortDirection: 'desc',

    startDate: null,

    endDate: null,

    loaded: false,

    // 'points' = daftar titik GPS mentah, 'trips' = perjalanan yang dikelompokkan.
    viewMode: 'points',

    trips: [],

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

        this.startDate = this.today();

        this.endDate = this.today();

        this.setDateInputs();

        this.bindEvents();

        this.bindSortButtons();

        this.bindViewToggle();

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

                this.updateExportLink();

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

                VehiclePlayback.load(this.histories, this.getPlaybackRange());

            });

        document.getElementById('historyPlaybackRangeReset')
            ?.addEventListener('click', () => this.resetPlaybackRange());

        /*
        |--------------------------------------------------------------------------
        | Refresh
        |--------------------------------------------------------------------------
        |
        | Memuat ulang rentang tanggal yang sedang aktif dari server.
        | Tombol dikunci + ikon berputar selama request berlangsung, lalu
        | ditutup toast, supaya jelas bahwa datanya benar-benar diambil
        | ulang dari database.
        |
        */

        const refreshButton = document.getElementById('historyRefreshButton');

        refreshButton?.addEventListener('click', async () => {

            this.startDate = document.getElementById('historyStartDate')?.value
                || this.startDate;

            this.endDate = document.getElementById('historyEndDate')?.value
                || this.endDate;

            this.updateExportLink();

            this.setRefreshLoading(refreshButton, true);

            try {

                await this.load();

                GPSTracker.showToast(
                    'success',
                    'Diperbarui',
                    'Riwayat perjalanan berhasil dimuat ulang.'
                );

            }

            finally {

                this.setRefreshLoading(refreshButton, false);

            }


        });

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
    | Playback Range (pilih titik awal/akhir yang dimainkan)
    |--------------------------------------------------------------------------
    | Dropdown "Dari Titik" / "Sampai Titik" diisi ulang setiap kali
    | this.histories berganti (pencarian rentang tanggal baru), supaya
    | tidak ada jumlah titik basi dari pencarian sebelumnya.
    |--------------------------------------------------------------------------
    */

    renderPlaybackRange() {

        const wrapper = document.getElementById('historyPlaybackRange');

        const fromSelect = document.getElementById('historyPlaybackFrom');

        const toSelect = document.getElementById('historyPlaybackTo');

        if (!wrapper || !fromSelect || !toSelect) {

            return;

        }

        const hasHistory = this.histories.length > 0;

        wrapper.classList.toggle('hidden', !hasHistory);

        wrapper.classList.toggle('flex', hasHistory);

        fromSelect.innerHTML = '';

        toSelect.innerHTML = '';

        if (!hasHistory) {

            return;

        }

        /*
        |--------------------------------------------------------------------------
        | Dropdown titik playback
        |--------------------------------------------------------------------------
        |
        | Versi sebelumnya memanggil select.add() sekali per titik untuk
        | DUA dropdown - 6.200 titik berarti 12.400 penyisipan DOM satu
        | per satu, dan itulah penyebab utama tab browser membeku saat
        | memfilter rentang beberapa hari.
        |
        | Sekarang:
        |
        |   - Seluruh <option> dirangkai jadi satu string, lalu ditulis
        |     SEKALI lewat innerHTML.
        |   - Isinya di-sampling merata sampai MAX_PLAYBACK_OPTIONS. Ini
        |     bukan kompromi yang nyata: menggulir 6.200 baris di dropdown
        |     bawaan browser memang tidak bisa dipakai manusia. Nilai
        |     option tetap memakai NOMOR TITIK ASLI, jadi rentang yang
        |     dipilih tetap menunjuk titik yang benar dan playback tetap
        |     memutar seluruh titik di antaranya.
        |
        */

        // this.histories selalu urut kronologis (ascending), jadi nomor
        // titik di dropdown sesuai urutan waktu sebenarnya.
        const options = this.sample(

            this.histories,

            this.MAX_PLAYBACK_OPTIONS

        ).map(({ item: history, index }) =>

            `<option value="${index + 1}">${this.escapeOption(
                this.playbackPointLabel(history, index)
            )}</option>`

        ).join('');

        fromSelect.innerHTML = options;

        toSelect.innerHTML = options;

        this.resetPlaybackRange();

    },

    /**
     * Alamat berasal dari data eksternal (reverse geocoding), jadi harus
     * di-escape sebelum ditempel sebagai HTML.
     */
    escapeOption(value) {

        return String(value).replace(/[&<>"']/g, (char) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;',
        })[char]);

    },

    playbackPointLabel(history, index) {

        const time = history.received_at ?? '-';

        const address = history.address ?? 'Lokasi tidak diketahui';

        return `${index + 1}. ${time} - ${address}`;

    },

    resetPlaybackRange() {

        const fromSelect = document.getElementById('historyPlaybackFrom');

        const toSelect = document.getElementById('historyPlaybackTo');

        if (!fromSelect || !toSelect || !this.histories.length) {

            return;

        }

        fromSelect.value = '1';

        toSelect.value = String(this.histories.length);

    },

    /*
    |--------------------------------------------------------------------------
    | Get Playback Range (dibaca oleh tombol Playback)
    |--------------------------------------------------------------------------
    | Mengembalikan {} (rentang penuh) kalau dropdown belum siap/tidak
    | valid, supaya perilaku default playback tetap memutar seluruh data.
    |--------------------------------------------------------------------------
    */

    getPlaybackRange() {

        const fromSelect = document.getElementById('historyPlaybackFrom');

        const toSelect = document.getElementById('historyPlaybackTo');

        if (!fromSelect?.value || !toSelect?.value) {

            return {};

        }

        let from = parseInt(fromSelect.value, 10) - 1;

        let to = parseInt(toSelect.value, 10) - 1;

        if (isNaN(from) || isNaN(to)) {

            return {};

        }

        if (from > to) {

            [from, to] = [to, from];

        }

        return { from, to };

    },

    /*
    |--------------------------------------------------------------------------
    | Load
    |--------------------------------------------------------------------------
    */

    async load() {

        this.loaded = true;

        /*
        |--------------------------------------------------------------------------
        | Overlay loading global
        |--------------------------------------------------------------------------
        |
        | Rentang tanggal yang lebar bisa menarik ribuan titik GPS dan
        | butuh beberapa detik. Tanpa umpan balik, halaman terlihat diam
        | dan pengguna mengira tombol "Tampilkan" tidak berfungsi.
        |
        | Overlay ditutup di blok finally supaya tetap hilang walau
        | request-nya gagal.
        |
        */

        GPSLoading.show(
            'Memuat riwayat perjalanan',
            'Mengambil data dari server…'
        );

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

        finally {

            GPSLoading.hide();

        }

        // Trip dimuat terpisah supaya kegagalan endpoint trip tidak
        // menggagalkan tampilan titik GPS mentah (yang juga dipakai
        // untuk garis rute di peta).
        this.loadTrips();

    },

    /*
    |--------------------------------------------------------------------------
    | Load Trips (Perjalanan yang Dikelompokkan)
    |--------------------------------------------------------------------------
    */

    async loadTrips() {

        try {

            const response = await VehicleApi.trips(

                this.startDate,

                this.endDate

            );

            this.trips = (response && response.success)
                ? (response.data ?? [])
                : [];

        }

        catch (error) {

            console.error('[VehicleHistory] loadTrips', error);

            this.trips = [];

        }

        this.renderTripList();

    },

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    render() {

        // Data baru -> timeline kembali ke jumlah baris awal, supaya
        // hasil "Muat lebih banyak" dari pencarian sebelumnya tidak
        // terbawa ke rentang tanggal yang baru.
        this.timelineLimit = this.MAX_TIMELINE_ROWS;

        this.toggleEmptyState();

        this.renderSummary();

        this.renderTimeline();

        this.renderMapPoints();

        this.renderMapLine();

        this.renderPlaybackRange();

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

        /*
        |--------------------------------------------------------------------------
        | Penanda titik GPS
        |--------------------------------------------------------------------------
        |
        | Tiga hal yang membuat versi sebelumnya membekukan browser saat
        | rentangnya beberapa hari (6.000+ titik):
        |
        |   1. Satu L.circleMarker per titik - ribuan layer SVG sekaligus.
        |   2. HTML popup dibangun untuk SEMUA titik di depan, padahal
        |      99% tidak pernah dibuka.
        |   3. Satu listener klik per titik.
        |
        | Perbaikannya:
        |
        |   1. Penanda di-sampling merata sampai MAX_MAP_MARKERS. GARIS
        |      RUTE tetap memakai SELURUH titik (lihat renderMapLine),
        |      jadi bentuk perjalanannya tidak berubah - yang berkurang
        |      hanya jumlah bulatan yang bisa diklik.
        |   2. Popup dipasang sebagai fungsi, jadi HTML-nya baru dibuat
        |      saat popup benar-benar dibuka (fitur bawaan Leaflet).
        |   3. Semua penanda digambar di SATU canvas (L.canvas) - jauh
        |      lebih ringan daripada ribuan elemen SVG terpisah.
        |
        */

        const group = L.layerGroup();

        const renderer = L.canvas({ padding: 0.3 });

        const valid = this.histories.filter(

            history => history.lat != null && history.lng != null

        );

        this.sample(valid, this.MAX_MAP_MARKERS).forEach(({ item: history }) => {

            const isMoving = Number(history.speed ?? 0) > 0;

            const point = L.circleMarker(
                [history.lat, history.lng],
                {
                    renderer: renderer,
                    radius: 5,
                    weight: 2,
                    color: '#ffffff',
                    fillColor: isMoving ? '#2563eb' : '#f97316',
                    fillOpacity: 1,
                }
            );

            point.bindPopup(() => `
                <div class="min-w-[13.75rem] p-1">
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

        // this.histories tetap ascending (dipakai bersama oleh ringkasan
        // first/last time dan playback yang butuh urutan kronologis).
        // Urutan tampilan timeline diatur terpisah lewat sortDirection.
        const ordered = this.sortDirection === 'asc'
            ? [...this.histories]
            : [...this.histories].reverse();

        /*
        |--------------------------------------------------------------------------
        | Hanya sebagian yang dirender
        |--------------------------------------------------------------------------
        |
        | Baris ditambah bertahap lewat tombol "Muat lebih banyak".
        | Menyuntikkan ribuan baris sekaligus adalah salah satu penyebab
        | tab browser membeku saat memfilter rentang beberapa hari.
        |
        */

        const limit = this.timelineLimit ?? this.MAX_TIMELINE_ROWS;

        const visible = ordered.slice(0, limit);

        const remaining = ordered.length - visible.length;

        container.innerHTML = visible.map(

            history => this.timelineItem(history)

        ).join('') + (

            remaining > 0

                ? `
                    <div class="px-6 py-5 text-center">
                        <button
                            id="historyTimelineMore"
                            type="button"
                            class="inline-flex items-center gap-2 rounded-[0.875rem] border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                            <i class="fa-solid fa-chevron-down text-[0.6875rem]"></i>
                            Muat ${Math.min(remaining, this.MAX_TIMELINE_ROWS)} titik lagi
                        </button>
                        <p class="mt-2 text-[0.75rem] text-slate-500">
                            Menampilkan ${visible.length} dari ${ordered.length} titik.
                            Garis rute di peta tetap memakai seluruh titik.
                        </p>
                    </div>
                `

                : ''

        );

        this.bindTimeline();

        this.updateSortButtons();

    },

    /*
    |--------------------------------------------------------------------------
    | Sort Direction
    |--------------------------------------------------------------------------
    */

    setSortDirection(direction) {

        if (direction !== 'asc' && direction !== 'desc') {

            return;
        }

        this.sortDirection = direction;

        this.renderTimeline();

    },

    updateSortButtons() {

        const desc = document.getElementById('historySortDesc');

        const asc = document.getElementById('historySortAsc');

        if (!desc || !asc) {

            return;
        }

        const active = 'rounded-[0.75rem] bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white';

        const inactive = 'rounded-[0.75rem] border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-100';

        desc.className = this.sortDirection === 'desc' ? active : inactive;

        asc.className = this.sortDirection === 'asc' ? active : inactive;

    },

    bindSortButtons() {

        document.getElementById('historySortDesc')?.addEventListener('click', () => {

            this.setSortDirection('desc');

        });

        document.getElementById('historySortAsc')?.addEventListener('click', () => {

            this.setSortDirection('asc');

        });

    },

    /*
    |--------------------------------------------------------------------------
    | View Mode ('points' = titik GPS mentah, 'trips' = perjalanan)
    |--------------------------------------------------------------------------
    | Titik GPS mentah tetap dipertahankan (dipakai juga oleh peta untuk
    | menggambar marker/garis rute), jadi ini hanya menambah tampilan
    | alternatif tanpa menghapus kemampuan yang sudah ada.
    |--------------------------------------------------------------------------
    */

    bindViewToggle() {

        document.getElementById('historyViewPointsButton')?.addEventListener('click', () => {

            this.setViewMode('points');

        });

        document.getElementById('historyViewTripsButton')?.addEventListener('click', () => {

            this.setViewMode('trips');

        });

    },

    setViewMode(mode) {

        if (mode !== 'points' && mode !== 'trips') {

            return;
        }

        this.viewMode = mode;

        const pointsView = document.getElementById('historyTimeline');

        const tripsView = document.getElementById('historyTripList');

        const sortButtons = document.getElementById('historySortButtons');

        const isTrips = mode === 'trips';

        pointsView?.classList.toggle('hidden', isTrips);

        tripsView?.classList.toggle('hidden', !isTrips);

        sortButtons?.classList.toggle('hidden', isTrips);

        this.updateViewToggleButtons();

    },

    updateViewToggleButtons() {

        const pointsButton = document.getElementById('historyViewPointsButton');

        const tripsButton = document.getElementById('historyViewTripsButton');

        if (!pointsButton || !tripsButton) {

            return;
        }

        const active = 'rounded-[0.75rem] bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white';

        const inactive = 'rounded-[0.75rem] border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-100';

        pointsButton.className = this.viewMode === 'points' ? active : inactive;

        tripsButton.className = this.viewMode === 'trips' ? active : inactive;

    },

    /*
    |--------------------------------------------------------------------------
    | Render Trip List
    |--------------------------------------------------------------------------
    */

    renderTripList() {

        const container = document.getElementById('historyTripList');

        if (!container) {

            return;
        }

        if (!this.trips.length) {

            container.innerHTML = `
                <div class="col-span-full py-12 text-center text-slate-500">
                    Tidak ada perjalanan pada rentang tanggal ini.
                </div>
            `;

            return;
        }

        container.innerHTML = this.trips.map(

            trip => this.tripCard(trip)

        ).join('');

    },

    /*
    |--------------------------------------------------------------------------
    | Trip Card
    |--------------------------------------------------------------------------
    */

    tripCard(trip) {

        const startTime = this.escapeHtml(trip.start_time ?? '-');

        const endTime = this.escapeHtml(trip.end_time ?? '-');

        const duration = this.escapeHtml(trip.duration ?? '-');

        const distance = Number(trip.distance_km ?? 0).toFixed(1);

        const startAddress = this.escapeHtml(trip.start_address ?? 'Lokasi tidak diketahui');

        const endAddress = this.escapeHtml(trip.end_address ?? 'Lokasi tidak diketahui');

        return `

            <div class="rounded-[1.25rem] border border-slate-200 p-5">

                <div class="flex items-center justify-between gap-3">

                    <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-2.5 py-0.5 text-[0.625rem] font-semibold text-blue-600">
                        <i class="fa-solid fa-route"></i>
                        Perjalanan
                    </span>

                    <span class="text-xs font-medium text-slate-400">${duration}</span>

                </div>

                <p class="mt-3 text-sm font-semibold text-slate-900">
                    ${startTime} &rarr; ${endTime}
                </p>

                <div class="mt-3 space-y-1.5 text-xs text-slate-500">
                    <p class="truncate"><i class="fa-solid fa-circle-dot mr-1.5 text-blue-500"></i>${startAddress}</p>
                    <p class="truncate"><i class="fa-solid fa-location-dot mr-1.5 text-orange-500"></i>${endAddress}</p>
                </div>

                <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3">
                    <span class="text-xs text-slate-400">Jarak Tempuh</span>
                    <span class="text-sm font-semibold text-slate-900">${distance} km</span>
                </div>

            </div>

        `;

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

                    <div class="min-w-0 flex-1">

                        <div class="flex items-center gap-2">

                            <span class="inline-flex items-center gap-1.5 rounded-full ${isMoving ? 'bg-blue-50 text-blue-600' : 'bg-orange-50 text-orange-600'} px-2.5 py-0.5 text-[0.625rem] font-semibold">
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

                        {{--
                            "truncate" wajib di sini: koordinat 6 desimal
                            selalu lebih panjang dari kolom kiri pada layar
                            sempit, dan tanpa ini barisnya melebar sampai
                            menindih kolom kecepatan di sebelah kanan.
                        --}}
                        <p class="mt-1 truncate text-xs text-slate-400">
                            Lat ${history.lat != null ? Number(history.lat).toFixed(6) : '-'},
                            Lng ${history.lng != null ? Number(history.lng).toFixed(6) : '-'}
                        </p>

                    </div>

                    <div class="flex-shrink-0 text-right">

                        <div class="text-lg font-semibold text-slate-900">
                            ${Number(history.speed ?? 0).toFixed(0)}
                        </div>

                        <div class="text-[0.6875rem] text-slate-400">
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

        const container = document.getElementById('historyTimeline');

        if (!container) {

            return;

        }

        /*
        |--------------------------------------------------------------------------
        | Event delegation
        |--------------------------------------------------------------------------
        |
        | SATU listener di container, bukan satu listener per baris.
        | Versi sebelumnya memasang ribuan listener setiap kali timeline
        | dirender ulang - itu sendiri sudah cukup membekukan tab browser
        | pada rentang tanggal yang lebar.
        |
        | Listener dipasang sekali saja (ditandai lewat dataset), karena
        | container-nya tidak pernah diganti - hanya isinya yang berubah.
        |
        */

        if (container.dataset.timelineBound === 'true') {

            return;

        }

        container.dataset.timelineBound = 'true';

        container.addEventListener('click', (event) => {

            const more = event.target.closest('#historyTimelineMore');

            if (more) {

                this.timelineLimit =
                    (this.timelineLimit ?? this.MAX_TIMELINE_ROWS)
                    + this.MAX_TIMELINE_ROWS;

                this.renderTimeline();

                return;

            }

            const button = event.target.closest('button[data-id]');

            if (!button) {

                return;

            }

            const history = this.histories.find(

                item => String(item.id) === String(button.dataset.id)

            );

            if (!history) {

                return;

            }

            this.activeHistory = history.id;

            this.highlight();

            const isMoving = Number(history.speed ?? 0) > 0;

            window.VehicleEventFocus?.focusOn({

                type: isMoving ? 'travel_moving' : 'travel_stopped',
                title: isMoving ? 'Kendaraan Bergerak' : 'Kendaraan Berhenti',
                message: `Kecepatan ${Number(history.speed ?? 0).toFixed(0)} km/jam`,
                address: history.address,
                latitude: history.lat,
                longitude: history.lng,
                time: history.received_at,

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

        this.updateExportLink();

    },

    /*
    |--------------------------------------------------------------------------
    | Export Link (Export PDF)
    |--------------------------------------------------------------------------
    | Anchor sungguhan (bukan fetch+blob) supaya download PDF tetap
    | jalan lewat browser secara native - href-nya disinkronkan ulang
    | setiap kali rentang tanggal berubah.
    |--------------------------------------------------------------------------
    */

    updateExportLink() {

        const link = document.getElementById('historyExportPdfLink');

        if (!link || !this.state?.device?.id) {

            return;
        }

        const params = new URLSearchParams();

        if (this.startDate) params.set('from', this.startDate);

        if (this.endDate) params.set('to', this.endDate);

        link.href = `/vehicles/${this.state.device.id}/export/travel?${params.toString()}`;

    },

    today() {

        // new Date().toISOString() selalu memberi tanggal UTC - untuk
        // pengguna WIB/WITA/WIT, jam 00:00-06:59 waktu lokal masih
        // "kemarin" di UTC, sehingga "Hari Ini" salah ambil rentang.
        // Pakai getter tanggal LOKAL (timezone browser) sebagai gantinya.
        const now = new Date();

        const year = now.getFullYear();

        const month = String(now.getMonth() + 1).padStart(2, '0');

        const day = String(now.getDate()).padStart(2, '0');

        return `${year}-${month}-${day}`;

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
