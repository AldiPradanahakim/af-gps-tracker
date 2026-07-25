<script>

document.addEventListener('DOMContentLoaded', () => {

    const button = document.getElementById('homeLocationButton');

    const modal = document.getElementById('homeLocationModal');

    const close = document.getElementById('closeHomeLocation');

    const cancel = document.getElementById('cancelHomeLocation');

    const save = document.getElementById('saveHomeLocation');

    const device = document.getElementById('homeDevice');

    const search = document.getElementById('homeSearch');

    const result = document.getElementById('homeSearchResult');

    const loading = document.getElementById('homeSearchLoading');

    const coordinate = document.getElementById('homeCoordinate');

    const latitude = document.getElementById('homeLatitude');

    const longitude = document.getElementById('homeLongitude');

    const latitudePreview = document.getElementById('homeLatitudePreview');

    const longitudePreview = document.getElementById('homeLongitudePreview');

    const displayName = document.getElementById('homeDisplayName');

    let debounce = null;

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

        search.value = '';

        result.innerHTML = '';

        result.classList.add('hidden');

        coordinate.textContent = 'Belum memilih lokasi.';

        latitude.value = '';

        longitude.value = '';

        displayName.value = '';

        latitudePreview.value = '';

        longitudePreview.value = '';

    }

    button?.addEventListener('click', openModal);

    close?.addEventListener('click', closeModal);

    cancel?.addEventListener('click', closeModal);

    search?.addEventListener('input', () => {

        clearTimeout(debounce);

        const keyword = search.value.trim();

        if (keyword.length < 3) {

            result.innerHTML = '';

            result.classList.add('hidden');

            return;

        }

        debounce = setTimeout(() => {

            searchLocation(keyword);

        }, 500);

    });

    async function searchLocation(keyword) {

        loading.classList.remove('hidden');

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

            result.innerHTML = `
                <div class="p-4 text-sm text-red-600">
                    Gagal mencari lokasi.
                </div>
            `;

            result.classList.remove('hidden');

        } finally {

            loading.classList.add('hidden');

        }

    }

        function renderResult(items) {

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

        latitude.value = item.lat;

        longitude.value = item.lon;

        displayName.value = item.display_name;

        latitudePreview.value = Number(item.lat).toFixed(6);

        longitudePreview.value = Number(item.lon).toFixed(6);

        coordinate.textContent = item.display_name;

        result.classList.add('hidden');

        search.value = item.display_name;

        try {

            await reverseGeocode(
                item.lat,
                item.lon
            );

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
                'Home Location',
                'Geser marker pada peta jika ingin menyesuaikan posisi.'
            );

        }

    }

    async function reverseGeocode(lat, lon) {

        try {

            const response = await fetch(

                `/api/location/reverse?lat=${lat}&lon=${lon}`

            );

            if (!response.ok) {

                return;

            }

            const location = await response.json();

            if (
                location &&
                location.display_name
            ) {

                coordinate.textContent =
                    location.display_name;

                displayName.value =
                    location.display_name;

            }

        } catch (error) {

            console.error(error);

        }

    }

        async function saveHomeLocation() {

        if (
            latitude.value === '' ||
            longitude.value === ''
        ) {

            GPSTracker.showToast(
                'warning',
                'Peringatan',
                'Silakan pilih lokasi terlebih dahulu.'
            );

            return;

        }

        try {

            save.disabled = true;

            save.innerHTML = 'Menyimpan...';

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

                        device_id: device.value,

                        latitude: latitude.value,

                        longitude: longitude.value,

                        display_name: displayName.value

                    })
                }
            );

            const data = await response.json();

            if (!response.ok) {

                GPSTracker.showToast(
                    'error',
                    'Gagal',
                    data.message ??
                    'Home Location gagal disimpan.'
                );

                return;

            }

            GPSTracker.showToast(
                'success',
                'Berhasil',
                'Home Location berhasil disimpan.'
            );

            if (
                window.GPSTracker &&
                typeof GPSTracker.saveHomeLocationSuccess === 'function'
            ) {

                GPSTracker.saveHomeLocationSuccess(data);

            }

            closeModal();

        } catch (error) {

            console.error(error);

            GPSTracker.showToast(
                'error',
                'Error',
                'Terjadi kesalahan pada server.'
            );

        } finally {

            save.disabled = false;

            save.innerHTML = 'Simpan';

        }

    }

    document.addEventListener(

        'gpstracker:home-preview-moved',

        async function (event) {

            const lat = event.detail.lat;

            const lng = event.detail.lng;

            latitude.value = lat;

            longitude.value = lng;

            latitudePreview.value = Number(lat).toFixed(6);

            longitudePreview.value = Number(lng).toFixed(6);

            try {

                await reverseGeocode(lat, lng);

            } catch (error) {

                console.error(error);

            }

        }

    );

    save?.addEventListener(
        'click',
        saveHomeLocation
    );

});

</script>