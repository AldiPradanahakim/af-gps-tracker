<script>

window.VehicleSearch = {

    /*
    |--------------------------------------------------------------------------
    | Properties
    |--------------------------------------------------------------------------
    */

    state: null,

    input: null,

    resultBox: null,

    keyword: '',

    results: [],

    selectedIndex: -1,

    opened: false,

    debounceTimer: null,

    minimumKeyword: 2,

    debounceDelay: 400,

    /*
    |--------------------------------------------------------------------------
    | Initialize
    |--------------------------------------------------------------------------
    */

    init(state) {

        this.state = state;

        this.input = document.getElementById('vehicleSearch');

        this.resultBox = document.getElementById('vehicleSearchResult');

        if (!this.input || !this.resultBox) {

            return;

        }

        this.bindEvents();

    },

    /*
    |--------------------------------------------------------------------------
    | Bind Events
    |--------------------------------------------------------------------------
    */

    bindEvents() {

        this.input.addEventListener('input', () => this.handleInput());

        this.input.addEventListener('keydown', (event) => this.handleKeyboard(event));

        this.input.addEventListener('focus', () => {

            if (this.results.length) {

                this.open();

            }

        });

        this.input.addEventListener('blur', () => {

            setTimeout(() => this.close(), 150);

        });

        this.resultBox.addEventListener('click', (event) => {

            const item = event.target.closest('button');

            if (!item) {

                return;

            }

            const index = Number(item.dataset.index);

            this.select(this.results[index]);

        });

        document.addEventListener('click', (event) => {

            if (

                this.input.contains(event.target) ||

                this.resultBox.contains(event.target)

            ) {

                return;

            }

            this.close();

        });

    },

    /*
    |--------------------------------------------------------------------------
    | Debounce
    |--------------------------------------------------------------------------
    */

    handleInput() {

        clearTimeout(this.debounceTimer);

        this.debounceTimer = setTimeout(async () => {

            this.keyword = this.input.value.trim();

            if (this.keyword.length < this.minimumKeyword) {

                this.results = [];

                this.close();

                return;

            }

            this.showLoading();

            const results = await this.execute(this.keyword);

            this.results = results;

            this.render(results);

        }, this.debounceDelay);

    },

    /*
    |--------------------------------------------------------------------------
    | Execute Search
    |--------------------------------------------------------------------------
    */

    async execute(keyword) {

        try {

            let url = `/api/search?keyword=${encodeURIComponent(keyword)}`;

            const anchor = this.state?.latestLocation;

            if (anchor && anchor.lat != null && anchor.lng != null) {

                url += `&lat=${encodeURIComponent(anchor.lat)}&lng=${encodeURIComponent(anchor.lng)}`;

            }

            const response = await fetch(

                url,

                {

                    headers: {

                        'Accept': 'application/json',

                        'X-Requested-With': 'XMLHttpRequest',

                    },

                }

            );

            if (!response.ok) {

                throw new Error('Search failed.');

            }

            return await response.json();

        } catch (error) {

            console.error(error);

            this.showError();

            return [];

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Icon
    |--------------------------------------------------------------------------
    */

    getIcon(type) {

        return type === 'vehicle'
            ? '🚗'
            : '📍';

    },

    /*
    |--------------------------------------------------------------------------
    | Highlight Keyword
    |--------------------------------------------------------------------------
    */

    highlight(text = '') {

        if (!this.keyword) {

            return String(text ?? '');

        }

        const escaped = this.keyword.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

        return String(text ?? '').replace(

            new RegExp(`(${escaped})`, 'ig'),

            '<mark class="rounded bg-yellow-200 px-1">$1</mark>'

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    render(results = []) {

        this.resultBox.innerHTML = '';

        this.selectedIndex = -1;

        if (!results.length) {

            this.showEmpty();

            return;

        }

        results.forEach((result, index) => {

            const item = document.createElement('button');

            item.type = 'button';

            item.dataset.index = index;

            item.dataset.type = result.type;

            const isCurrentVehicle =
                result.type === 'vehicle' &&
                this.state.device &&
                String(result.id) === String(this.state.device.id);

            item.className =
                'flex w-full items-start gap-3 border-b border-slate-100 px-4 py-3 text-left transition hover:bg-slate-50';

            item.innerHTML = `

                <div class="mt-1 text-lg">
                    ${this.getIcon(result.type)}
                </div>

                <div class="min-w-0 flex-1">

                    <div class="flex items-center gap-2">

                        <span class="truncate font-medium text-slate-900">
                            ${this.highlight(result.title)}
                        </span>

                        ${isCurrentVehicle
                            ? '<span class="flex-shrink-0 rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-semibold text-blue-700">Sedang Dilihat</span>'
                            : ''}

                    </div>

                    <div class="mt-1 truncate text-xs text-slate-500">
                        ${result.subtitle ?? ''}
                    </div>

                </div>

            `;

            this.resultBox.appendChild(item);

        });

        this.open();

    },

    showLoading() {

        this.resultBox.innerHTML = `
            <div class="px-4 py-3 text-sm text-slate-500">
                Mencari...
            </div>
        `;

        this.open();

    },

    showEmpty() {

        this.resultBox.innerHTML = `
            <div class="px-4 py-3 text-sm text-slate-500">
                Data tidak ditemukan.
            </div>
        `;

        this.open();

    },

    showError() {

        this.resultBox.innerHTML = `
            <div class="px-4 py-3 text-sm text-red-500">
                Gagal melakukan pencarian.
            </div>
        `;

        this.open();

    },

    /*
    |--------------------------------------------------------------------------
    | Open / Close
    |--------------------------------------------------------------------------
    */

    open() {

        this.resultBox.classList.remove('hidden');

        this.opened = true;

    },

    close() {

        this.resultBox.classList.add('hidden');

        this.opened = false;

    },

    /*
    |--------------------------------------------------------------------------
    | Keyboard Navigation
    |--------------------------------------------------------------------------
    */

    handleKeyboard(event) {

        if (!this.opened) {

            return;

        }

        const items = this.resultBox.querySelectorAll('button');

        if (!items.length) {

            return;

        }

        switch (event.key) {

            case 'ArrowDown':

                event.preventDefault();

                this.moveSelection(1, items);

                break;

            case 'ArrowUp':

                event.preventDefault();

                this.moveSelection(-1, items);

                break;

            case 'Enter':

                event.preventDefault();

                if (this.selectedIndex >= 0 && items[this.selectedIndex]) {

                    items[this.selectedIndex].click();

                }

                break;

            case 'Escape':

                this.close();

                break;

        }

    },

    moveSelection(direction, items) {

        let index = this.selectedIndex + direction;

        if (index < 0) {

            index = items.length - 1;

        }

        if (index >= items.length) {

            index = 0;

        }

        items.forEach((item, current) => {

            item.classList.toggle('bg-blue-50', current === index);

        });

        this.selectedIndex = index;

    },

    /*
    |--------------------------------------------------------------------------
    | Select Result
    |--------------------------------------------------------------------------
    */

    select(result) {

        if (!result) {

            return;

        }

        this.input.value = '';

        this.close();

        switch (result.type) {

            case 'vehicle':

                this.selectVehicle(result);

                break;

            case 'location':

                VehicleMap.showTemporaryMarker(
                    result.latitude,
                    result.longitude,
                    result.title
                );

                VehicleMap.flyTo(
                    result.latitude,
                    result.longitude,
                    17
                );

                break;

        }

    },

    selectVehicle(result) {

        if (

            this.state.device &&

            String(result.id) === String(this.state.device.id)

        ) {

            VehicleMap.clearTemporary();

            VehicleMap.resetView();

            return;

        }

        window.location.href = `/vehicles/${result.id}`;

    },

};

</script>
