<script>

window.VehicleHistory = {

    /*
    |--------------------------------------------------------------------------
    | Properties
    |--------------------------------------------------------------------------
    */

    state: null,

    histories: [],

    activeHistory: null,

    selectedDate: null,

    /*
    |--------------------------------------------------------------------------
    | Initialize
    |--------------------------------------------------------------------------
    */

    init(state) {

        this.state = state;

        this.selectedDate = this.today();

        this.setDateInput();

        this.bindEvents();

        this.load();

        document.addEventListener(

            'vehicle.location.updated',

            () => {

                this.renderSummary();

            }

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Bind Events
    |--------------------------------------------------------------------------
    */

    bindEvents() {

        document.getElementById(

            'historyLoadButton'

        )?.addEventListener(

            'click',

            () => {

                this.selectedDate =

                    document.getElementById(

                        'historyDate'

                    ).value;

                this.load();

            }

        );

        document.getElementById(

            'historyTodayButton'

        )?.addEventListener(

            'click',

            () => {

                this.selectedDate =

                    this.today();

                this.setDateInput();

                this.load();

            }

        );

        document.getElementById(

            'historyPlaybackButton'

        )?.addEventListener(

            'click',

            () => {

                VehiclePlayback.load(

                    this.histories

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

            const response = await VehicleApi.history(

                this.selectedDate

            );

            if (

                !response ||

                !response.success

            ) {

                this.histories = [];

                this.render();

                return;

            }

            this.histories =

                response.data ?? [];

            this.render();

        }

        catch (error) {

            console.error(

                '[VehicleHistory]',

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

        this.renderTimeline();

    },

    /*
    |--------------------------------------------------------------------------
    | Render Summary
    |--------------------------------------------------------------------------
    */

    renderSummary() {

        let maxSpeed = 0;

        this.histories.forEach(history => {

            const speed = Number(

                history.speed ?? 0

            );

            if (speed > maxSpeed) {

                maxSpeed = speed;

            }

        });

        this.setText(

            'historyTotalPoint',

            this.histories.length

        );

        this.setText(

            'historyMaxSpeed',

            `${maxSpeed.toFixed(0)} km/jam`

        );

        this.setText(

            'historyTotalDistance',

            '-'

        );

    },

        /*
    |--------------------------------------------------------------------------
    | Timeline
    |--------------------------------------------------------------------------
    */

    renderTimeline() {

        const container = document.getElementById(

            'historyTimeline'

        );

        if (!container) {

            return;

        }

        if (

            !this.histories.length

        ) {

            container.innerHTML = `

                <div
                    class="py-10 text-center text-slate-500"
                >

                    Belum ada histori.

                </div>

            `;

            return;

        }

        container.innerHTML = this.histories.map(

            history => this.timelineItem(

                history

            )

        ).join('');

        this.bindTimeline();

    },

        /*
    |--------------------------------------------------------------------------
    | Timeline Item
    |--------------------------------------------------------------------------
    */

    timelineItem(history) {

        return `

            <button

                type="button"

                class="block w-full border-b border-slate-100 px-6 py-4 text-left hover:bg-slate-50"

                data-id="${history.id}"

            >

                <div class="flex items-center justify-between">

                    <div>

                        <h4 class="font-semibold">

                            ${history.address ?? '-'}

                        </h4>

                        <p class="mt-1 text-sm text-slate-500">

                            ${history.received_at}

                        </p>

                    </div>

                    <div class="text-right">

                        <div class="font-semibold">

                            ${Number(

                                history.speed ?? 0

                            ).toFixed(0)}

                            km/jam

                        </div>

                    </div>

                </div>

            </button>

        `;

    },

        /*
    |--------------------------------------------------------------------------
    | Bind Timeline
    |--------------------------------------------------------------------------
    */

    bindTimeline() {

        document.querySelectorAll(

            '#historyTimeline button'

        ).forEach(

            button => {

                button.addEventListener(

                    'click',

                    () => {

                        const history = this.histories.find(

                            item =>

                                item.id ==

                                button.dataset.id

                        );

                        if (!history) {

                            return;

                        }

                        this.activeHistory = history.id;

                        this.highlight();

                        this.state.latestLocation = {

                            ...history

                        };

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

                    }

                );

            }

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Highlight
    |--------------------------------------------------------------------------
    */

    highlight() {

        document.querySelectorAll(

            '#historyTimeline button'

        ).forEach(

            button => {

                button.classList.remove(

                    'bg-blue-50',

                    'border-l-4',

                    'border-blue-500'

                );

                if (

                    Number(button.dataset.id) ===

                    this.activeHistory

                ) {

                    button.classList.add(

                        'bg-blue-50',

                        'border-l-4',

                        'border-blue-500'

                    );

                }

            }

        );

    },

        /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    setDateInput() {

        const input = document.getElementById(

            'historyDate'

        );

        if (input) {

            input.value = this.selectedDate;

        }

    },

    today() {

        return new Date()

            .toISOString()

            .split('T')[0];

    },

    setText(id, value) {

        const element = document.getElementById(

            id

        );

        if (element) {

            element.textContent = value;

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    destroy() {

        this.histories = [];

        this.activeHistory = null;

    },
};

</script>