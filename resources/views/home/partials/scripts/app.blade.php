<script>

window.GPSTracker = {

    /*
    |--------------------------------------------------------------------------
    | Leaflet Instance
    |--------------------------------------------------------------------------
    */

    map: null,

    markerLayer: null,

    radiusLayer: null,

    administrativeLayer: null,

    routeLayer: null,

    temporaryLayer: null,

    /*
    |--------------------------------------------------------------------------
    | Collections
    |--------------------------------------------------------------------------
    */

    vehicles: window.Home.vehicles ?? [],

    geofences: window.Home.geofences ?? [],

    notifications: window.Home.notifications ?? [],

    markers: {},

    /*
    |--------------------------------------------------------------------------
    | State
    |--------------------------------------------------------------------------
    */

    selectedVehicle: null,

    selectedLocation: null,

    searchKeyword: '',

    radiusVisible: true,

    administrativeVisible: true,

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    searchResults: [],

    searching: false,

    /*
    |--------------------------------------------------------------------------
    | Geofence
    |--------------------------------------------------------------------------
    */

    currentGeofenceType: 'radius',

    currentRadius: 500,

    /*
    |--------------------------------------------------------------------------
    | Popup
    |--------------------------------------------------------------------------
    */

    popupTemplate(vehicle){

        return `

            <div class="space-y-3">

                <div>

                    <div class="text-base font-bold">

                        ${vehicle.vehicle_name ?? '-'}

                    </div>

                    <div class="text-sm text-slate-500">

                        ${vehicle.plate_number ?? '-'}

                    </div>

                </div>

                <div class="grid grid-cols-2 gap-3 text-sm">

                    <div>

                        <div class="text-slate-400">

                            Status

                        </div>

                        <div class="font-semibold">

                            ${vehicle.is_active ? 'Online' : 'Offline'}

                        </div>

                    </div>

                    <div>

                        <div class="text-slate-400">

                            Kecepatan

                        </div>

                        <div class="font-semibold">

                            ${vehicle.speed ?? 0} km/j

                        </div>

                    </div>

                </div>

            </div>

        `;

    },

    /*
    |--------------------------------------------------------------------------
    | Focus Vehicle
    |--------------------------------------------------------------------------
    */

    focusVehicle(deviceId){

        const marker = this.markers[deviceId];

        if(!marker){

            return;

        }

        this.selectedVehicle = deviceId;

        this.map.flyTo(

            marker.getLatLng(),

            17,

            {

                animate:true,

                duration:1.2,

            }

        );

        marker.openPopup();

    },

    /*
    |--------------------------------------------------------------------------
    | Get Vehicle
    |--------------------------------------------------------------------------
    */

    getVehicle(deviceId){

        return this.vehicles.find(

            vehicle => vehicle.device_id == deviceId

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Clear Temporary Layer
    |--------------------------------------------------------------------------
    */

    clearTemporary(){

        if(!this.temporaryLayer){

            return;

        }

        this.temporaryLayer.clearLayers();

    }

};

window.focusVehicle = function(deviceId){

    GPSTracker.focusVehicle(deviceId);

};

document.addEventListener('DOMContentLoaded',()=>{

    const loading = document.getElementById('mapLoading');

    if(loading){

        loading.classList.remove('hidden');

    }

});

window.addEventListener('load',()=>{

    const loading = document.getElementById('mapLoading');

    if(!loading){

        return;

    }

    setTimeout(()=>{

        loading.classList.add('hidden');

    },400);

});

</script>