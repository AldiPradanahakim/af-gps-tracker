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
    */

    load(histories = []) {

        this.stop();

        this.histories = histories;

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
    | Render Path
    |--------------------------------------------------------------------------
    */

    renderPath() {

        const points =

            this.histories.map(

                history => ({

                    lat: history.lat,

                    lng: history.lng,

                })

            );

        if (window.VehiclePath) {

            VehiclePath.setPath(points);

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
