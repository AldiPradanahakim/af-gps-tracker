<script>

window.VehicleGeofence = {

    /*
    |--------------------------------------------------------------------------
    | Properties
    |--------------------------------------------------------------------------
    */

    state: null,

    layers: {},

    drawing: null,

    /*
    |--------------------------------------------------------------------------
    | Initialize
    |--------------------------------------------------------------------------
    */

    init(state) {

        this.state = state;

        this.bindEvents();

        this.renderMap();

    },

    /*
    |--------------------------------------------------------------------------
    | Map Visibility (checkbox)
    |--------------------------------------------------------------------------
    */

    isTypeVisible(type) {

        const ids = {
            radius: 'toggleRadiusMap',
            administrative: 'toggleAdministrativeMap',
            custom: 'togglePolygonMap',
        };

        const checkbox = document.getElementById(ids[type]);

        return checkbox ? checkbox.checked : true;

    },

    /*
    |--------------------------------------------------------------------------
    | Render Map
    |--------------------------------------------------------------------------
    */

    clearLayers() {

        Object.keys(this.layers).forEach(id => {

            VehicleMap.removeOverlay(`geofence-${id}`);

        });

        this.layers = {};

    },

    renderMap() {

        this.clearLayers();

        (this.state.geofences ?? []).forEach(geofence => {

            this.renderOne(geofence);

        });

    },

    renderOne(geofence) {

        const layer = geofence.type === 'radius'
            ? this.buildRadiusLayer(geofence)
            : this.buildGeoJsonLayer(geofence);

        if (!layer) {
            return;
        }

        this.layers[geofence.id] = layer;

        if (this.isTypeVisible(geofence.type)) {

            VehicleMap.addOverlay(`geofence-${geofence.id}`, layer);

        }

    },

    buildRadiusLayer(geofence) {

        const center = geofence.config?.center;

        if (
            !center ||
            center.lat == null ||
            center.lng == null ||
            geofence.config.radius == null
        ) {
            return null;
        }

        const circle = L.circle(
            [Number(center.lat), Number(center.lng)],
            {
                radius: Number(geofence.config.radius),
                color: '#2563eb',
                weight: 2,
                fillColor: '#3b82f6',
                fillOpacity: 0.15,
            }
        );

        circle.bindPopup(
            `<strong>${geofence.name}</strong><br>Radius: ${Number(geofence.config.radius).toLocaleString()} m`
        );

        return circle;

    },

    buildGeoJsonLayer(geofence) {

        const geometry = geofence.config?.geometry;

        if (!geometry) {
            return null;
        }

        const isAdministrative = geofence.type === 'administrative';

        const layer = L.geoJSON(geometry, {
            style: {
                color: isAdministrative ? '#16a34a' : '#7c3aed',
                weight: 2,
                fillColor: isAdministrative ? '#22c55e' : '#8b5cf6',
                fillOpacity: 0.15,
            },
        });

        layer.bindPopup(`<strong>${geofence.name}</strong>`);

        return layer;

    },

    refreshLayerVisibility() {

        (this.state.geofences ?? []).forEach(geofence => {

            const layer = this.layers[geofence.id];

            if (!layer) {
                return;
            }

            if (this.isTypeVisible(geofence.type)) {
                VehicleMap.addOverlay(`geofence-${geofence.id}`, layer);
            } else {
                VehicleMap.removeOverlay(`geofence-${geofence.id}`);
            }

        });

    },

    /*
    |--------------------------------------------------------------------------
    | Bind Events
    |--------------------------------------------------------------------------
    */

    bindEvents() {

        this.bindCheckboxes();

        this.bindRadius();

        this.bindAdministrative();

        this.bindPolygon();

        this.bindDelete();

    },

    bindCheckboxes() {

        const all = document.getElementById('toggleAllGeofenceMap');
        const radius = document.getElementById('toggleRadiusMap');
        const administrative = document.getElementById('toggleAdministrativeMap');
        const polygon = document.getElementById('togglePolygonMap');

        const syncAll = () => {

            if (!all) return;

            all.checked =
                Boolean(radius?.checked) &&
                Boolean(administrative?.checked) &&
                Boolean(polygon?.checked);

        };

        [radius, administrative, polygon].forEach(checkbox => {

            checkbox?.addEventListener('change', () => {
                this.refreshLayerVisibility();
                syncAll();
            });

        });

        all?.addEventListener('change', () => {

            const checked = all.checked;

            if (radius) radius.checked = checked;
            if (administrative) administrative.checked = checked;
            if (polygon) polygon.checked = checked;

            this.refreshLayerVisibility();

        });

    },

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    showModal(id) {
        document.getElementById(id)?.classList.remove('hidden');
        document.getElementById(id)?.classList.add('flex');
    },

    hideModal(id) {
        document.getElementById(id)?.classList.add('hidden');
        document.getElementById(id)?.classList.remove('flex');
    },

    toast(type, title, message) {
        GPSTracker.showToast(type, title, message);
    },

    reloadAfter(delay = 800) {

        /*
        |--------------------------------------------------------------------------
        | Pastikan setelah reload halaman tetap membuka section Geofence,
        | bukan balik ke "Informasi Kendaraan" (default section).
        |--------------------------------------------------------------------------
        */

        history.replaceState(null, '', '#geofence');

        setTimeout(() => window.location.reload(), delay);
    },

    async submitCreate(payload, label) {

        try {

            const response = await VehicleApi.storeGeofence(payload);

            if (!response.success) {

                const message = response.errors
                    ? Object.values(response.errors).flat().join('\n')
                    : (response.message ?? 'Gagal menyimpan geofence.');

                this.toast('error', 'Gagal', message);

                return;

            }

            this.toast(
                'success',
                'Berhasil',
                response.message ?? `${label} geofence berhasil ditambahkan.`
            );

            this.reloadAfter();

        } catch (error) {

            console.error(error);

            this.toast('error', 'Error', 'Terjadi kesalahan pada server.');

        }

    },

    async submitUpdate(id, payload, label) {

        try {

            const response = await VehicleApi.updateGeofence(id, payload);

            if (!response.success) {

                const message = response.errors
                    ? Object.values(response.errors).flat().join('\n')
                    : (response.message ?? 'Gagal memperbarui geofence.');

                this.toast('error', 'Gagal', message);

                return;

            }

            this.toast(
                'success',
                'Berhasil',
                response.message ?? `${label} geofence berhasil diperbarui.`
            );

            this.reloadAfter();

        } catch (error) {

            console.error(error);

            this.toast('error', 'Error', 'Terjadi kesalahan pada server.');

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Radius
    |--------------------------------------------------------------------------
    */

    bindRadius() {

        document.getElementById('createRadiusButton')
            ?.addEventListener('click', () => this.showModal('createRadiusModal'));

        document.getElementById('closeCreateRadiusModal')
            ?.addEventListener('click', () => this.hideModal('createRadiusModal'));

        document.getElementById('cancelCreateRadius')
            ?.addEventListener('click', () => this.hideModal('createRadiusModal'));

        document.getElementById('createRadiusForm')
            ?.addEventListener('submit', async (event) => {

                event.preventDefault();

                const name = document.getElementById('createRadiusName').value.trim();

                if (!name) {
                    this.toast('error', 'Peringatan', 'Nama Radius wajib diisi.');
                    return;
                }

                await this.submitCreate({
                    device_id: String(this.state.device.id),
                    type: 'radius',
                    name,
                    radius_source: document.getElementById('createRadiusSource').value,
                    radius: document.getElementById('createRadiusValue').value,
                    radius_unit: 'meter',
                    status: document.getElementById('createRadiusStatus').value,
                }, 'Radius');

            });

        document.getElementById('editRadiusButton')
            ?.addEventListener('click', () => {
                document.getElementById('radiusReadContainer')?.classList.add('hidden');
                document.getElementById('radiusEditContainer')?.classList.remove('hidden');
            });

        document.getElementById('cancelRadiusEdit')
            ?.addEventListener('click', () => {
                document.getElementById('radiusEditContainer')?.classList.add('hidden');
                document.getElementById('radiusReadContainer')?.classList.remove('hidden');
            });

        document.getElementById('editRadiusForm')
            ?.addEventListener('submit', async (event) => {

                event.preventDefault();

                const id = document.getElementById('editRadiusId').value;

                await this.submitUpdate(id, {
                    name: document.getElementById('editRadiusName').value,
                    status: document.getElementById('editRadiusStatus').value,
                    radius: document.getElementById('editRadiusValue').value,
                }, 'Radius');

            });

        document.getElementById('changeRadiusCenter')
            ?.addEventListener('click', () => this.pickRadiusCenter());

    },

    pickRadiusCenter() {

        if (!window.VehicleMap?.map) {
            return;
        }

        const status = document.getElementById('radiusCenterStatus');

        if (status) {
            status.innerHTML = 'Klik pada peta untuk memilih titik baru...';
        }

        this.toast('info', 'Ubah Titik', 'Klik pada peta untuk memilih lokasi baru.');

        VehicleMap.map.once('click', async (event) => {

            const { lat, lng } = event.latlng;

            if (status) {
                status.innerHTML =
                    `Titik baru dipilih: <b>${lat.toFixed(6)}, ${lng.toFixed(6)}</b>. Menyimpan...`;
            }

            const id = document.getElementById('editRadiusId').value;

            await this.submitUpdate(id, {
                name: document.getElementById('editRadiusName').value,
                status: document.getElementById('editRadiusStatus').value,
                radius: document.getElementById('editRadiusValue').value,
                latitude: lat,
                longitude: lng,
            }, 'Radius');

        });

    },

    /*
    |--------------------------------------------------------------------------
    | Administrative
    |--------------------------------------------------------------------------
    */

    bindAdministrative() {

        document.getElementById('createAdministrativeButton')
            ?.addEventListener('click', () => this.showModal('createAdministrativeModal'));

        document.getElementById('closeCreateAdministrativeModal')
            ?.addEventListener('click', () => this.hideModal('createAdministrativeModal'));

        document.getElementById('cancelCreateAdministrative')
            ?.addEventListener('click', () => this.hideModal('createAdministrativeModal'));

        this.bindAdministrativeSearch(
            'createAdministrativeSearch',
            'createAdministrativeResult',
            (item, geometry) => {

                document.getElementById('createAdministrativeGeojson').value = JSON.stringify(geometry);
                document.getElementById('createAdministrativeDisplayName').value = item.name;
                document.getElementById('createAdministrativeAreaType').value = item.level;

                const selected = document.getElementById('createAdministrativeSelected');

                if (selected) {
                    selected.textContent = `Terpilih: ${item.name} (${item.type})`;
                    selected.classList.remove('hidden');
                }

            }
        );

        document.getElementById('createAdministrativeForm')
            ?.addEventListener('submit', async (event) => {

                event.preventDefault();

                const name = document.getElementById('createAdministrativeName').value.trim();

                const geojson = document.getElementById('createAdministrativeGeojson').value;

                if (!name) {
                    this.toast('error', 'Peringatan', 'Nama geofence wajib diisi.');
                    return;
                }

                if (!geojson) {
                    this.toast('error', 'Peringatan', 'Silakan pilih wilayah terlebih dahulu.');
                    return;
                }

                await this.submitCreate({
                    device_id: String(this.state.device.id),
                    type: 'administrative',
                    name,
                    status: document.getElementById('createAdministrativeStatus').value,
                    geojson,
                    display_name: document.getElementById('createAdministrativeDisplayName').value,
                    administrative_type: document.getElementById('createAdministrativeAreaType').value,
                }, 'Administrative');

            });

        document.getElementById('editAdministrativeButton')
            ?.addEventListener('click', () => {
                document.getElementById('administrativeReadContainer')?.classList.add('hidden');
                document.getElementById('administrativeEditContainer')?.classList.remove('hidden');
            });

        document.getElementById('cancelAdministrativeEdit')
            ?.addEventListener('click', () => {
                document.getElementById('administrativeEditContainer')?.classList.add('hidden');
                document.getElementById('administrativeReadContainer')?.classList.remove('hidden');
            });

        this.bindAdministrativeSearch(
            'editAdministrativeSearch',
            'editAdministrativeResult',
            (item, geometry) => {

                document.getElementById('editAdministrativeGeojson').value = JSON.stringify(geometry);
                document.getElementById('editAdministrativeDisplayName').value = item.name;
                document.getElementById('editAdministrativeAreaType').value = item.level;

            }
        );

        document.getElementById('editAdministrativeForm')
            ?.addEventListener('submit', async (event) => {

                event.preventDefault();

                const id = document.getElementById('editAdministrativeId').value;

                const payload = {
                    name: document.getElementById('editAdministrativeName').value,
                    status: document.getElementById('editAdministrativeStatus').value,
                };

                const geojson = document.getElementById('editAdministrativeGeojson').value;

                if (geojson) {
                    payload.geojson = geojson;
                    payload.display_name = document.getElementById('editAdministrativeDisplayName').value;
                    payload.administrative_type = document.getElementById('editAdministrativeAreaType').value;
                }

                await this.submitUpdate(id, payload, 'Administrative');

            });

    },

    bindAdministrativeSearch(inputId, resultId, onSelect) {

        const input = document.getElementById(inputId);
        const result = document.getElementById(resultId);

        if (!input || !result) {
            return;
        }

        let debounce = null;

        let cachedDistricts = null;

        input.addEventListener('input', () => {

            clearTimeout(debounce);

            const keyword = input.value.trim();

            if (keyword.length < 3) {
                result.innerHTML = '';
                result.classList.add('hidden');
                return;
            }

            debounce = setTimeout(async () => {

                try {

                    if (!cachedDistricts) {

                        const response = await VehicleApi.administrativeDistricts();

                        cachedDistricts = response.data ?? [];

                    }

                    const lower = keyword.toLowerCase();

                    const matches = [];

                    cachedDistricts.forEach(district => {

                        if (district.name.toLowerCase().includes(lower)) {

                            matches.push({
                                level: 'district',
                                code: district.code,
                                name: district.name,
                                type: 'Kecamatan',
                            });

                        }

                        (district.villages ?? []).forEach(village => {

                            if (village.name.toLowerCase().includes(lower)) {

                                matches.push({
                                    level: 'village',
                                    code: village.code,
                                    name: village.name,
                                    type: 'Kelurahan',
                                    district_name: district.name,
                                });

                            }

                        });

                    });

                    this.renderAdministrativeResult(
                        result,
                        matches,
                        item => this.selectAdministrativeItem(item, input, result, onSelect)
                    );

                } catch (error) {

                    console.error(error);

                }

            }, 400);

        });

    },

    renderAdministrativeResult(container, items, onClick) {

        container.innerHTML = '';

        if (!items.length) {

            container.innerHTML =
                '<div class="p-4 text-[13px] text-slate-500">Wilayah tidak ditemukan.</div>';

            container.classList.remove('hidden');

            return;

        }

        items.forEach(item => {

            const button = document.createElement('button');

            button.type = 'button';

            button.className =
                'block w-full border-b border-slate-100 px-4 py-3 text-left text-[13px] hover:bg-slate-50 last:border-0';

            button.innerHTML = `
                <div class="font-semibold text-slate-800">${item.name}</div>
                <div class="mt-1 text-[11px] text-slate-500">${item.type}${item.district_name ? ' &middot; ' + item.district_name : ''}</div>
            `;

            button.addEventListener('click', () => onClick(item));

            container.appendChild(button);

        });

        container.classList.remove('hidden');

    },

    async selectAdministrativeItem(item, input, result, onSelect) {

        try {

            const response = await VehicleApi.administrativeGeoJson(item.level, item.code);

            const geometry = response.data;

            input.value = item.name;

            result.classList.add('hidden');

            onSelect(item, geometry);

            this.toast('success', 'Wilayah Terpilih', item.name);

        } catch (error) {

            console.error(error);

            this.toast('error', 'Gagal', 'Gagal mengambil data wilayah.');

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Polygon
    |--------------------------------------------------------------------------
    */

    bindPolygon() {

        document.getElementById('createPolygonButton')
            ?.addEventListener('click', () => this.showModal('createPolygonModal'));

        document.getElementById('closeCreatePolygonModal')
            ?.addEventListener('click', () => this.hideModal('createPolygonModal'));

        document.getElementById('cancelCreatePolygon')
            ?.addEventListener('click', () => this.hideModal('createPolygonModal'));

        document.getElementById('createPolygonForm')
            ?.addEventListener('submit', (event) => {

                event.preventDefault();

                const name = document.getElementById('createPolygonName').value.trim();

                if (!name) {
                    this.toast('error', 'Peringatan', 'Nama Polygon wajib diisi.');
                    return;
                }

                const status = document.getElementById('createPolygonStatus').value;

                this.hideModal('createPolygonModal');

                this.toast(
                    'info',
                    'Gambar Polygon',
                    'Klik pada peta untuk membuat titik, lalu double klik untuk menyelesaikan.'
                );

                this.startPolygonDrawing(async (geojson) => {

                    await this.submitCreate({
                        device_id: String(this.state.device.id),
                        type: 'custom',
                        name,
                        status,
                        geojson: JSON.stringify(geojson),
                    }, 'Polygon');

                });

            });

        document.getElementById('editPolygonButton')
            ?.addEventListener('click', () => {
                document.getElementById('polygonReadContainer')?.classList.add('hidden');
                document.getElementById('polygonEditContainer')?.classList.remove('hidden');
            });

        document.getElementById('cancelPolygonEdit')
            ?.addEventListener('click', () => {
                document.getElementById('polygonEditContainer')?.classList.add('hidden');
                document.getElementById('polygonReadContainer')?.classList.remove('hidden');
            });

        document.getElementById('editPolygonForm')
            ?.addEventListener('submit', async (event) => {

                event.preventDefault();

                const id = document.getElementById('editPolygonId').value;

                await this.submitUpdate(id, {
                    name: document.getElementById('editPolygonName').value,
                    status: document.getElementById('editPolygonStatus').value,
                }, 'Polygon');

            });

        document.getElementById('changePolygonArea')
            ?.addEventListener('click', () => {

                const id = document.getElementById('editPolygonId').value;

                const name = document.getElementById('editPolygonName').value;

                const status = document.getElementById('editPolygonStatus').value;

                this.toast(
                    'info',
                    'Gambar Ulang',
                    'Klik pada peta untuk membuat titik baru, lalu double klik untuk menyelesaikan.'
                );

                this.startPolygonDrawing(async (geojson) => {

                    await this.submitUpdate(id, {
                        name,
                        status,
                        geojson: JSON.stringify(geojson),
                    }, 'Polygon');

                });

            });

    },

    startPolygonDrawing(onFinish) {

        if (!window.VehicleMap?.map) {
            return;
        }

        this.cancelPolygonDrawing();

        const map = VehicleMap.map;

        const points = [];

        const markers = [];

        let polygon = null;

        map.doubleClickZoom.disable();

        const redraw = () => {

            if (polygon) {
                map.removeLayer(polygon);
                polygon = null;
            }

            if (points.length >= 2) {

                polygon = L.polygon(points, {
                    color: '#7c3aed',
                    weight: 2,
                    fillColor: '#8b5cf6',
                    fillOpacity: 0.15,
                    dashArray: '6',
                });

                polygon.addTo(map);

            }

        };

        const onClick = (event) => {

            const point = event.latlng;

            points.push(point);

            const marker = L.circleMarker(point, {
                radius: 8,
                color: '#ffffff',
                weight: 3,
                fillColor: '#7c3aed',
                fillOpacity: 1,
            }).addTo(map);

            markers.push(marker);

            redraw();

        };

        const onDblClick = (event) => {

            L.DomEvent.stop(event);

            if (points.length < 3) {

                this.toast('error', 'Polygon Kurang Titik', 'Minimal 3 titik untuk membuat polygon.');

                return;

            }

            const geojson = polygon.toGeoJSON().geometry;

            this.cancelPolygonDrawing();

            onFinish(geojson);

        };

        map.on('click', onClick);

        map.on('dblclick', onDblClick);

        this.drawing = {

            map,

            onClick,

            onDblClick,

            getMarkers: () => markers,

            getPolygon: () => polygon,

        };

    },

    cancelPolygonDrawing() {

        if (!this.drawing) {
            return;
        }

        const { map, onClick, onDblClick } = this.drawing;

        map.off('click', onClick);

        map.off('dblclick', onDblClick);

        map.doubleClickZoom.enable();

        (this.drawing.getMarkers?.() ?? []).forEach(marker => map.removeLayer(marker));

        const polygon = this.drawing.getPolygon?.();

        if (polygon) {
            map.removeLayer(polygon);
        }

        this.drawing = null;

    },

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    bindDelete() {

        const buttons = [
            document.getElementById('deleteRadiusButton'),
            document.getElementById('deleteAdministrativeButton'),
            document.getElementById('deletePolygonButton'),
        ];

        const typeLabels = {

            deleteRadiusButton: 'Radius',

            deleteAdministrativeButton: 'Administrative',

            deletePolygonButton: 'Polygon',

        };

        buttons.forEach(button => {

            if (!button) return;

            button.addEventListener('click', () => {

                document.getElementById('deleteGeofenceId').value = button.dataset.id;

                document.getElementById('deleteGeofenceType').value = typeLabels[button.id];

                document.getElementById('deleteGeofenceTypeText').textContent = typeLabels[button.id];

                document.getElementById('deleteGeofenceName').textContent = button.dataset.name || '-';

                this.showModal('deleteGeofenceModal');

            });

        });

        document.getElementById('cancelDeleteGeofence')
            ?.addEventListener('click', () => this.hideModal('deleteGeofenceModal'));

        document.getElementById('confirmDeleteGeofence')
            ?.addEventListener('click', async () => {

                const id = document.getElementById('deleteGeofenceId').value;

                if (!id) {
                    return;
                }

                const button = document.getElementById('confirmDeleteGeofence');

                try {

                    button.disabled = true;

                    const response = await VehicleApi.deleteGeofence(id);

                    if (!response.success) {
                        this.toast('error', 'Gagal', response.message ?? 'Gagal menghapus geofence.');
                        return;
                    }

                    this.toast('success', 'Berhasil', response.message ?? 'Geofence berhasil dihapus.');

                    this.reloadAfter();

                } catch (error) {

                    console.error(error);

                    this.toast('error', 'Error', 'Terjadi kesalahan pada server.');

                } finally {

                    button.disabled = false;

                }

            });

    },

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    destroy() {

        this.cancelPolygonDrawing();

        this.clearLayers();

        this.state = null;

    },

};

</script>
