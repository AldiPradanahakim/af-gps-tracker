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

        speed: 'vehicleSpeedSection',

        messages: 'vehicleMessagesSection',

    },

    lazyActivators: {

        history: () => window.Vehicle?.history?.activate?.(),

        stop: () => window.Vehicle?.stop?.activate?.(),

        messages: () => window.Vehicle?.messages?.activate?.(),

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

        // Pengaman: kalau overlay loading global sempat nyangkut dari
        // section sebelumnya, jangan sampai terbawa ke section baru -
        // activate() di bawah akan menyalakannya lagi kalau memang
        // section ini butuh fetch data.
        window.GPSLoading?.reset?.();

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

        this.lazyActivators[name]?.();

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

                // Reset button background and text
                button.classList.remove(

                    'bg-blue-600',

                    'text-white',

                    'bg-[#2563EB]',

                    'bg-blue-50',

                    'text-blue-700',

                    'text-slate-900'

                );

                button.classList.add(

                    'bg-white',

                    'text-slate-700'

                );

                const icon = button.querySelector('span:first-child');
                const text = button.querySelector('span:last-child');

                // Reset icon and text span styles
                if (icon) {
                    icon.classList.remove('bg-white', 'text-[#2563EB]', 'text-blue-600', 'bg-blue-100');
                    icon.classList.add('bg-slate-100', 'text-slate-500');
                }

                if (text) {
                    text.classList.remove('text-white', 'text-blue-700');
                    text.classList.add('text-slate-700');
                }

                if (

                    button.dataset.section ===

                    name

                ) {

                    button.classList.remove(

                        'bg-white',

                        'text-slate-700',

                        'hover:bg-slate-50'

                    );

                    button.classList.add(

                        'bg-[#2563EB]',

                        'text-white'

                    );

                    if (icon) {
                        icon.classList.remove('bg-slate-100', 'text-slate-500');
                        icon.classList.add('bg-white', 'text-[#2563EB]');
                    }

                    if (text) {
                        text.classList.remove('text-slate-700');
                        text.classList.add('text-white');
                    }

                } else {

                    button.classList.add('hover:bg-slate-50');

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