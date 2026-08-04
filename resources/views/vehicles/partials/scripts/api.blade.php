<script>

window.VehicleApi = {

    /*
    |--------------------------------------------------------------------------
    | Properties
    |--------------------------------------------------------------------------
    */

    deviceId: null,

    /*
    |--------------------------------------------------------------------------
    | Initialize
    |--------------------------------------------------------------------------
    */

    init(state) {

        this.deviceId = state.device.id;

    },

    /*
    |--------------------------------------------------------------------------
    | Request
    |--------------------------------------------------------------------------
    */

    async request(

        url,

        options = {}

    ) {

        const response = await fetch(

            url,

            {

                method:

                    options.method ?? 'GET',

                headers: {

                    'Accept': 'application/json',

                    'Content-Type': 'application/json',

                    'X-Requested-With': 'XMLHttpRequest',

                    'X-CSRF-TOKEN':

                        document.querySelector(

                            'meta[name="csrf-token"]'

                        )?.content,

                    ...(options.headers ?? {}),

                },

                body:

                    options.body

                        ? JSON.stringify(

                            options.body

                        )

                        : null,

            }

        );

        return await response.json();

    },

    /*
    |--------------------------------------------------------------------------
    | Latest
    |--------------------------------------------------------------------------
    */

    latest() {

        return this.request(

            `/vehicles/${this.deviceId}/latest`

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Summary
    |--------------------------------------------------------------------------
    */

    summary() {

        return this.request(

            `/vehicles/${this.deviceId}/summary`

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Activity
    |--------------------------------------------------------------------------
    */

    activity() {

        return this.request(

            `/vehicles/${this.deviceId}/activity`

        );

    },

    /*
    |--------------------------------------------------------------------------
    | History
    |--------------------------------------------------------------------------
    */

    history(date = null) {

        let url =

            `/vehicles/${this.deviceId}/history`;

        if (date) {

            url += `?date=${date}`;

        }

        return this.request(

            url

        );

    },

    stop() {

        return this.request(

            `/vehicles/${this.deviceId}/stop`

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Save Home Location
    |--------------------------------------------------------------------------
    */

    saveHomeLocation(data) {

        return this.request(

            '/api/home-location',

            {

                method: 'POST',

                body: data,

            }

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Search Location
    |--------------------------------------------------------------------------
    */

    searchLocation(query) {

        return this.request(

            `/api/location/search?q=${encodeURIComponent(query)}`

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Reverse Location
    |--------------------------------------------------------------------------
    */

    reverseLocation(lat, lng) {

        return this.request(

            `/api/location/reverse?latitude=${lat}&longitude=${lng}`

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Geofences
    |--------------------------------------------------------------------------
    */

    geofences() {

        return this.request(

            `/geofence/device/${this.deviceId}`

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Playback
    |--------------------------------------------------------------------------
    */

    playback(date = null) {

        let url =

            `/vehicles/${this.deviceId}/playback`;

        if (date) {

            url += `?date=${date}`;

        }

        return this.request(

            url

        );

    }

};

</script>