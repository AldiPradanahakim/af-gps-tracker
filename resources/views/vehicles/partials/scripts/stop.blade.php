<script>

window.VehicleStop = {

    /*
    |--------------------------------------------------------------------------
    | Properties
    |--------------------------------------------------------------------------
    */

    state: null,

    stops: [],

    selectedStop: null,

    /*
    |--------------------------------------------------------------------------
    | Initialize
    |--------------------------------------------------------------------------
    */

    init(state) {

        this.state = state;

        this.bindEvents();

        this.load();

    },

    /*
    |--------------------------------------------------------------------------
    | Bind Events
    |--------------------------------------------------------------------------
    */

    bindEvents() {

        document.getElementById(

            'reloadStopButton'

        )?.addEventListener(

            'click',

            () => {

                this.reload();

            }

        );

        document.addEventListener(

            'click',

            event => {

                const button =

                    event.target.closest(

                        '.stop-focus'

                    );

                if (!button) {

                    return;

                }

                this.focus(

                    button.dataset.id

                );

            }

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Load
    |--------------------------------------------------------------------------
    */

    async load() {

        try {

            const response =

                await VehicleApi.stop();

            if (

                !response ||

                !response.success

            ) {

                return;

            }

            this.stops =

                response.data ?? [];

            this.render();

        }

        catch (error) {

            console.error(

                '[VehicleStop]',

                error

            );

        }

    },

        /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    render() {

        this.renderSummary();

        this.renderList();

    },

    /*
    |--------------------------------------------------------------------------
    | Summary
    |--------------------------------------------------------------------------
    */

    renderSummary() {

        const stops =

            this.stops ?? [];

        this.setText(

            'stopTotal',

            stops.length

        );

        this.setText(

            'stopToday',

            stops.length

        );

        this.setText(

            'stopLongest',

            '-'

        );

        this.setText(

            'stopAverage',

            '-'

        );

    },

        /*
    |--------------------------------------------------------------------------
    | Render List
    |--------------------------------------------------------------------------
    */

    renderList() {

        const container =

            document.getElementById(

                'stopList'

            );

        const empty =

            document.getElementById(

                'stopEmpty'

            );

        if (

            !container ||

            !empty

        ) {

            return;

        }

        if (

            !this.stops.length

        ) {

            container.innerHTML = '';

            empty.classList.remove(

                'hidden'

            );

            return;

        }

        empty.classList.add(

            'hidden'

        );

        container.innerHTML =

            this.stops.map(

                stop =>

                    this.item(

                        stop

                    )

            ).join('');

    },

        /*
    |--------------------------------------------------------------------------
    | Item
    |--------------------------------------------------------------------------
    */

    item(stop) {

        return `

            <div
                class="flex items-center justify-between px-6 py-5"
            >

                <div>

                    <h4
                        class="font-semibold"
                    >

                        ${stop.address ?? '-'}

                    </h4>

                    <p
                        class="mt-1 text-sm text-slate-500"
                    >

                        ${stop.started_at ?? '-'}

                    </p>

                </div>

                <button

                    class="stop-focus rounded-lg border border-slate-300 px-4 py-2"

                    data-id="${stop.id}"

                >

                    Lihat

                </button>

            </div>

        `;

    },

        /*
    |--------------------------------------------------------------------------
    | Focus
    |--------------------------------------------------------------------------
    */

    focus(id) {

        const stop =

            this.stops.find(

                item =>

                    item.id == id

            );

        if (!stop) {

            return;

        }

        this.selectedStop = stop.id;

        Vehicle.updateLatestLocation({

            lat: stop.lat,

            lng: stop.lng,

            address: stop.address,

            received_at: stop.started_at,

        });

    },

        /*
    |--------------------------------------------------------------------------
    | Reload
    |--------------------------------------------------------------------------
    */

    async reload() {

        await this.load();

    },

    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */

    setText(id, value) {

        const element =

            document.getElementById(

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

        this.stops = [];

        this.selectedStop = null;

        this.state = null;

    }

};

</script>