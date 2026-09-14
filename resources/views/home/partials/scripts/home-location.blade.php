<script>

document.addEventListener('DOMContentLoaded', () => {

    /*
    |--------------------------------------------------------------------------
    | GPSTracker Extensions for Home Location
    |--------------------------------------------------------------------------
    */
    if (window.GPSTracker) {
        
        GPSTracker.previewHomeLocation = function(lat, lng) {
            if (!this.map) return;
            
            if (this.temporaryLayer) {
                this.temporaryLayer.clearLayers();
            }
            
            const icon = L.divIcon({
                className: "",
                iconSize: [40, 40],
                iconAnchor: [20, 40],
                html: `
                    <div style="width:40px; height:40px; display:flex; justify-content:center; align-items:center;">
                        <div style="width:32px; height:32px; border-radius:50%; background:#f59e0b; border:3px solid white; box-shadow:0 4px 10px rgba(0,0,0,0.3); display:flex; justify-content:center; align-items:center;">
                            <i class="fa-solid fa-house" style="font-size:14px; color:white;"></i>
                        </div>
                    </div>
                `
            });
            
            const marker = L.marker([lat, lng], {
                icon: icon,
                draggable: true,
                zIndexOffset: 1000
            });
            
            marker.on('dragend', function(e) {
                const position = marker.getLatLng();
                document.dispatchEvent(new CustomEvent('gpstracker:home-preview-moved', {
                    detail: {
                        lat: position.lat,
                        lng: position.lng
                    }
                }));
            });
            
            marker.addTo(this.temporaryLayer);
            this.map.setView([lat, lng], 18, { animate: true });
        };

        GPSTracker.saveHomeLocationSuccess = function(data) {
            if (this.temporaryLayer) {
                this.temporaryLayer.clearLayers();
            }
            this.renderHomeLocations();
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        };

        GPSTracker.renderHomeLocations = function() {
            if (!this.homeLayer) return;
            
            this.homeLayer.clearLayers();
            
            const locations = window.GPSHomeLocations || [];
            
            locations.forEach(device => {
                if (device.home_location && device.home_location.lat && device.home_location.lng) {
                    const lat = device.home_location.lat;
                    const lng = device.home_location.lng;
                    
                    const icon = L.divIcon({
                        className: "",
                        iconSize: [40, 40],
                        iconAnchor: [20, 40],
                        popupAnchor: [0, -40],
                        html: `
                            <div style="width:40px; height:40px; display:flex; justify-content:center; align-items:center;">
                                <div style="width:32px; height:32px; border-radius:50%; background:#2563eb; border:3px solid white; box-shadow:0 4px 10px rgba(0,0,0,0.3); display:flex; justify-content:center; align-items:center;">
                                    <i class="fa-solid fa-house" style="font-size:14px; color:white;"></i>
                                </div>
                            </div>
                        `
                    });
                    
                    const marker = L.marker([lat, lng], {
                        icon: icon
                    });
                    
                    const title = device.vehicle_name || device.device_id;
                    const address = device.home_location.display_name || 'Lokasi Rumah';

                    /*
                    | Halaman Home hanya bisa menambah Home Location.
                    | Edit/Hapus hanya tersedia di halaman Detail Kendaraan.
                    */
                    marker.bindPopup(`
                        <div class="p-2 min-w-[200px]">
                            <div class="font-bold text-slate-800 mb-1 border-b pb-1">${title}</div>
                            <div class="text-xs text-slate-500">${address}</div>
                        </div>
                    `);

                    marker.addTo(this.homeLayer);
                }
            });
        };

    }

    document.addEventListener('gpstracker:map-ready', () => {
        if (window.GPSTracker && typeof window.GPSTracker.renderHomeLocations === 'function') {
            window.GPSTracker.renderHomeLocations();
        }
    });

    /*
    |--------------------------------------------------------------------------
    | Elements
    |--------------------------------------------------------------------------
    */

    const button    = document.getElementById('homeLocationButton');
    const modal     = document.getElementById('homeLocationModal');
    const close     = document.getElementById('closeHomeLocation');
    const cancel    = document.getElementById('cancelHomeLocation');
    const save      = document.getElementById('saveHomeLocation');
    const search    = document.getElementById('homeSearch');
    const result    = document.getElementById('homeSearchResult');
    const loading   = document.getElementById('homeSearchLoading');
    const coordinate = document.getElementById('homeCoordinate');
    const latitude  = document.getElementById('homeLatitude');
    const longitude = document.getElementById('homeLongitude');
    const latitudePreview  = document.getElementById('homeLatitudePreview');
    const longitudePreview = document.getElementById('homeLongitudePreview');
    const displayName = document.getElementById('homeDisplayName');

    let debounce = null;

    /*
    |--------------------------------------------------------------------------
    | Cek apakah tombol Home aktif
    | (tombol disabled jika semua device sudah punya home location)
    |--------------------------------------------------------------------------
    */

    function refreshHomeButtonState() {

        if (!button) {
            return;
        }

        const locations = window.GPSHomeLocations ?? [];

        const anyAvailable = locations.some(d => !d.home_location);

        if (anyAvailable) {

            button.disabled = false;

            button.classList.remove(
                'opacity-50', 'cursor-not-allowed'
            );

            button.title = 'Tambah Lokasi Rumah';

        } else {

            button.disabled = true;

            button.classList.add(
                'opacity-50', 'cursor-not-allowed'
            );

            button.title = 'Semua kendaraan sudah memiliki Lokasi Rumah';

        }

    }

    refreshHomeButtonState();

    /*
    |--------------------------------------------------------------------------
    | Open / Close Modal
    |--------------------------------------------------------------------------
    */

    function openModal() {

        modal.classList.remove('hidden');

        modal.classList.add('flex');

    }

    function closeModal() {

        modal.classList.remove('flex');

        modal.classList.add('hidden');

        resetForm();

    }

    function resetForm() {

        if (search) search.value = '';

        if (result) {
            result.innerHTML = '';
            result.classList.add('hidden');
        }

        if (coordinate) coordinate.textContent = 'Belum memilih lokasi.';

        if (latitude)  latitude.value  = '';
        if (longitude) longitude.value = '';
        if (displayName) displayName.value = '';
        if (latitudePreview)  latitudePreview.value  = '';
        if (longitudePreview) longitudePreview.value = '';

    }

    button?.addEventListener('click', () => {

        if (button.disabled) {
            return;
        }

        openModal();

    });

    close?.addEventListener('click', closeModal);

    cancel?.addEventListener('click', closeModal);

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    search?.addEventListener('input', () => {

        clearTimeout(debounce);

        const keyword = search.value.trim();

        if (keyword.length < 3) {

            if (result) {
                result.innerHTML = '';
                result.classList.add('hidden');
            }

            return;
        }

        debounce = setTimeout(() => {

            searchLocation(keyword);

        }, 500);

    });

    async function searchLocation(keyword) {

        loading?.classList.remove('hidden');

        try {

            const response = await fetch(
                `/api/location/search?q=${encodeURIComponent(keyword)}`
            );

            if (!response.ok) {
                throw new Error('Search gagal.');
            }

            const items = await response.json();

            renderResult(items);

        } catch (error) {

            console.error(error);

            if (result) {
                result.innerHTML = `
                    <div class="p-4 text-sm text-red-600">
                        Gagal mencari lokasi.
                    </div>
                `;
                result.classList.remove('hidden');
            }

        } finally {

            loading?.classList.add('hidden');

        }

    }

    function renderResult(items) {

        if (!result) return;

        result.innerHTML = '';

        if (!Array.isArray(items) || items.length === 0) {

            result.innerHTML = `
                <div class="p-4 text-sm text-slate-500">
                    Lokasi tidak ditemukan.
                </div>
            `;

            result.classList.remove('hidden');

            return;
        }

        items.forEach(item => {

            const row = document.createElement('button');

            row.type = 'button';

            row.className =
                'block w-full border-b border-slate-100 p-4 text-left transition hover:bg-blue-50 last:border-0';

            row.innerHTML = `
                <div class="font-semibold text-slate-800">
                    ${item.display_name}
                </div>
                <div class="mt-1 text-xs text-slate-500">
                    ${parseFloat(item.lat).toFixed(6)},
                    ${parseFloat(item.lon).toFixed(6)}
                </div>
            `;

            row.addEventListener('click', () => {

                selectLocation(item);

            });

            result.appendChild(row);

        });

        result.classList.remove('hidden');

    }

    async function selectLocation(item) {

        if (latitude)  latitude.value  = item.lat;
        if (longitude) longitude.value = item.lon;
        if (displayName) displayName.value = item.display_name;
        if (latitudePreview)  latitudePreview.value  = Number(item.lat).toFixed(6);
        if (longitudePreview) longitudePreview.value = Number(item.lon).toFixed(6);
        if (coordinate) coordinate.textContent = item.display_name;
        if (result) result.classList.add('hidden');
        if (search) search.value = item.display_name;

        try {

            await reverseGeocode(item.lat, item.lon);

        } catch (error) {

            console.error(error);

        }

        if (
            window.GPSTracker &&
            typeof GPSTracker.previewHomeLocation === 'function'
        ) {

            GPSTracker.previewHomeLocation(
                Number(item.lat),
                Number(item.lon)
            );

            GPSTracker.showToast(
                'info',
                'Lokasi Rumah',
                'Geser marker pada peta jika ingin menyesuaikan posisi.'
            );

        }

    }

    /*
    |--------------------------------------------------------------------------
    | Reverse Geocoding
    |--------------------------------------------------------------------------
    */

    async function reverseGeocode(lat, lon) {

        try {

            /*
            | Endpoint menggunakan 'latitude' dan 'longitude' sebagai param
            | (sesuai LocationController::reverse)
            */
            const response = await fetch(
                `/api/location/reverse?latitude=${lat}&longitude=${lon}`
            );

            if (!response.ok) {
                return;
            }

            const location = await response.json();

            if (location && location.display_name) {

                if (coordinate) coordinate.textContent = location.display_name;
                if (displayName) displayName.value     = location.display_name;

            }

        } catch (error) {

            console.error(error);

        }

    }

    /*
    |--------------------------------------------------------------------------
    | Save Home Location
    |--------------------------------------------------------------------------
    */

    async function saveHomeLocation() {

        const deviceEl = document.getElementById('homeDevice');

        if (!deviceEl) {

            GPSTracker.showToast(
                'warning',
                'Peringatan',
                'Tidak ada kendaraan yang tersedia untuk ditambahkan Lokasi Rumah.'
            );

            return;
        }

        if (!latitude?.value || !longitude?.value) {

            GPSTracker.showToast(
                'warning',
                'Peringatan',
                'Silakan pilih lokasi terlebih dahulu.'
            );

            return;

        }

        try {

            if (save) {
                save.disabled = true;
                save.innerHTML = 'Menyimpan...';
            }

            const response = await fetch(
                '/api/home-location',
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document
                            .querySelector('meta[name="csrf-token"]')
                            .content
                    },
                    body: JSON.stringify({

                        device_id:    deviceEl.value,

                        latitude:     latitude.value,

                        longitude:    longitude.value,

                        display_name: displayName?.value || coordinate?.textContent

                    })
                }
            );

            const data = await response.json();

            if (!response.ok) {

                let msg = data.message ?? 'Lokasi Rumah gagal disimpan.';

                if (data.errors) {
                    msg = Object.values(data.errors).flat().join('\n');
                }

                GPSTracker.showToast('error', 'Gagal', msg);

                return;

            }

            /*
            |--------------------------------------------------------------
            | Update GPSHomeLocations state — tandai device sudah punya HL
            | (bisa 1 device atau banyak device sekaligus jika "Semua
            | Kendaraan" yang dipilih)
            |--------------------------------------------------------------
            */

            if (window.GPSHomeLocations && Array.isArray(data.devices)) {

                const updated = new Map(
                    data.devices.map(device => [device.id, device.home_location])
                );

                window.GPSHomeLocations = window.GPSHomeLocations.map(d => {

                    if (updated.has(d.id)) {

                        return {
                            ...d,
                            home_location: updated.get(d.id)
                        };

                    }

                    return d;

                });

            }

            /*
            |--------------------------------------------------------------
            | Update Home Marker di peta
            |--------------------------------------------------------------
            */

            if (
                window.GPSTracker &&
                typeof GPSTracker.saveHomeLocationSuccess === 'function'
            ) {

                GPSTracker.saveHomeLocationSuccess(data);

            }

            /*
            |--------------------------------------------------------------
            | Refresh tombol Home
            |--------------------------------------------------------------
            */

            refreshHomeButtonState();

            GPSTracker.showToast(
                'success',
                'Berhasil',
                data.message ?? 'Lokasi Rumah berhasil disimpan.'
            );

            closeModal();

        } catch (error) {

            console.error(error);

            GPSTracker.showToast(
                'error',
                'Error',
                'Terjadi kesalahan pada server.'
            );

        } finally {

            if (save) {
                save.disabled = false;
                save.innerHTML = 'Simpan';
            }

        }

    }

    /*
    |--------------------------------------------------------------------------
    | Event: Drag marker (update lat/lng dari peta Home)
    |--------------------------------------------------------------------------
    */

    document.addEventListener(

        'gpstracker:home-preview-moved',

        async function (event) {

            const lat = event.detail.lat;
            const lng = event.detail.lng;

            if (latitude)  latitude.value  = lat;
            if (longitude) longitude.value = lng;
            if (latitudePreview)  latitudePreview.value  = Number(lat).toFixed(6);
            if (longitudePreview) longitudePreview.value = Number(lng).toFixed(6);

            try {

                await reverseGeocode(lat, lng);

            } catch (error) {

                console.error(error);

            }

        }

    );

    save?.addEventListener('click', saveHomeLocation);

});

</script>