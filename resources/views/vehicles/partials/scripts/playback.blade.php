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

        if (

            window.VehiclePath

        ) {

            VehiclePath.setPath(

                points

            );

        };

        VehiclePath.setPath(

            points

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Play
    |--------------------------------------------------------------------------
    */

    play() {

        if (

            this.isPlaying ||

            !this.histories.length

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

    },

    /*
    |--------------------------------------------------------------------------
    | Pause
    |--------------------------------------------------------------------------
    */

    pause() {

        this.isPlaying = false;

        clearInterval(

            this.timer

        );

        this.timer = null;

    },

    /*
    |--------------------------------------------------------------------------
    | Stop
    |--------------------------------------------------------------------------
    */

    stop() {

        this.pause();

        this.currentIndex = 0;

        if (

            window.VehiclePath

        ) {

            VehiclePath.clear();

        }

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

    },

        /*
    |--------------------------------------------------------------------------
    | Set Speed
    |--------------------------------------------------------------------------
    */

    setSpeed(speed) {

        this.speed = Number(speed);

        if (

            this.isPlaying

        ) {

            this.pause();

            this.play();

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