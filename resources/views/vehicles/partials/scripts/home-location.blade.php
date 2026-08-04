<script>

window.VehicleHomeLocation = {

    /*
    |--------------------------------------------------------------------------
    | Properties
    |--------------------------------------------------------------------------
    */

    state: null,

    marker: null,

    selectedLocation: null,

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

        const search = document.getElementById(

            'homeLocationSearch'

        );

        if (search) {

            let timeout = null;

            search.addEventListener(

                'input',

                () => {

                    clearTimeout(

                        timeout

                    );

                    timeout = setTimeout(

                        () => {

                            this.search(

                                search.value

                            );

                        },

                        500

                    );

                }

            );

        }

        const saveButton = document.getElementById(

            'homeLocationSaveButton'

        );

        if (saveButton) {

            saveButton.addEventListener(

                'click',

                () => this.save()

            );

        }

        const resetButton = document.getElementById(

            'homeLocationResetButton'

        );

        if (resetButton) {

            resetButton.addEventListener(

                'click',

                () => this.reset()

            );

        }

        const vehicleButton = document.getElementById(

            'homeLocationVehicleButton'

        );

        if (vehicleButton) {

            vehicleButton.addEventListener(

                'click',

                () => this.useVehicleLocation()

            );

        }

    },

        /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    async search(keyword) {

        keyword = keyword.trim();

        if (!keyword.length) {

            return;

        }

        try {

            const response = await VehicleApi.searchLocation(

                keyword

            );

            this.renderSearchResult(

                response

            );

        }

        catch (error) {

            console.error(

                '[HomeLocation] Search',

                error

            );

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Render Search Result
    |--------------------------------------------------------------------------
    */

    renderSearchResult(results = []) {

        const container = document.getElementById(

            'homeLocationSearchResult'

        );

        if (!container) {

            return;

        }

        if (!results.length) {

            container.innerHTML = '';

            container.classList.add(

                'hidden'

            );

            return;

        }

        container.classList.remove(

            'hidden'

        );

        container.innerHTML = results.map(

            result => `

                <button

                    type="button"

                    class="block w-full border-b border-slate-100 px-4 py-3 text-left hover:bg-slate-50"

                    data-lat="${result.lat}"

                    data-lng="${result.lon}"

                    data-address="${result.display_name}"

                >

                    ${result.display_name}

                </button>

            `

        ).join('');

        container.querySelectorAll(

            'button'

        ).forEach(

            button => {

                button.addEventListener(

                    'click',

                    () => {

                        this.select({

                            lat: Number(

                                button.dataset.lat

                            ),

                            lng: Number(

                                button.dataset.lng

                            ),

                            address:

                                button.dataset.address,

                        });

                    }

                );

            }

        );

    },

        /*
    |--------------------------------------------------------------------------
    | Select Location
    |--------------------------------------------------------------------------
    */

    select(location) {

        this.selectedLocation = location;

        this.setMarker(location);

        this.fillForm(location);

        VehicleMap.flyTo(

            location.lat,

            location.lng

        );

        const result = document.getElementById(

            'homeLocationSearchResult'

        );

        if (result) {

            result.classList.add(

                'hidden'

            );

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Set Marker
    |--------------------------------------------------------------------------
    */

    setMarker(location) {

        if (

            this.marker

        ) {

            VehicleMap.removeOverlay(

                'home-location'

            );

        }

        this.marker = L.marker(

            [

                location.lat,

                location.lng,

            ]

        );

        this.marker.bindPopup(

            '<strong>Home Location</strong>'

        );

        VehicleMap.addOverlay(

            'home-location',

            this.marker

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Fill Form
    |--------------------------------------------------------------------------
    */

    fillForm(location) {

        this.setValue(

            'homeLatitude',

            Number(

                location.lat

            ).toFixed(6)

        );

        this.setValue(

            'homeLongitude',

            Number(

                location.lng

            ).toFixed(6)

        );

        this.setValue(

            'homeLocationAddress',

            location.address

        );

    },

        /*
    |--------------------------------------------------------------------------
    | Use Vehicle Location
    |--------------------------------------------------------------------------
    */

    useVehicleLocation() {

        const location =

            this.state.latestLocation;

        if (

            !location ||

            location.lat == null ||

            location.lng == null

        ) {

            return;

        }

        this.select({

            lat: location.lat,

            lng: location.lng,

            address:

                location.address ??

                'Lokasi kendaraan'

        });

    },

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    render() {

        const home =

            this.state.device.home_location;

        if (

            !home

        ) {

            return;

        }

        this.select({

            lat: home.latitude,

            lng: home.longitude,

            address:

                home.display_name

        });

        this.setText(

            'savedHomeLatitude',

            Number(

                home.latitude

            ).toFixed(6)

        );

        this.setText(

            'savedHomeLongitude',

            Number(

                home.longitude

            ).toFixed(6)

        );

        this.setText(

            'savedHomeStatus',

            'Tersimpan'

        );

    },

        /*
    |--------------------------------------------------------------------------
    | Save
    |--------------------------------------------------------------------------
    */

    async save() {

        if (!this.selectedLocation) {

            this.showStatus(

                'Pilih Home Location terlebih dahulu.',

                false

            );

            return;

        }

        try {

            const response = await VehicleApi.saveHomeLocation({

                device_id: this.state.device.id,

                latitude: this.selectedLocation.lat,

                longitude: this.selectedLocation.lng,

                display_name: this.selectedLocation.address,

            });

            if (!response.success) {

                this.showStatus(

                    response.message ??

                    'Gagal menyimpan Home Location.',

                    false

                );

                return;

            }

            this.state.device.home_location = {

                latitude: this.selectedLocation.lat,

                longitude: this.selectedLocation.lng,

                display_name: this.selectedLocation.address,

            };

            this.setText(

                'savedHomeLatitude',

                Number(

                    this.selectedLocation.lat

                ).toFixed(6)

            );

            this.setText(

                'savedHomeLongitude',

                Number(

                    this.selectedLocation.lng

                ).toFixed(6)

            );

            this.setText(

                'savedHomeStatus',

                'Tersimpan'

            );

            this.showStatus(

                response.message ??

                'Home Location berhasil disimpan.',

                true

            );

        }

        catch (error) {

            console.error(

                '[HomeLocation] Save',

                error

            );

            this.showStatus(

                'Terjadi kesalahan saat menyimpan.',

                false

            );

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Reset
    |--------------------------------------------------------------------------
    */

    reset() {

        this.selectedLocation = null;

        VehicleMap.removeOverlay(

            'home-location'

        );

        this.marker = null;

        this.setValue(

            'homeLatitude',

            ''

        );

        this.setValue(

            'homeLongitude',

            ''

        );

        this.setValue(

            'homeLocationAddress',

            ''

        );

        this.setValue(

            'homeLocationSearch',

            ''

        );

        const result = document.getElementById(

            'homeLocationSearchResult'

        );

        if (result) {

            result.classList.add(

                'hidden'

            );

            result.innerHTML = '';

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    showStatus(message, success = true) {

        const element = document.getElementById(

            'homeLocationStatus'

        );

        if (!element) {

            return;

        }

        element.classList.remove(

            'hidden',

            'bg-red-50',

            'border-red-200',

            'text-red-700',

            'bg-emerald-50',

            'border-emerald-200',

            'text-emerald-700'

        );

        if (success) {

            element.classList.add(

                'bg-emerald-50',

                'border-emerald-200',

                'text-emerald-700'

            );

        }

        else {

            element.classList.add(

                'bg-red-50',

                'border-red-200',

                'text-red-700'

            );

        }

        element.textContent = message;

    },

    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */

    setValue(id, value) {

        const element = document.getElementById(

            id

        );

        if (element) {

            element.value = value ?? '';

        }

    },

    setText(id, value) {

        const element = document.getElementById(

            id

        );

        if (element) {

            element.textContent = value ?? '-';

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    destroy() {

        VehicleMap.removeOverlay(

            'home-location'

        );

        this.marker = null;

        this.selectedLocation = null;

    }

};

</script>