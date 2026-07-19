<script>

document.addEventListener(

    'gpstracker:map-ready',

    () => {

        /*
        |--------------------------------------------------------------------------
        | State
        |--------------------------------------------------------------------------
        */

        GPSTracker.address ??= {

            initialized: false,

            cache: new Map(),

            searchCache: new Map(),

            pending: new Map(),

            temporaryMarker: null,

        };

        /*
        |--------------------------------------------------------------------------
        | Config
        |--------------------------------------------------------------------------
        */

        GPSTracker.addressConfig = {

            reverseUrl:
                'https://nominatim.openstreetmap.org/reverse',

            searchUrl:
                'https://nominatim.openstreetmap.org/search',

            format: 'jsonv2',

            language: 'id',

            zoom: 18,

            cacheSize: 1000,

            timeout: 10000,

            distanceThreshold: 30,

            updateInterval: 60000,

        };

        /*
        |--------------------------------------------------------------------------
        | Getter
        |--------------------------------------------------------------------------
        */

        GPSTracker.getAddressState = function () {

            return this.address;

        };

        GPSTracker.getAddressConfig = function () {

            return this.addressConfig;

        };

        GPSTracker.getAddressCache = function () {

            return this.address.cache;

        };

        GPSTracker.getSearchCache = function () {

            return this.address.searchCache;

        };

        GPSTracker.getPendingRequest = function (

            key

        ) {

            return this.address.pending.get(

                key

            ) ?? null;

        };

        GPSTracker.getTemporaryAddressMarker = function () {

            return this.address.temporaryMarker;

        };

        /*
        |--------------------------------------------------------------------------
        | Setter
        |--------------------------------------------------------------------------
        */

        GPSTracker.setTemporaryAddressMarker = function (

            marker

        ) {

            this.address.temporaryMarker = marker;

        };

        GPSTracker.setPendingRequest = function (

            key,

            controller

        ) {

            this.address.pending.set(

                key,

                controller

            );

        };

        GPSTracker.removePendingRequest = function (

            key

        ) {

            this.address.pending.delete(

                key

            );

        };

        GPSTracker.setAddressInitialized = function (

            status = true

        ) {

            this.address.initialized = status;

        };

        /*
        |--------------------------------------------------------------------------
        | Checker
        |--------------------------------------------------------------------------
        */

        GPSTracker.hasAddressCache = function (

            key

        ) {

            return this.address.cache.has(

                key

            );

        };

        GPSTracker.hasSearchCache = function (

            keyword

        ) {

            return this.address.searchCache.has(

                keyword

            );

        };

        GPSTracker.hasPendingRequest = function (

            key

        ) {

            return this.address.pending.has(

                key

            );

        };

        GPSTracker.hasTemporaryAddressMarker = function () {

            return this.address.temporaryMarker !== null;

        };

        GPSTracker.isAddressInitialized = function () {

            return this.address.initialized;

        };

        /*
        |--------------------------------------------------------------------------
        | Logger
        |--------------------------------------------------------------------------
        */

        GPSTracker.addressLog = function (

            ...message

        ) {

            console.log(

                '[Address]',

                ...message

            );

        };

        GPSTracker.addressWarn = function (

            ...message

        ) {

            console.warn(

                '[Address]',

                ...message

            );

        };

        GPSTracker.addressError = function (

            ...message

        ) {

            console.error(

                '[Address]',

                ...message

            );

        };
                /*
        |--------------------------------------------------------------------------
        | Cache Helper
        |--------------------------------------------------------------------------
        */

        GPSTracker.createAddressKey = function (

            latitude,

            longitude

        ) {

            return [

                Number(latitude).toFixed(6),

                Number(longitude).toFixed(6),

            ].join(',');

        };

        GPSTracker.setAddressCache = function (

            key,

            value

        ) {

            if (

                this.getAddressCache().size >=

                this.getAddressConfig().cacheSize

            ) {

                const firstKey =

                    this.getAddressCache()

                        .keys()

                        .next()

                        .value;

                this.getAddressCache()

                    .delete(

                        firstKey

                    );

            }

            this.getAddressCache().set(

                key,

                value

            );

        };

        GPSTracker.getCachedAddress = function (

            key

        ) {

            return this.getAddressCache().get(

                key

            ) ?? null;

        };

        GPSTracker.removeAddressCache = function (

            key

        ) {

            this.getAddressCache().delete(

                key

            );

        };

        GPSTracker.clearAddressCache = function () {

            this.getAddressCache().clear();

        };

        /*
        |--------------------------------------------------------------------------
        | Search Cache
        |--------------------------------------------------------------------------
        */

        GPSTracker.setSearchCache = function (

            keyword,

            result

        ) {

            this.getSearchCache().set(

                keyword.toLowerCase(),

                result

            );

        };

        GPSTracker.getCachedSearch = function (

            keyword

        ) {

            return this.getSearchCache().get(

                keyword.toLowerCase()

            ) ?? null;

        };

        GPSTracker.removeSearchCache = function (

            keyword

        ) {

            this.getSearchCache().delete(

                keyword.toLowerCase()

            );

        };

        GPSTracker.clearSearchCache = function () {

            this.getSearchCache().clear();

        };

        /*
        |--------------------------------------------------------------------------
        | Request Helper
        |--------------------------------------------------------------------------
        */

        GPSTracker.abortRequest = function (

            key

        ) {

            const controller =

                this.getPendingRequest(

                    key

                );

            if (

                controller

            ) {

                controller.abort();

            }

            this.removePendingRequest(

                key

            );

        };

        GPSTracker.abortAllRequests = function () {

            this.address.pending.forEach(

                controller => {

                    controller.abort();

                }

            );

            this.address.pending.clear();

        };

        GPSTracker.createRequest = function (

            key

        ) {

            this.abortRequest(

                key

            );

            const controller =

                new AbortController();

            this.setPendingRequest(

                key,

                controller

            );

            return controller;

        };

        /*
        |--------------------------------------------------------------------------
        | Helper
        |--------------------------------------------------------------------------
        */

        GPSTracker.normalizeKeyword = function (

            keyword

        ) {

            return String(

                keyword ?? ''

            )

                .trim()

                .toLowerCase();

        };

        GPSTracker.normalizeCoordinate = function (

            latitude,

            longitude

        ) {

            return {

                latitude: Number(

                    latitude

                ),

                longitude: Number(

                    longitude

                ),

            };

        };

        GPSTracker.isValidKeyword = function (

            keyword

        ) {

            return this.normalizeKeyword(

                keyword

            ).length >= 2;

        };
              /*
        |--------------------------------------------------------------------------
        | Reverse Geocoding
        |--------------------------------------------------------------------------
        */

        GPSTracker.reverseGeocode = async function (

            latitude,

            longitude

        ) {

            const coordinate = this.normalizeCoordinate(

                latitude,

                longitude

            );

            const key = this.createAddressKey(

                coordinate.latitude,

                coordinate.longitude

            );

            if (

                this.hasAddressCache(

                    key

                )

            ) {

                return this.getCachedAddress(

                    key

                );

            }

            const controller = this.createRequest(

                key

            );

            try {

                const url = new URL(

                    this.getAddressConfig().reverseUrl

                );

                url.searchParams.set(

                    'format',

                    this.getAddressConfig().format

                );

                url.searchParams.set(

                    'lat',

                    coordinate.latitude

                );

                url.searchParams.set(

                    'lon',

                    coordinate.longitude

                );

                url.searchParams.set(

                    'accept-language',

                    this.getAddressConfig().language

                );

                const response = await fetch(

                    url,

                    {

                        signal: controller.signal,

                        headers: {

                            'Accept': 'application/json',

                        },

                    }

                );

                if (

                    !response.ok

                ) {

                    throw new Error(

                        'Reverse geocoding failed.'

                    );

                }

                const json = await response.json();

                const result = {

                    latitude:

                        Number(

                            json.lat

                        ),

                    longitude:

                        Number(

                            json.lon

                        ),

                    displayName:

                        json.display_name ?? '-',

                    address:

                        json.address ?? {},

                };

                this.setAddressCache(

                    key,

                    result

                );

                return result;

            }

            catch (

                error

            ) {

                if (

                    error.name === 'AbortError'

                ) {

                    return null;

                }

                this.addressError(

                    error

                );

                return null;

            }

            finally {

                this.removePendingRequest(

                    key

                );

            }

        };

        /*
        |--------------------------------------------------------------------------
        | Search Place
        |--------------------------------------------------------------------------
        */

        GPSTracker.searchPlace = async function (

            keyword

        ) {

            keyword = this.normalizeKeyword(

                keyword

            );

            if (

                !this.isValidKeyword(

                    keyword

                )

            ) {

                return [];

            }

            if (

                this.hasSearchCache(

                    keyword

                )

            ) {

                return this.getCachedSearch(

                    keyword

                );

            }

            const controller = this.createRequest(

                'search'

            );

            try {

                const url = new URL(

                    this.getAddressConfig().searchUrl

                );

                url.searchParams.set(

                    'format',

                    this.getAddressConfig().format

                );

                url.searchParams.set(

                    'q',

                    keyword

                );

                url.searchParams.set(

                    'limit',

                    10

                );

                url.searchParams.set(

                    'accept-language',

                    this.getAddressConfig().language

                );

                const response = await fetch(

                    url,

                    {

                        signal: controller.signal,

                        headers: {

                            'Accept': 'application/json',

                        },

                    }

                );

                if (

                    !response.ok

                ) {

                    throw new Error(

                        'Place search failed.'

                    );

                }

                const json = await response.json();

                const result = json.map(

                    place => {

                        return {

                            id:

                                place.place_id,

                            latitude:

                                Number(

                                    place.lat

                                ),

                            longitude:

                                Number(

                                    place.lon

                                ),

                            name:

                                place.display_name,

                            type:

                                place.type,

                            class:

                                place.class,

                            address:

                                place.address ?? {},

                        };

                    }

                );

                this.setSearchCache(

                    keyword,

                    result

                );

                return result;

            }

            catch (

                error

            ) {

                if (

                    error.name === 'AbortError'

                ) {

                    return [];

                }

                this.addressError(

                    error

                );

                return [];

            }

            finally {

                this.removePendingRequest(

                    'search'

                );

            }

        };

        /*
        |--------------------------------------------------------------------------
        | Address Helper
        |--------------------------------------------------------------------------
        */

        GPSTracker.getDisplayAddress = function (

            result

        ) {

            return result?.displayName ?? '-';

        };

        GPSTracker.getAddressObject = function (

            result

        ) {

            return result?.address ?? {};

        };

        GPSTracker.getProvince = function (

            result

        ) {

            return this.getAddressObject(

                result

            ).state ?? '-';

        };

        GPSTracker.getCity = function (

            result

        ) {

            const address = this.getAddressObject(

                result

            );

            return (

                address.city ??

                address.county ??

                address.municipality ??

                '-'

            );

        };

        GPSTracker.getDistrict = function (

            result

        ) {

            return this.getAddressObject(

                result

            ).suburb ??

            this.getAddressObject(

                result

            ).city_district ??

            '-';

        };
                /*
        |--------------------------------------------------------------------------
        | Temporary Marker
        |--------------------------------------------------------------------------
        */

        GPSTracker.showTemporaryAddressMarker = function (

            latitude,

            longitude,

            title = ''

        ) {

            this.removeTemporaryAddressMarker();

            const marker = L.marker(

                [

                    Number(latitude),

                    Number(longitude),

                ]

            );

            if (

                title

            ) {

                marker.bindPopup(

                    title

                );

            }

            marker.addTo(

                this.temporaryLayer

            );

            this.setTemporaryAddressMarker(

                marker

            );

            return marker;

        };

        GPSTracker.removeTemporaryAddressMarker = function () {

            const marker = this.getTemporaryAddressMarker();

            if (

                !marker

            ) {

                return;

            }

            this.temporaryLayer.removeLayer(

                marker

            );

            this.setTemporaryAddressMarker(

                null

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Map Helper
        |--------------------------------------------------------------------------
        */

        GPSTracker.focusAddress = function (

            latitude,

            longitude,

            zoom = null

        ) {

            this.flyToLocation(

                latitude,

                longitude,

                zoom ??

                this.getAddressConfig().zoom

            );

        };

        GPSTracker.previewAddress = function (

            address

        ) {

            if (

                !address

            ) {

                return;

            }

            this.showTemporaryAddressMarker(

                address.latitude,

                address.longitude,

                this.getDisplayAddress(

                    address

                )

            );

            this.focusAddress(

                address.latitude,

                address.longitude

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Vehicle Address State
        |--------------------------------------------------------------------------
        */

        GPSTracker.addressVehicleState ??= new Map();

        GPSTracker.getVehicleAddressState = function (

            deviceId

        ) {

            return this.addressVehicleState.get(

                String(

                    deviceId

                )

            ) ?? null;

        };

        GPSTracker.setVehicleAddressState = function (

            deviceId,

            state

        ) {

            this.addressVehicleState.set(

                String(

                    deviceId

                ),

                state

            );

        };

        GPSTracker.removeVehicleAddressState = function (

            deviceId

        ) {

            this.addressVehicleState.delete(

                String(

                    deviceId

                )

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Vehicle Address
        |--------------------------------------------------------------------------
        */

        GPSTracker.updateVehicleAddress = async function (

            vehicle

        ) {

            if (

                !vehicle

            ) {

                return null;

            }

            const address = await this.reverseGeocode(

                vehicle.latitude,

                vehicle.longitude

            );

            if (

                !address

            ) {

                return null;

            }

            this.setVehicleAddressState(

                vehicle.device_id,

                {

                    latitude:

                        Number(

                            vehicle.latitude

                        ),

                    longitude:

                        Number(

                            vehicle.longitude

                        ),

                    updatedAt:

                        Date.now(),

                    address,

                }

            );

            return address;

        };

        /*
        |--------------------------------------------------------------------------
        | Refresh
        |--------------------------------------------------------------------------
        */

        GPSTracker.refreshVehicleAddress = async function (

            vehicle

        ) {

            return await this.updateVehicleAddress(

                vehicle

            );

        };
                /*
        |--------------------------------------------------------------------------
        | Address Synchronization
        |--------------------------------------------------------------------------
        */

        GPSTracker.shouldUpdateVehicleAddress = function (

            vehicle

        ) {

            const state = this.getVehicleAddressState(

                vehicle.device_id

            );

            if (

                !state

            ) {

                return true;

            }

            const previous = L.latLng(

                state.latitude,

                state.longitude

            );

            const current = L.latLng(

                Number(

                    vehicle.latitude

                ),

                Number(

                    vehicle.longitude

                )

            );

            const distance = previous.distanceTo(

                current

            );

            const elapsed =

                Date.now() -

                state.updatedAt;

            return (

                distance >=

                this.getAddressConfig()

                    .distanceThreshold ||

                elapsed >=

                this.getAddressConfig()

                    .updateInterval

            );

        };

        GPSTracker.syncVehicleAddress = async function (

            vehicle

        ) {

            if (

                !this.shouldUpdateVehicleAddress(

                    vehicle

                )

            ) {

                return;

            }

            const address = await this.refreshVehicleAddress(

                vehicle

            );

            if (

                !address

            ) {

                return;

            }

            document.dispatchEvent(

                new CustomEvent(

                    'gpstracker:address-updated',

                    {

                        detail: {

                            vehicle,

                            address,

                        },

                    }

                )

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Initialize
        |--------------------------------------------------------------------------
        */

        GPSTracker.initializeAddress = function () {

            if (

                this.isAddressInitialized()

            ) {

                return;

            }

            this.setAddressInitialized(

                true

            );

            this.addressLog(

                'Address initialized.'

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Reset
        |--------------------------------------------------------------------------
        */

        GPSTracker.resetAddress = function () {

            this.abortAllRequests();

            this.removeTemporaryAddressMarker();

            this.clearAddressCache();

            this.clearSearchCache();

            this.addressVehicleState.clear();

            this.setAddressInitialized(

                false

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Destroy
        |--------------------------------------------------------------------------
        */

        GPSTracker.destroyAddress = function () {

            this.resetAddress();

        };

        /*
        |--------------------------------------------------------------------------
        | Handler
        |--------------------------------------------------------------------------
        */

        GPSTracker.handleVehicleAddressUpdate = async function (

            vehicle

        ) {

            if (

                !vehicle

            ) {

                return;

            }

            await this.syncVehicleAddress(

                vehicle

            );

        };

        /*
        |--------------------------------------------------------------------------
        | Event
        |--------------------------------------------------------------------------
        */

        document.addEventListener(

            'gpstracker:vehicle-updated',

            event => {

                GPSTracker.handleVehicleAddressUpdate(

                    event.detail?.vehicle

                );

            }

        );

        document.addEventListener(

            'gpstracker:vehicle-removed',

            event => {

                const deviceId = event.detail?.device_id;

                if (

                    !deviceId

                ) {

                    return;

                }

                GPSTracker.removeVehicleAddressState(

                    deviceId

                );

            }

        );

        document.addEventListener(

            'gpstracker:clear-temporary-marker',

            () => {

                GPSTracker.removeTemporaryAddressMarker();

            }

        );

        /*
        |--------------------------------------------------------------------------
        | Ready
        |--------------------------------------------------------------------------
        */

        GPSTracker.initializeAddress();

        document.dispatchEvent(

            new CustomEvent(

                'gpstracker:address-ready'

            )

        );

    }

);

</script>