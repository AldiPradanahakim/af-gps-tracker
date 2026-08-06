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
    | Delete Home Location
    |--------------------------------------------------------------------------
    */

    deleteHomeLocation(deviceId) {

        return this.request(

            '/api/home-location',

            {

                method: 'DELETE',

                body: { device_id: deviceId },

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
    | Store Geofence
    |--------------------------------------------------------------------------
    */

    storeGeofence(payload) {

        return this.request(

            '/geofences',

            {

                method: 'POST',

                body: payload,

            }

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Update Geofence
    |--------------------------------------------------------------------------
    */

    updateGeofence(id, payload) {

        return this.request(

            `/geofences/${id}`,

            {

                method: 'PATCH',

                body: payload,

            }

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Delete Geofence
    |--------------------------------------------------------------------------
    */

    deleteGeofence(id) {

        return this.request(

            `/geofences/${id}`,

            {

                method: 'DELETE',

            }

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Search Administrative District/Village
    |--------------------------------------------------------------------------
    */

    administrativeDistricts() {

        return this.request(
            '/api/administrative/districts'
        );

    },

    /*
    |--------------------------------------------------------------------------
    | Administrative GeoJSON
    |--------------------------------------------------------------------------
    */

    administrativeGeoJson(level, code) {

        return this.request(
            `/api/administrative/geojson/${level}/${code}`
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