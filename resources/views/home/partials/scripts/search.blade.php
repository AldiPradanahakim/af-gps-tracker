<script>

document.addEventListener('gpstracker:map-ready', () => {

    /*
    |--------------------------------------------------------------------------
    | Search State
    |--------------------------------------------------------------------------
    */

    GPSTracker.search ??= {

        initialized: false,

        loading: false,

        opened: false,

        keyword: '',

        selectedIndex: -1,

        debounceTimer: null,

        results: [],

        vehicleResults: [],

        placeResults: [],

        input: null,

        resultBox: null,

    };

    /*
    |--------------------------------------------------------------------------
    | Search Configuration
    |--------------------------------------------------------------------------
    */

    GPSTracker.searchConfig = {

        inputId: 'searchVehicle',

        resultId: 'searchResult',

        minimumKeyword: 2,

        debounceDelay: 400,

        maximumVehicleResult: 5,

        maximumPlaceResult: 5,

        maximumResult: 10,

    };

    /*
    |--------------------------------------------------------------------------
    | Getter
    |--------------------------------------------------------------------------
    */

    GPSTracker.getSearchState = function () {

        return this.search;

    };

    GPSTracker.getSearchConfig = function () {

        return this.searchConfig;

    };

    GPSTracker.getSearchInput = function () {

        if (

            !this.search.input

        ) {

            this.search.input = document.getElementById(

                this.searchConfig.inputId

            );

        }

        return this.search.input;

    };

    GPSTracker.getSearchResult = function () {

        if (

            !this.search.resultBox

        ) {

            this.search.resultBox = document.getElementById(

                this.searchConfig.resultId

            );

        }

        return this.search.resultBox;

    };

    GPSTracker.getSearchKeyword = function () {

        return this.search.keyword;

    };

    GPSTracker.getSearchResults = function () {

        return this.search.results;

    };

    GPSTracker.getVehicleSearchResults = function () {

        return this.search.vehicleResults;

    };

    GPSTracker.getPlaceSearchResults = function () {

        return this.search.placeResults;

    };

    GPSTracker.getSelectedSearchIndex = function () {

        return this.search.selectedIndex;

    };

    /*
    |--------------------------------------------------------------------------
    | Setter
    |--------------------------------------------------------------------------
    */

    GPSTracker.setSearchInitialized = function (

        status = true

    ) {

        this.search.initialized = Boolean(

            status

        );

    };

    GPSTracker.setSearchLoading = function (

        status = true

    ) {

        this.search.loading = Boolean(

            status

        );

    };

    GPSTracker.setSearchOpened = function (

        status = true

    ) {

        this.search.opened = Boolean(

            status

        );

    };

    GPSTracker.setSearchKeyword = function (

        keyword = ''

    ) {

        this.search.keyword = String(

            keyword

        ).trim();

    };

    GPSTracker.setSearchResults = function (

        results = []

    ) {

        this.search.results = Array.isArray(

            results

        )

            ? results

            : [];

    };

    GPSTracker.setVehicleSearchResults = function (

        results = []

    ) {

        this.search.vehicleResults = Array.isArray(

            results

        )

            ? results

            : [];

    };

    GPSTracker.setPlaceSearchResults = function (

        results = []

    ) {

        this.search.placeResults = Array.isArray(

            results

        )

            ? results

            : [];

    };

    GPSTracker.setSelectedSearchIndex = function (

        index = -1

    ) {

        this.search.selectedIndex = Number(

            index

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Checker
    |--------------------------------------------------------------------------
    */

    GPSTracker.isSearchInitialized = function () {

        return this.search.initialized;

    };

    GPSTracker.isSearchLoading = function () {

        return this.search.loading;

    };

    GPSTracker.isSearchOpened = function () {

        return this.search.opened;

    };

    GPSTracker.hasSearchKeyword = function () {

        return (

            this.search.keyword.length >=

            this.searchConfig.minimumKeyword

        );

    };

    GPSTracker.hasSearchResults = function () {

        return (

            this.search.results.length > 0

        );

    };

    GPSTracker.hasVehicleSearchResults = function () {

        return (

            this.search.vehicleResults.length > 0

        );

    };

    GPSTracker.hasPlaceSearchResults = function () {

        return (

            this.search.placeResults.length > 0

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    */

    GPSTracker.cacheSearchElement = function () {

        this.search.input = document.getElementById(

            this.searchConfig.inputId

        );

        this.search.resultBox = document.getElementById(

            this.searchConfig.resultId

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */

    GPSTracker.clearSearch = function () {

        this.setSearchLoading(

            false

        );

        this.setSearchOpened(

            false

        );

        this.setSearchKeyword(

            ''

        );

        this.setSelectedSearchIndex(

            -1

        );

        this.setSearchResults(

            []

        );

        this.setVehicleSearchResults(

            []

        );

        this.setPlaceSearchResults(

            []

        );

        this.clearTemporary();

    };

    GPSTracker.runSearchDebounce = function (

        callback

    ) {

        clearTimeout(

            this.search.debounceTimer

        );

        this.search.debounceTimer = setTimeout(

            callback,

            this.searchConfig.debounceDelay

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Logger
    |--------------------------------------------------------------------------
    */

    GPSTracker.searchLog = function (

        ...message

    ) {

        console.log(

            '[Search]',

            ...message

        );

    };

    GPSTracker.searchWarn = function (

        ...message

    ) {

        console.warn(

            '[Search]',

            ...message

        );

    };

    GPSTracker.searchError = function (

        ...message

    ) {

        console.error(

            '[Search]',

            ...message

        );

    };
        /*
    |--------------------------------------------------------------------------
    | Vehicle Search Helper
    |--------------------------------------------------------------------------
    */

    GPSTracker.normalizeVehicleKeyword = function (

        keyword = ''

    ) {

        return String(

            keyword

        )

            .trim()

            .toLowerCase();

    };

    GPSTracker.getSearchVehicles = function () {

        if (

            typeof this.getVehicles === 'function'

        ) {

            return this.getVehicles();

        }

        return [];

    };

    GPSTracker.buildVehicleSearchResult = function (

        vehicle

    ) {

        return {

            type: 'vehicle',

            id: vehicle.device_id,

            device_id: vehicle.device_id,

            title:

                vehicle.vehicle_name ??

                vehicle.device_id,

            subtitle:

                vehicle.plate_number ??

                '-',

            latitude:

                vehicle.latitude,

            longitude:

                vehicle.longitude,

            vehicle,

        };

    };

    /*
    |--------------------------------------------------------------------------
    | Filter Vehicle
    |--------------------------------------------------------------------------
    */

    GPSTracker.filterVehicles = function (

        keyword

    ) {

        keyword = this.normalizeVehicleKeyword(

            keyword

        );

        if (

            !keyword

        ) {

            return [];

        }

        return this.getSearchVehicles().filter(

            vehicle => {

                const vehicleName = String(

                    vehicle.vehicle_name ??

                    ''

                ).toLowerCase();

                const plateNumber = String(

                    vehicle.plate_number ??

                    ''

                ).toLowerCase();

                const deviceId = String(

                    vehicle.device_id ??

                    ''

                ).toLowerCase();

                return (

                    vehicleName.includes(

                        keyword

                    ) ||

                    plateNumber.includes(

                        keyword

                    ) ||

                    deviceId.includes(

                        keyword

                    )

                );

            }

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Sort Vehicle
    |--------------------------------------------------------------------------
    */

    GPSTracker.sortVehicleSearchResult = function (

        vehicles = []

    ) {

        return [

            ...vehicles,

        ].sort(

            (

                first,

                second

            ) => {

                return String(

                    first.vehicle_name ??

                    ''

                ).localeCompare(

                    String(

                        second.vehicle_name ??

                        ''

                    )

                );

            }

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Search Vehicle
    |--------------------------------------------------------------------------
    */

    GPSTracker.searchVehicle = function (

        keyword

    ) {

        const vehicles =

            this.sortVehicleSearchResult(

                this.filterVehicles(

                    keyword

                )

            );

        const results = vehicles

            .slice(

                0,

                this.getSearchConfig()

                    .maximumVehicleResult

            )

            .map(

                vehicle =>

                    this.buildVehicleSearchResult(

                        vehicle

                    )

            );

        this.setVehicleSearchResults(

            results

        );

        return results;

    };
        /*
    |--------------------------------------------------------------------------
    | Place Search Helper
    |--------------------------------------------------------------------------
    */

    GPSTracker.normalizePlaceKeyword = function (

        keyword = ''

    ) {

        return String(

            keyword

        )

            .trim();

    };

    GPSTracker.buildPlaceSearchResult = function (

        place

    ) {

        return {

            type: 'place',

            id:

                place.place_id ??

                crypto.randomUUID(),

            title:

                place.display_name ??

                '-',

            subtitle:

                place.address ??

                '',

            latitude:

                Number(

                    place.latitude ??

                    place.lat

                ),

            longitude:

                Number(

                    place.longitude ??

                    place.lon

                ),

            place,

        };

    };

    /*
    |--------------------------------------------------------------------------
    | Search Place
    |--------------------------------------------------------------------------
    */

    GPSTracker.searchPlaces = async function (

        keyword

    ) {

        keyword = this.normalizePlaceKeyword(

            keyword

        );

        if (

            !keyword

        ) {

            this.setPlaceSearchResults(

                []

            );

            return [];

        }

        try {

            const places = await this.searchPlace(

                keyword

            );

            const results = (

                Array.isArray(

                    places

                )

                    ? places

                    : []

            )

                .slice(

                    0,

                    this.getSearchConfig()

                        .maximumPlaceResult

                )

                .map(

                    place =>

                        this.buildPlaceSearchResult(

                            place

                        )

                );

            this.setPlaceSearchResults(

                results

            );

            return results;

        }

        catch (

            error

        ) {

            this.searchError(

                error

            );

            this.setPlaceSearchResults(

                []

            );

            return [];

        }

    };

    /*
    |--------------------------------------------------------------------------
    | Merge Search Result
    |--------------------------------------------------------------------------
    */

    GPSTracker.mergeSearchResults = function () {

        const results = [

            ...this.getVehicleSearchResults(),

            ...this.getPlaceSearchResults(),

        ].slice(

            0,

            this.getSearchConfig()

                .maximumResult

        );

        this.setSearchResults(

            results

        );

        return results;

    };

    /*
    |--------------------------------------------------------------------------
    | Execute Search
    |--------------------------------------------------------------------------
    */

    GPSTracker.executeSearch = async function (keyword) {

        this.setSearchKeyword(keyword);

        if (!this.hasSearchKeyword()) {

            this.clearSearch();

            return [];

        }

        this.setSearchLoading(true);

        try {

            const response = await fetch(

                `/api/search?keyword=${encodeURIComponent(keyword)}`,

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

            const results = await response.json();

            this.setSearchResults(results);

            return results;

        }

        catch (error) {

            this.searchError(error);

            this.setSearchResults([]);

            return [];

        }

        finally {

            this.setSearchLoading(false);

        }

    };
        /*
    |--------------------------------------------------------------------------
    | Search Icon
    |--------------------------------------------------------------------------
    */

    GPSTracker.getSearchIcon = function (type) {

        return type === 'vehicle'
            ? '🚗'
            : '📍';

    }

    /*
    |--------------------------------------------------------------------------
    | Highlight Keyword
    |--------------------------------------------------------------------------
    */

    GPSTracker.highlightSearchKeyword = function (

        text = ''

    ) {

        const keyword =

            this.getSearchKeyword();

        if (

            !keyword

        ) {

            return String(

                text

            );

        }

        const escapeKeyword = keyword.replace(

            /[.*+?^${}()|[\]\\]/g,

            '\\$&'

        );

        return String(

            text

        ).replace(

            new RegExp(

                `(${escapeKeyword})`,

                'ig'

            ),

            '<mark class="rounded bg-yellow-200 px-1">$1</mark>'

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Create Search Item
    |--------------------------------------------------------------------------
    */

    GPSTracker.createSearchItem = function (

        result

    ) {

        const item =

            document.createElement(

                'button'

            );

        item.type = 'button';

        item.dataset.type =

            result.type;

        item.className =
            'flex w-full items-start gap-3 border-b border-slate-100 px-4 py-3 text-left transition hover:bg-slate-50';

        item.innerHTML = `

            <div class="mt-1 text-lg">

                ${this.getSearchIcon(

                    result.type

                )}

            </div>

            <div class="min-w-0 flex-1">

                <div class="truncate font-medium text-slate-900">

                    ${this.highlightSearchKeyword(

                        result.title

                    )}

                </div>

                <div class="mt-1 truncate text-xs text-slate-500">

                    ${result.subtitle ?? ''}

                </div>

            </div>

        `;

        item.dataset.index =

            this.getSearchResult()

                .children.length;

        return item;

    };

    /*
    |--------------------------------------------------------------------------
    | Render Result
    |--------------------------------------------------------------------------
    */

    GPSTracker.renderSearchResults = function (

        results = []

    ) {

        const resultBox =

            this.getSearchResult();

        if (

            !resultBox

        ) {

            return;

        }

        resultBox.innerHTML = '';

        this.setSelectedSearchIndex(

            -1

        );

        if (

            !results.length

        ) {

            this.showSearchEmpty();

            return;

        }

        results.forEach(

            result => {

                resultBox.appendChild(

                    this.createSearchItem(

                        result

                    )

                );

            }

        );

        this.openSearchResult();

    };
    

    /*
    |--------------------------------------------------------------------------
    | Loading
    |--------------------------------------------------------------------------
    */

    GPSTracker.showSearchLoading = function () {

        const resultBox =

            this.getSearchResult();

        if (

            !resultBox

        ) {

            return;

        }

        resultBox.innerHTML = `

            <div class="px-4 py-3 text-sm text-slate-500">

                Mencari...

            </div>

        `;

        this.openSearchResult();

    };

    /*
    |--------------------------------------------------------------------------
    | Empty
    |--------------------------------------------------------------------------
    */

    GPSTracker.showSearchEmpty = function () {

        const resultBox =

            this.getSearchResult();

        if (

            !resultBox

        ) {

            return;

        }

        resultBox.innerHTML = `

            <div class="px-4 py-3 text-sm text-slate-500">

                Data tidak ditemukan.

            </div>

        `;

        this.openSearchResult();

    };

    /*
    |--------------------------------------------------------------------------
    | Error
    |--------------------------------------------------------------------------
    */

    GPSTracker.showSearchError = function () {

        const resultBox =

            this.getSearchResult();

        if (

            !resultBox

        ) {

            return;

        }

        resultBox.innerHTML = `

            <div class="px-4 py-3 text-sm text-red-500">

                Gagal melakukan pencarian.

            </div>

        `;

        this.openSearchResult();

    };
        /*
    |--------------------------------------------------------------------------
    | Open / Close Result
    |--------------------------------------------------------------------------
    */

    GPSTracker.openSearchResult = function () {

        const resultBox = this.getSearchResult();

        if (!resultBox) {

            return;

        }

        resultBox.classList.remove(

            'hidden'

        );

        this.setSearchOpened(

            true

        );

    };

    GPSTracker.closeSearchResult = function () {

        const resultBox = this.getSearchResult();

        if (!resultBox) {

            return;

        }

        resultBox.classList.add(

            'hidden'

        );

        this.setSearchOpened(

            false

        );

        this.setSelectedSearchIndex(

            -1

        );

    };

    GPSTracker.clearSearchResult = function () {

        const resultBox =

            this.getSearchResult();

        if (!resultBox) {

            return;

        }

        resultBox.innerHTML = '';

    };

    /*
    |--------------------------------------------------------------------------
    | Select Result
    |--------------------------------------------------------------------------
    */

    GPSTracker.selectSearchResult = function (

        result

    ) {

        if (!result) {

            return;

        }

        const input =

            this.getSearchInput();

        if (input) {

            input.value =

                result.title ?? '';

        }

        this.closeSearchResult();

        switch (result.type) {

            case 'vehicle':

                this.focusVehicle(
                    result.device_id
                );

                this.clearTemporary();

                break;

            case 'location':

                this.flyToLocation(

                    result.latitude,

                    result.longitude,

                    17

                );

                this.showTemporaryMarker(

                    result.latitude,

                    result.longitude,

                    result.title

                );

                break;

        }

    };

    /*
    |--------------------------------------------------------------------------
    | Input Handler
    |--------------------------------------------------------------------------
    */

    GPSTracker.handleSearchInput = function () {

        const input =

            this.getSearchInput();

        if (!input) {

            return;

        }

        this.runSearchDebounce(

            async () => {

                this.showSearchLoading();

                const results =

                    await this.executeSearch(

                        input.value

                    );

                this.renderSearchResults(

                    results

                );

            }

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Keyboard Navigation
    |--------------------------------------------------------------------------
    */

    GPSTracker.moveSearchSelection = function (

        direction,

        items

    ) {

        let index =

            this.getSelectedSearchIndex();

        index += direction;

        if (

            index < 0

        ) {

            index =

                items.length - 1;

        }

        if (

            index >= items.length

        ) {

            index = 0;

        }

        items.forEach(

            (

                item,

                current

            ) => {

                item.classList.toggle(

                    'bg-blue-50',

                    current === index

                );

            }

        );

        this.setSelectedSearchIndex(

            index

        );

    };

    GPSTracker.selectCurrentSearchItem = function (

        items

    ) {

        const index =

            this.getSelectedSearchIndex();

        if (

            index < 0 ||

            !items[index]

        ) {

            return;

        }

        items[index].click();

    };

    GPSTracker.handleSearchKeyboard = function (

        event

    ) {

        if (

            !this.isSearchOpened()

        ) {

            return;

        }

        const items =

            this.getSearchResult()

                ?.querySelectorAll(

                    'button'

                );

        if (

            !items ||

            !items.length

        ) {

            return;

        }

        switch (

            event.key

        ) {

            case 'ArrowDown':

                event.preventDefault();

                this.moveSearchSelection(

                    1,

                    items

                );

                break;

            case 'ArrowUp':

                event.preventDefault();

                this.moveSearchSelection(

                    -1,

                    items

                );

                break;

            case 'Enter':

                event.preventDefault();

                this.selectCurrentSearchItem(

                    items

                );

                break;

            case 'Escape':

                this.closeSearchResult();

                break;

        }

    };

    /*
    |--------------------------------------------------------------------------
    | Event Binding
    |--------------------------------------------------------------------------
    */

    GPSTracker.bindSearchEvents = function () {

        const input =

            this.getSearchInput();

        const resultBox =

            this.getSearchResult();

        if (

            !input ||

            !resultBox

        ) {

            return;

        }

        input.addEventListener(

            'input',

            () => this.handleSearchInput()

        );

        input.addEventListener(

            'keydown',

            event => this.handleSearchKeyboard(

                event

            )

        );

        input.addEventListener(

            'focus',

            () => {

                if (

                    this.hasSearchResults()

                ) {

                    this.openSearchResult();

                }

            }

        );

        input.addEventListener(

            'blur',

            () => {

                setTimeout(

                    () => {

                        this.closeSearchResult();

                    },

                    150

                );

            }

        );

        resultBox.addEventListener(

            'click',

            event => {

                const item =

                    event.target.closest(

                        'button'

                    );

                if (!item) {

                    return;

                }

                const index = Number(

                    item.dataset.index

                );

                this.selectSearchResult(

                    this.getSearchResults()[

                        index

                    ]

                );

            }

        );

        document.addEventListener(

            'click',

            event => {

                if (

                    input.contains(

                        event.target

                    ) ||

                    resultBox.contains(

                        event.target

                    )

                ) {

                    return;

                }

                this.closeSearchResult();

            }

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Lifecycle
    |--------------------------------------------------------------------------
    */

    GPSTracker.initializeSearch = function () {

        if (

            this.isSearchInitialized()

        ) {

            return;

        }

        this.cacheSearchElement();

        if (

            !this.getSearchInput() ||

            !this.getSearchResult()

        ) {

            return;

        }

        this.bindSearchEvents();

        this.setSearchInitialized(

            true

        );

        this.searchLog(

            'Search initialized.'

        );

    };

    GPSTracker.destroySearch = function () {

        this.clearSearch();

        this.clearSearchResult();

        this.closeSearchResult();

        this.setSearchInitialized(

            false

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Ready
    |--------------------------------------------------------------------------
    */

    GPSTracker.initializeSearch();

    document.dispatchEvent(

        new CustomEvent(

            'gpstracker:search-ready'

        )

    );

});
</script>