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

    history(startDate = null, endDate = null) {

        const params = new URLSearchParams();

        if (startDate) {
            params.set('start_date', startDate);
        }

        if (endDate) {
            params.set('end_date', endDate);
        }

        const query = params.toString();

        return this.request(

            `/vehicles/${this.deviceId}/history${query ? `?${query}` : ''}`

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
    | Search Administrative Province
    |--------------------------------------------------------------------------
    */

    administrativeProvinces() {

        return this.request(
            '/api/administrative/provinces'
        );

    },

    /*
    |--------------------------------------------------------------------------
    | Search Administrative Regency
    |--------------------------------------------------------------------------
    */

    administrativeRegencies() {

        return this.request(
            '/api/administrative/regencies'
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

    },

    /*
    |--------------------------------------------------------------------------
    | Route (Map-Matching - garis mengikuti jalan)
    |--------------------------------------------------------------------------
    */

    route(points = []) {

        return this.request(

            `/vehicles/${this.deviceId}/route`,

            {

                method: 'POST',

                body: { points },

            }

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */

    markNotificationRead(id) {

        return this.request(

            `/notifications/${id}/read`,

            {

                method: 'PATCH',

            }

        );

    },

    markAllNotificationsRead() {

        return this.request(

            '/notifications/mark-all-read',

            {

                method: 'PATCH',

            }

        );

    },

};

</script>