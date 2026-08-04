<script>

window.VehicleSetting = {

    /*
    |--------------------------------------------------------------------------
    | Properties
    |--------------------------------------------------------------------------
    */

    state: null,

    /*
    |--------------------------------------------------------------------------
    | Initialize
    |--------------------------------------------------------------------------
    */

    init(state) {

        this.state = state;

        this.render();

        this.bindEvents();

    },

    /*
    |--------------------------------------------------------------------------
    | Bind Events
    |--------------------------------------------------------------------------
    */

    bindEvents() {

        document.getElementById(

            'settingFollowMarker'

        )?.addEventListener(

            'change',

            event => {

                VehicleMarker.setFollow(

                    event.target.checked

                );

            }

        );

        document.getElementById(

            'settingAutoCenter'

        )?.addEventListener(

            'change',

            event => {

                VehicleMap.autoCenter =

                    event.target.checked;

            }

        );

        document.querySelectorAll(

            '.playback-speed'

        ).forEach(

            button => {

                button.addEventListener(

                    'click',

                    () => {

                        this.changePlaybackSpeed(

                            button

                        );

                    }

                );

            }

        );

    },

        /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    render() {

        const device =

            this.state.device;

        this.setText(

            'settingDeviceId',

            device.device_id

        );

        this.setHeartbeat();

        this.setStatus();

    },

    /*
    |--------------------------------------------------------------------------
    | Device Status
    |--------------------------------------------------------------------------
    */

    setStatus() {

        const element = document.getElementById(

            'settingDeviceStatus'

        );

        if (!element) {

            return;

        }

        const location =

            this.state.latestLocation;

        if (

            !location

        ) {

            element.textContent =

                'Offline';

            element.className =

                'mt-2 font-semibold text-red-600';

            return;

        }

        element.textContent =

            'Online';

        element.className =

            'mt-2 font-semibold text-emerald-600';

    },

    /*
    |--------------------------------------------------------------------------
    | Heartbeat
    |--------------------------------------------------------------------------
    */

    setHeartbeat() {

        const element = document.getElementById(

            'settingHeartbeat'

        );

        if (!element) {

            return;

        }

        element.textContent =

            this.state.device.last_heartbeat ??

            '-';

    },

        /*
    |--------------------------------------------------------------------------
    | Playback Speed
    |--------------------------------------------------------------------------
    */

    changePlaybackSpeed(button) {

        document.querySelectorAll(

            '.playback-speed'

        ).forEach(

            item => {

                item.classList.remove(

                    'bg-blue-600',

                    'text-white'

                );

                item.classList.add(

                    'border',

                    'border-slate-300'

                );

            }

        );

        button.classList.remove(

            'border',

            'border-slate-300'

        );

        button.classList.add(

            'bg-blue-600',

            'text-white'

        );

        VehiclePlayback.setSpeed(

            button.dataset.speed

        );

    },

        /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    setText(id, value) {

        const element = document.getElementById(

            id

        );

        if (element) {

            element.textContent =

                value ?? '-';

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    destroy() {

    }

};

</script>