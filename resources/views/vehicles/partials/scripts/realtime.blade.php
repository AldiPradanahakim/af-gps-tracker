<script>

window.VehicleRealtime = {

    /*
    |--------------------------------------------------------------------------
    | Properties
    |--------------------------------------------------------------------------
    */

    state: null,

    timer: null,

    interval: 5000,

    isLoading: false,

    /*
    |--------------------------------------------------------------------------
    | Initialize
    |--------------------------------------------------------------------------
    */

    init(state) {

        this.state = state;

        this.start();

    },

    /*
    |--------------------------------------------------------------------------
    | Start
    |--------------------------------------------------------------------------
    */

    start() {

        this.stop();

        this.fetchLatest();

        this.timer = setInterval(

            () => {

                this.fetchLatest();

            },

            this.interval

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Stop
    |--------------------------------------------------------------------------
    */

    stop() {

        if (!this.timer) {

            return;

        }

        clearInterval(

            this.timer

        );

        this.timer = null;

    },

    /*
    |--------------------------------------------------------------------------
    | Fetch Latest Location
    |--------------------------------------------------------------------------
    */

    async fetchLatest() {

        if (this.isLoading) {

            return;

        }

        this.isLoading = true;

        try {

            const response = await VehicleApi.latest();

            if (

                !response ||

                !response.success ||

                !response.data

            ) {

                return;

            }

            this.handleLocation(

                response.data

            );

        }

        catch (error) {

            console.error(

                '[VehicleRealtime]',

                error

            );

        }

        finally {

            this.isLoading = false;

        }

    },

        /*
    |--------------------------------------------------------------------------
    | Handle Location
    |--------------------------------------------------------------------------
    */

    handleLocation(location) {

        const current =

            Vehicle.getState().latestLocation;

        if (

            this.isSameLocation(

                current,

                location

            )

        ) {

            return;

        }

        Vehicle.updateLatestLocation(

            location

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Compare Location
    |--------------------------------------------------------------------------
    */

    isSameLocation(current, next) {

        if (

            !current ||

            !next

        ) {

            return false;

        }

        return (

            Number(current.lat) === Number(next.lat)

            &&

            Number(current.lng) === Number(next.lng)

            &&

            Number(current.heading ?? 0) ===

            Number(next.heading ?? 0)

            &&

            Number(current.speed ?? 0) ===

            Number(next.speed ?? 0)

        );

    },

        /*
    |--------------------------------------------------------------------------
    | Interval
    |--------------------------------------------------------------------------
    */

    setInterval(interval) {

        this.interval = Number(interval);

        this.start();

    },

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    destroy() {

        this.stop();

    }

};

</script>