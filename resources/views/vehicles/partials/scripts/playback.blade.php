<script>

window.VehiclePlayback = {

    /*
    |--------------------------------------------------------------------------
    | Properties
    |--------------------------------------------------------------------------
    */

    state: null,

    histories: [],

    currentIndex: 0,

    isPlaying: false,

    speed: 1000,

    timer: null,

    /*
    |--------------------------------------------------------------------------
    | Initialize
    |--------------------------------------------------------------------------
    */

    init(state) {

        this.state = state;

        this.bindEvents();

        this.updateControls();

    },

    /*
    |--------------------------------------------------------------------------
    | Bind Events
    |--------------------------------------------------------------------------
    */

    bindEvents() {

        document.getElementById('historyPlaybackPauseButton')
            ?.addEventListener('click', () => this.pause());

        document.getElementById('historyPlaybackResumeButton')
            ?.addEventListener('click', () => this.play());

        document.getElementById('historyPlaybackStopButton')
            ?.addEventListener('click', () => this.stop());

        document.querySelectorAll('.playback-speed').forEach(button => {

            button.addEventListener(

                'click',

                () => this.changeSpeed(button)

            );

        });

    },

    /*
    |--------------------------------------------------------------------------
    | Change Speed (tombol 0.5x / 1x / 2x / 4x)
    |--------------------------------------------------------------------------
    */

    changeSpeed(button) {

        document.querySelectorAll('.playback-speed').forEach(item => {

            item.classList.remove('bg-blue-600', 'text-white');

            item.classList.add('border', 'border-slate-300');

        });

        button.classList.remove('border', 'border-slate-300');

        button.classList.add('bg-blue-600', 'text-white');

        this.setSpeed(button.dataset.speed);

    },

    /*
    |--------------------------------------------------------------------------
    | Load
    |--------------------------------------------------------------------------
    | `range` opsional: { from, to } index 0-based, inklusif, merujuk ke
    | posisi di dalam array `histories` yang dikirim. Kalau tidak diisi
    | (atau tidak valid), seluruh rentang dimainkan seperti biasa -
    | perilaku default tetap tidak berubah.
    |--------------------------------------------------------------------------
    */

    load(histories = [], range = {}) {

        this.stop();

        this.histories = this.sliceRange(histories, range);

        this.currentIndex = 0;

        if (
            !this.histories.length
        ) {

            VehiclePath.clear();

            this.updateControls();

            return;

        }

        this.renderPath();

        this.play();

    },

    /*
    |--------------------------------------------------------------------------
    | Slice Range
    |--------------------------------------------------------------------------
    */

    sliceRange(histories = [], range = {}) {

        if (!histories.length) {

            return [];

        }

        const hasValidRange =
            Number.isInteger(range.from) &&
            Number.isInteger(range.to);

        if (!hasValidRange) {

            return histories;

        }

        const from = Math.max(0, Math.min(range.from, histories.length - 1));

        const to = Math.max(0, Math.min(range.to, histories.length - 1));

        return histories.slice(from, to + 1);

    },

    /*
    |--------------------------------------------------------------------------
    | Render Path
    |--------------------------------------------------------------------------
    */

    renderPath() {

        const points =

            this.histories

                .filter(history => history.lat != null && history.lng != null)

                .map(history => ({

                    lat: history.lat,

                    lng: history.lng,

                }));

        if (!window.VehiclePath) {

            return;
        }

        // Garis lurus antar titik dulu, langsung tampil tanpa menunggu.
        VehiclePath.setPath(points);

        this.loadMatchedRoute(points);

    },

    /*
    |--------------------------------------------------------------------------
    | Load Matched Route (Map-Matching - jalan asli)
    |--------------------------------------------------------------------------
    |
    | Progressive enhancement: kalau map-matching berhasil, garis lurus
    | di atas diganti dengan rute yang mengikuti jalan asli. Kalau
    | gagal (offline, server matching down, dsb), garis lurus yang
    | sudah tampil dibiarkan apa adanya - fitur playback tidak pernah
    | rusak/kosong hanya karena map-matching gagal.
    |--------------------------------------------------------------------------
    */

    async loadMatchedRoute(points) {

        if (points.length < 2) {

            return;
        }

        try {

            const response = await VehicleApi.route(points);

            if (

                response?.success &&

                response.data?.matched &&

                response.data.path?.length >= 2

            ) {

                VehiclePath.setPath(

                    response.data.path.map(([lat, lng]) => ({ lat, lng }))

                );

            }

        } catch (error) {

            console.warn('[VehiclePlayback] Map-matching gagal, pakai garis lurus.', error);

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Play
    |--------------------------------------------------------------------------
    */

    play() {

        if (
            this.isPlaying ||
            !this.histories.length ||
            this.currentIndex >= this.histories.length
        ) {

            return;

        }

        this.isPlaying = true;

        this.timer = setInterval(

            () => {

                this.next();

            },

            this.speed

        );

        this.updateControls();

    },

    /*
    |--------------------------------------------------------------------------
    | Pause
    |--------------------------------------------------------------------------
    */

    pause() {

        if (!this.isPlaying) {

            return;

        }

        this.isPlaying = false;

        clearInterval(this.timer);

        this.timer = null;

        this.updateControls();

    },

    /*
    |--------------------------------------------------------------------------
    | Stop
    |--------------------------------------------------------------------------
    */

    stop() {

        this.isPlaying = false;

        clearInterval(this.timer);

        this.timer = null;

        this.currentIndex = 0;

        if (window.VehiclePath) {

            VehiclePath.clear();

        }

        this.updateControls();

    },

    /*
    |--------------------------------------------------------------------------
    | Next
    |--------------------------------------------------------------------------
    */

    next() {

        if (
            this.currentIndex >=
            this.histories.length
        ) {

            this.stop();

            return;

        }

        const history =

            this.histories[
                this.currentIndex
            ];

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

        this.currentIndex++;

        /*
        |--------------------------------------------------------------------------
        | Titik terakhir sudah ditampilkan -> hentikan interval tanpa
        | mereset posisi/menghapus jalur, supaya rute yang baru saja
        | selesai diputar tetap terlihat di peta.
        |--------------------------------------------------------------------------
        */

        if (this.currentIndex >= this.histories.length) {

            this.pause();

        }

        this.updateControls();

    },

    /*
    |--------------------------------------------------------------------------
    | Set Speed
    |--------------------------------------------------------------------------
    */

    setSpeed(speed) {

        this.speed = Number(speed);

        if (this.isPlaying) {

            this.pause();

            this.play();

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Update Controls (Pause / Resume / Stop / Progress)
    |--------------------------------------------------------------------------
    */

    updateControls() {

        const wrapper = document.getElementById('historyPlaybackControls');

        const startButton = document.getElementById('historyPlaybackButton');

        const pauseButton = document.getElementById('historyPlaybackPauseButton');

        const resumeButton = document.getElementById('historyPlaybackResumeButton');

        const progress = document.getElementById('historyPlaybackProgress');

        const hasHistory = this.histories.length > 0;

        const isMidway = hasHistory &&
            this.currentIndex > 0 &&
            this.currentIndex < this.histories.length;

        const isActive = this.isPlaying || isMidway;

        if (wrapper) {

            wrapper.classList.toggle('hidden', !isActive);

            wrapper.classList.toggle('flex', isActive);

        }

        if (startButton) {

            startButton.disabled = this.isPlaying;

            startButton.classList.toggle('opacity-50', this.isPlaying);

            startButton.classList.toggle('cursor-not-allowed', this.isPlaying);

        }

        pauseButton?.classList.toggle('hidden', !this.isPlaying);

        resumeButton?.classList.toggle(

            'hidden',

            this.isPlaying || !isMidway

        );

        if (progress) {

            const current = Math.min(this.currentIndex, this.histories.length);

            progress.textContent = `Titik ${current} dari ${this.histories.length}`;

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    destroy() {

        this.stop();

        this.histories = [];

        this.currentIndex = 0;

        this.isPlaying = false;

    }

};

</script>
