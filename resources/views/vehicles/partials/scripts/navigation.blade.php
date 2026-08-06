<script>

window.VehicleNavigation = {

    /*
    |--------------------------------------------------------------------------
    | Properties
    |--------------------------------------------------------------------------
    */

    state: null,

    current: 'information',

    sections: {

        information: 'vehicleInformationSection',

        'home-location': 'vehicleHomeLocationSection',

        geofence: 'vehicleGeofenceSection',

        history: 'vehicleHistorySection',

        stop: 'vehicleStopSection',

    },

    /*
    |--------------------------------------------------------------------------
    | Initialize
    |--------------------------------------------------------------------------
    */

    init(state) {

        this.state = state;

        this.bindEvents();

        this.show(

            this.getInitialSection()

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Initial Section (dari URL hash, supaya section aktif tidak
    | balik ke "Informasi Kendaraan" setiap kali halaman di-reload)
    |--------------------------------------------------------------------------
    */

    getInitialSection() {

        const hash = window.location.hash.replace('#', '');

        if (hash && this.sections[hash]) {

            return hash;

        }

        return this.current;

    },

        /*
    |--------------------------------------------------------------------------
    | Bind Events
    |--------------------------------------------------------------------------
    */

    bindEvents() {

        document.querySelectorAll(

            '[data-section]'

        ).forEach(

            button => {

                button.addEventListener(

                    'click',

                    () => {

                        this.show(

                            button.dataset.section

                        );

                    }

                );

            }

        );

    },

        /*
    |--------------------------------------------------------------------------
    | Show Section
    |--------------------------------------------------------------------------
    */

    show(name) {

        this.hideAll();

        const section =

            this.getSection(

                name

            );

        if (!section) {

            return;

        }

        section.classList.remove(

            'hidden'

        );

        this.current = name;

        this.setActive(

            name

        );

        history.replaceState(

            null,

            '',

            `#${name}`

        );

    },

        /*
    |--------------------------------------------------------------------------
    | Hide All
    |--------------------------------------------------------------------------
    */

    hideAll() {

        Object.values(

            this.sections

        ).forEach(

            id => {

                const section =

                    document.getElementById(

                        id

                    );

                if (

                    section

                ) {

                    section.classList.add(

                        'hidden'

                    );

                }

            }

        );

    },

        /*
    |--------------------------------------------------------------------------
    | Active Menu
    |--------------------------------------------------------------------------
    */

    setActive(name) {

        document.querySelectorAll(

            '[data-section]'

        ).forEach(

            button => {

                button.classList.remove(

                    'bg-blue-600',

                    'text-white',

                    'bg-blue-50',

                    'text-slate-900'

                );

                button.classList.add(

                    'bg-white',

                    'text-slate-700'

                );

                if (

                    button.dataset.section ===

                    name

                ) {

                    button.classList.remove(

                        'bg-white',

                        'text-slate-700'

                    );

                    button.classList.add(

                        'bg-blue-600',

                        'text-white'

                    );

                }

            }

        );

    },

        /*
    |--------------------------------------------------------------------------
    | Get Section
    |--------------------------------------------------------------------------
    */

    getSection(name) {

        const id =

            this.sections[name];

        if (!id) {

            return null;

        }

        return document.getElementById(

            id

        );

    }

};

</script>